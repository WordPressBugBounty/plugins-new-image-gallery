<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * @package Image Gallery
 */
/*
Plugin Name: Image Gallery
Plugin URI: http://awplife.com/
Description: A Responsive Simple Beautiful Easy Powerful WordPress Gallery Plugin With Masonry Layout.
Version: 2.0.3
Author: A WP Life
Author URI: http://awplife.com/
License: GPLv2 or later
Text Domain: new-image-gallery
Domain Path: /languages
 */
require_once(plugin_dir_path(__FILE__) . 'include/frontend-functions.php');
require_once(plugin_dir_path(__FILE__) . 'include/shortcode.php');

/**
 * Helper to safely unserialize data, blocking PHP Object Injection
 */
if (!function_exists('awl_ig_safe_unserialize')) {
	function awl_ig_safe_unserialize($data)
	{
		// Prevent deserialization of objects by checking for O: or C: tags
		if (is_string($data) && preg_match('/(^|;)O:\d+:/', $data)) {
			return false; // Possible Object Injection payload
		}
		// Avoid unserializing if it's already an array
		if (is_array($data)) {
			return $data;
		}
		return @unserialize($data);
	}
}

if (!function_exists('ig_prefix_col_class')) {
	function ig_prefix_col_class($class) {
		if (empty($class)) return '';
		if (strpos($class, 'ig-') === 0) return $class;
		return 'ig-' . $class;
	}
}

/**
 * Normalizes column settings (Bootstrap classes or integers) to a clean numeric column count.
 * Ensures backward compatibility with existing gallery data.
 */
if (!function_exists('ig_get_column_count')) {
	function ig_get_column_count($value, $default = 4) {
		if (empty($value)) return $default;
		if (is_numeric($value)) return (int) $value;
		
		// Map Bootstrap-style classes (col-lg-4, col-md-6, col-6, etc.)
		if (preg_match('/(?:col-\w+-|col-)(\d+)/', $value, $matches)) {
			$span = (int) $matches[1];
			if ($span > 0) {
				return max(1, floor(12 / $span));
			}
		}
		return $default;
	}
}

if (!class_exists('New_Image_Gallery')) {


	class New_Image_Gallery
	{



		public function __construct()
		{
			$this->_constants();
			$this->_hooks();
		}

		protected function _constants()
		{
			//Plugin Version
			define('IG_PLUGIN_VER', '2.0.3');

			//Plugin Slug
			define('IG_PLUGIN_SLUG', 'image_gallery');

			//Plugin Directory Path
			define('IG_PLUGIN_DIR', plugin_dir_path(__FILE__));

			//Plugin Directory URL
			define('IG_PLUGIN_URL', plugin_dir_url(__FILE__));

		} // end of constructor function

		/**
		 * Setup the default filters and actions
		 * @uses      add_action()  To add various actions
		 * @access    private
		 * @return    void
		 */
		protected function _hooks()
		{

			//add gallery menu item, change menu filter for multisite
			add_action('admin_menu', array($this, '_srgallery_menu'), 101);

			//Create Image Gallery Custom Post
			add_action('init', array($this, '_New_Image_Gallery'));

			//Add meta box to custom post
			add_action('add_meta_boxes', array($this, '_admin_add_meta_box'));

			add_action('admin_enqueue_scripts', array($this, '_ig_admin_enqueue_scripts'));

			add_action('wp_ajax_image_gallery_js', array(&$this, '_ajax_image_gallery'));

			add_action('save_post', array(&$this, '_ig_save_settings'));

			// add nig cpt shortcode column - manage_{$post_type}_posts_columns
			add_filter('manage_image_gallery_posts_columns', array($this, 'set_ig_shortcode_column_name'));

			// add nig cpt shortcode column data - manage_{$post_type}_posts_custom_column
			add_action('manage_image_gallery_posts_custom_column', array($this, 'custom_ig_shodrcode_data'), 10, 2);

			add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts_in_header'));

		}// end of hook function

		// end of hook function
		public function enqueue_scripts_in_header()
		{
			wp_enqueue_script('jquery');
		}

		// ig slider cpt shortcode column before date columns
		public function set_ig_shortcode_column_name($defaults)
		{
			$new = array();
			unset($defaults['tags']);   // remove it from the columns list

			foreach ($defaults as $key => $value) {
				if ($key == 'date') {  // when we find the date column
					$new['ig_shortcode'] = esc_html__('Shortcode', 'new-image-gallery');  // put the tags column before it
				}
				$new[$key] = $value;
			}
			return $new;
		}

		// ig cpt shortcode column data
		public function custom_ig_shodrcode_data($column, $post_id)
		{
			switch ($column) {
				case 'ig_shortcode':
					printf(
						"<input type='text' class='button button-primary' id='ig-shortcode-%d' value='[IMG-Gal id=%d]' style='font-weight:bold; background-color:#32373C; color:#FFFFFF;' />",
						esc_attr($post_id),
						esc_attr($post_id)
					);
					printf(
						"<input type='button' class='button button-primary' onclick='return IGCopyShortcode%d();' readonly value='%s' style='margin-left:4px;' />",
						esc_attr($post_id),
						esc_attr__('Copy', 'new-image-gallery')
					);
					printf(
						"<span id='copy-msg-%d' class='button button-primary' style='display:none; background-color:#32CD32; color:#FFFFFF; margin-left:4px; border-radius: 4px;'>%s</span>",
						esc_attr($post_id),
						esc_html__('copied', 'new-image-gallery')
					);
					?>
					<script>
						function IGCopyShortcode<?php echo esc_js($post_id); ?>() {
							var copyText = document.getElementById('ig-shortcode-<?php echo esc_js($post_id); ?>');
							copyText.select();
							document.execCommand('copy');
							
							//fade in and out copied message
							jQuery('#copy-msg-<?php echo esc_js($post_id); ?>').fadeIn('1000', 'linear');
							jQuery('#copy-msg-<?php echo esc_js($post_id); ?>').fadeOut(2500,'swing');
						}
					</script>
					<?php
					break;
			}
		}

		/**
		 * Enqueue Admin Scripts and Styles
		 */
		public function _ig_admin_enqueue_scripts($hook) {
			$screen = get_current_screen();
			if ($screen && $screen->post_type === 'image_gallery') {
				wp_enqueue_style('ig-admin-style-css', IG_PLUGIN_URL . 'assets/css/ig-admin-style.css', array(), IG_PLUGIN_VER);
				
				// Typography for settings
				wp_enqueue_style('ig-google-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap', array(), null);
			}
			
			// Specific docs styling if active
			if (isset($_GET['page']) && $_GET['page'] === 'sr-doc-page') {
				wp_enqueue_style('ig-admin-docs-css', IG_PLUGIN_URL . 'assets/css/ig-docs.css', array(), IG_PLUGIN_VER);
			}

			// Specific our plugins/themes styling if active
			if (isset($_GET['page']) && ($_GET['page'] === 'ig-our-plugins' || $_GET['page'] === 'ig-our-themes')) {
				wp_enqueue_style('thickbox');
				wp_enqueue_script('thickbox');
				wp_enqueue_style('ig-our-plugins-css', IG_PLUGIN_URL . 'assets/css/our-plugins-style.css', array(), IG_PLUGIN_VER);
				wp_enqueue_style('ig-google-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap', array(), null);
			}
		}

		/**
		 * Adds the Gallery menu item
		 * @access    private
		 * @since     3.0
		 * @return    void
		 */
		public function _srgallery_menu()
		{
			add_submenu_page('edit.php?post_type=' . IG_PLUGIN_SLUG, __('Docs', 'new-image-gallery'), __('Docs', 'new-image-gallery'), 'administrator', 'sr-doc-page', array($this, '_ig_doc_page'));
			add_submenu_page('edit.php?post_type=' . IG_PLUGIN_SLUG, __('Our Plugins', 'new-image-gallery'), __('Our Plugins', 'new-image-gallery'), 'administrator', 'ig-our-plugins', array($this, '_ig_our_plugins_page'));
			add_submenu_page('edit.php?post_type=' . IG_PLUGIN_SLUG, __('Our Themes', 'new-image-gallery'), __('Our Themes', 'new-image-gallery'), 'administrator', 'ig-our-themes', array($this, '_ig_our_themes_page'));
		}


		/**
		 * Image Gallery Custom Post
		 * Create gallery post type in admin dashboard.
		 * @access    private
		 * @since     3.0
		 * @return    void      Return custom post type.
		 */
		public function _New_Image_Gallery()
		{
			$labels = array(
				'name' => _x('Image Gallery', 'Post Type General Name', 'new-image-gallery'),
				'singular_name' => _x('Image Gallery', 'Post Type Singular Name', 'new-image-gallery'),
				'menu_name' => __('Image Gallery', 'new-image-gallery'),
				'name_admin_bar' => __('Image Gallery', 'new-image-gallery'),
				'parent_item_colon' => __('Parent Item:', 'new-image-gallery'),
				'all_items' => __('All Image Gallery', 'new-image-gallery'),
				'add_new_item' => __('Add New Image Gallery', 'new-image-gallery'),
				'add_new' => __('Add Image Gallery', 'new-image-gallery'),
				'new_item' => __('New Image Gallery', 'new-image-gallery'),
				'edit_item' => __('Edit Image Gallery', 'new-image-gallery'),
				'update_item' => __('Update Image Gallery', 'new-image-gallery'),
				'search_items' => __('Search Image Gallery', 'new-image-gallery'),
				'not_found' => __('Image Gallery Not found', 'new-image-gallery'),
				'not_found_in_trash' => __('Image Gallery Not found in Trash', 'new-image-gallery'),
			);
			$args = array(
				'label' => __('Image Gallery', 'new-image-gallery'),
				'description' => __('Custom Post Type For Image Gallery', 'new-image-gallery'),
				'labels' => $labels,
				'supports' => array('title'),
				'taxonomies' => array(),
				'hierarchical' => false,
				'public' => false,
				'show_ui' => true,
				'show_in_menu' => true,
				'menu_position' => 65,
				'menu_icon' => 'dashicons-images-alt2',
				'show_in_admin_bar' => false,
				'show_in_nav_menus' => false,
				'can_export' => true,
				'has_archive' => false,
				'exclude_from_search' => true,
				'publicly_queryable' => false,
				'capability_type' => 'page',
				'show_in_rest' => true,
			);
			register_post_type('image_gallery', $args);
		} // end of post type function

		/**
		 * Adds Meta Boxes
		 * @access    private
		 * @return    void
		 */
		public function _admin_add_meta_box()
		{
			remove_meta_box('postcustom', 'image_gallery', 'normal');
			// Syntax: add_meta_box( $id, $title, $callback, $screen, $context, $priority, $callback_args );
			add_meta_box('1', __('Copy Image Gallery Shortcode', 'new-image-gallery'), array(&$this, '_ig_shortcode_left_metabox'), 'image_gallery', 'side', 'default');
			add_meta_box('2', __('Add Image', 'new-image-gallery'), array(&$this, 'ig_upload_multiple_images'), 'image_gallery', 'normal', 'high');
		}

		// image gallery copy shortcode meta box under publish button
		public function _ig_shortcode_left_metabox($post)
		{ ?>
			<p class="input-text-wrap" style="position: relative; display: inline-block; width: 100%;">
				<input type="text" name="IGCopyShortcode" id="IGCopyShortcode"
					value="[IMG-Gal id='<?php echo esc_attr($post->ID); ?>']" readonly
					class="ig-shortcode-input">
				<span class="igm-copy dashicons dashicons-clipboard" data-target="#IGCopyShortcode" title="<?php esc_attr_e('Copy Shortcode', 'new-image-gallery'); ?>"></span>
			</p>
			
			<p id="igm-copy-code" style="margin-top: 5px; color: #10b981; font-weight: 500; font-size: 13px; text-align: center; display: none;">
				<span class="dashicons dashicons-yes-alt" style="font-size: 18px; margin-top: -2px;"></span> <?php esc_html_e('Shortcode copied!', 'new-image-gallery'); ?>
			</p>
			
			<p style="margin-top: 10px; font-size: 12px; color: #64748b; line-height: 1.4;">
				<?php esc_html_e('Copy & Embed shortcode into any Page/Post/Text Widget to display gallery.', 'new-image-gallery'); ?>
			</p>
		<?php
		}

		// add new image metabox
		public function ig_upload_multiple_images($post)
		{
			//wp_enqueue_script('jquery');
			wp_enqueue_script('awl-ig-uploader.js', IG_PLUGIN_URL . 'assets/js/awl-ig-uploader.js', array('jquery'), IG_PLUGIN_VER, true);
			wp_localize_script('awl-ig-uploader.js', 'igp_uploader_vars', array(
				'media_title' => __('Add Images to Gallery', 'new-image-gallery'),
				'button_text' => __('Add to Gallery', 'new-image-gallery'),
				'confirm_delete' => __('Are you sure you want to delete this image?', 'new-image-gallery'),
				'confirm_delete_all' => __('Are you sure you want to delete all images?', 'new-image-gallery'),
			));
			wp_enqueue_media();

		?>
		<?php
			require_once('include/gallery-settings.php');
		} // end of upload multiple image

		public function _ig_ajax_callback_function($id)
		{
			//thumb, thumbnail, medium, large, post-thumbnail
			$thumbnail = wp_get_attachment_image_src($id, 'medium', true);
			$attachment = get_post($id); // $id = attachment id
			$image_alt = get_post_meta($id, '_wp_attachment_image_alt', true);
			if (empty($image_alt)) {
				$image_alt = get_the_title($id);
			}
		?>
			<li class="ig-image-slide" id="<?php echo esc_attr($id); ?>" data-position="<?php echo esc_attr($id); ?>">
				<div class="ig-image-preview">
					<div class="ig-image-controls">
						<div class="ig-move-handle" title="<?php esc_attr_e('Drag to reorder', 'new-image-gallery'); ?>"><span class="dashicons dashicons-move"></span></div>
						<a class="pw-trash-icon remove-slide" name="remove-slide" href="#" title="<?php esc_attr_e('Delete image', 'new-image-gallery'); ?>"><span class="dashicons dashicons-trash"></span></a>
					</div>
					<img src="<?php echo esc_url($thumbnail[0]); ?>" alt="<?php echo esc_html(get_the_title($id)); ?>">
				</div>
				<div class="ig-image-info">
					<input type="hidden" name="slide-ids[]" value="<?php echo esc_attr($id); ?>" />
					<input type="text" name="slide-title[]" placeholder="<?php esc_html_e('Title', 'new-image-gallery'); ?>" value="<?php echo esc_attr(get_the_title($id)); ?>">
					<input type="text" name="slide-alt[]" placeholder="<?php esc_html_e('Alt Text', 'new-image-gallery'); ?>" value="<?php echo esc_attr($image_alt); ?>">

				</div>
			</li>
<?php
		}

		public function _ajax_image_gallery()
		{
			if (!current_user_can('manage_options')) {
				wp_die(-1);
			}
			
			if (!isset($_POST['igp_add_images_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['igp_add_images_nonce'])), 'igp_add_images')) {
				wp_die(esc_html__('Sorry, your nonce did not verify.', 'new-image-gallery'));
			}
			
			$slide_ids_raw = isset($_POST['slideId']) ? wp_unslash($_POST['slideId']) : array();
			$slide_ids     = is_array($slide_ids_raw) ? array_map('absint', $slide_ids_raw) : array(absint($slide_ids_raw));

			foreach ($slide_ids as $id) {
				$this->_ig_ajax_callback_function($id);
			}
			wp_die();
		}

		public function _ig_save_settings($post_id)
		{
			// Check if it's an autosave
			if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
				return;
			}

			// Check post type
			if ('image_gallery' !== get_post_type($post_id)) {
				return;
			}

			if (current_user_can('edit_post', $post_id)) {

				if (isset($_POST['igp_save_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['igp_save_nonce'])), 'ig_save_settings')) {

						$gal_thumb_size      = isset($_POST['gal_thumb_size']) ? sanitize_text_field(wp_unslash($_POST['gal_thumb_size'])) : "thumbnail";
						$col_large_desktops  = isset($_POST['col_large_desktops']) ? sanitize_text_field(wp_unslash($_POST['col_large_desktops'])) : "4";
						$col_desktops        = isset($_POST['col_desktops']) ? sanitize_text_field(wp_unslash($_POST['col_desktops'])) : "3";
						$col_tablets         = isset($_POST['col_tablets']) ? sanitize_text_field(wp_unslash($_POST['col_tablets'])) : "2";
						$col_phones          = isset($_POST['col_phones']) ? sanitize_text_field(wp_unslash($_POST['col_phones'])) : "1";
						$img_title           = isset($_POST['img_title']) ? sanitize_text_field(wp_unslash($_POST['img_title'])) : "1";
						$no_spacing         = isset($_POST['no_spacing']) ? sanitize_text_field(wp_unslash($_POST['no_spacing'])) : "0";
						$thumbnail_order     = isset($_POST['thumbnail_order']) ? sanitize_text_field(wp_unslash($_POST['thumbnail_order'])) : "ASC";
						$image_hover_effect_type = isset($_POST['image_hover_effect_type']) ? sanitize_text_field(wp_unslash($_POST['image_hover_effect_type'])) : "sg";
						$image_hover_effect_four = isset($_POST['image_hover_effect_four']) ? sanitize_text_field(wp_unslash($_POST['image_hover_effect_four'])) : "hvr-grow-shadow";
						$light_box           = isset($_POST['light-box']) ? sanitize_text_field(wp_unslash($_POST['light-box'])) : "1";
						$show_lightbox_loop = isset($_POST['show_lightbox_loop']) ? absint($_POST['show_lightbox_loop']) : 1;



						$image_ids      = array();
						$image_titles   = array();
						$image_alt      = array();

						$image_ids_raw    = (isset($_POST['slide-ids']) && is_array($_POST['slide-ids'])) ? array_map('absint', wp_unslash($_POST['slide-ids'])) : array();
						$image_titles_raw = (isset($_POST['slide-title']) && is_array($_POST['slide-title'])) ? array_map('sanitize_text_field', wp_unslash($_POST['slide-title'])) : array();
						$image_alt_raw    = (isset($_POST['slide-alt']) && is_array($_POST['slide-alt'])) ? array_map('sanitize_text_field', wp_unslash($_POST['slide-alt'])) : array();


						foreach ($image_ids_raw as $index => $image_id) {
							$image_id = absint($image_id);
							$image_ids[]     = $image_id;
							$image_titles[]  = isset($image_titles_raw[$index]) ? sanitize_text_field($image_titles_raw[$index]) : "";
							$image_alt[]     = isset($image_alt_raw[$index])    ? sanitize_text_field($image_alt_raw[$index])    : "";

							$single_image_update = array(
								'ID'           => $image_id,
								'post_title'   => end($image_titles),
							);

							// Avoid infinite loop during save
							remove_action('save_post', array(&$this, '_ig_save_settings'));
							wp_update_post($single_image_update);
							add_action('save_post', array(&$this, '_ig_save_settings'));

						}

						$gallery_settings = array(
							'slide-ids' 			=> $image_ids,
							'slide-title' 			=> $image_titles,
							'slide-alt' => $image_alt,
							'gal_thumb_size' => $gal_thumb_size,
							'no_spacing' => $no_spacing,
							'img_title' => $img_title,
							'thumbnail_order' => $thumbnail_order,
							'image_hover_effect_type' => $image_hover_effect_type,
							'image_hover_effect_four' => $image_hover_effect_four,
							'light-box' => $light_box,
							'show_lightbox_loop' => $show_lightbox_loop,
							'col_large_desktops' => $col_large_desktops,
							'col_desktops' => $col_desktops,
							'col_tablets' => $col_tablets,
							'col_phones' => $col_phones,
						);
						$awl_image_gallery_shortcode_setting = 'awl_ig_settings_' . $post_id;
						update_post_meta($post_id, $awl_image_gallery_shortcode_setting, json_encode($gallery_settings));
					}
				}
			}

		/**
		 * Image Gallery Docs Page
		 * Create doc page to help user to setup plugin
		 * @access    private
		 * @since     3.0
		 * @return    void.
		 */
		public function _ig_doc_page()
		{
			require_once('include/docs.php');
		}

		/**
		 * Image Gallery Our Plugins Page
		 * Fetches and displays plugins from WordPress.org author profile
		 */
		public function _ig_our_plugins_page()
		{
			require_once('include/our-plugins.php');
		}

		/**
		 * Image Gallery Our Themes Page
		 * Fetches and displays themes from WordPress.org author profile
		 */
		public function _ig_our_themes_page()
		{
			require_once('include/our-themes.php');
		}

	} // end of class

	// register sf scripts
	function awplife_igp_register_scripts()
	{
		// css & JS
		wp_register_script('awl-imagesloaded-pkgd-js', plugin_dir_url(__FILE__) . 'assets/js/imagesloaded.pkgd.js', array('jquery'), IG_PLUGIN_VER, true);
		wp_register_script('awl-ig-isotope-js', plugin_dir_url(__FILE__) . 'assets/js/isotope.pkgd.min.js', array('jquery'), IG_PLUGIN_VER, true);
		wp_register_style('awl-ig-frontend-grid-css', plugin_dir_url(__FILE__) . 'assets/css/ig-frontend-grid.css', array(), IG_PLUGIN_VER);
		// css & JS

		// Hash Guard for URL conflicts
		wp_register_script('awl-ig-hash-guard-js', IG_PLUGIN_URL . 'assets/js/ig-hash-guard.js', array(), IG_PLUGIN_VER, false); // Load in head

		wp_register_style('awl-ld-lightbox-css', plugin_dir_url(__FILE__) . 'include/lightbox/ld-lightbox/css/lightbox.css', array(), IG_PLUGIN_VER);
		wp_register_script('awl-ld-lightbox-js', plugin_dir_url(__FILE__) . 'include/lightbox/ld-lightbox/js/lightbox.js', array('jquery'), IG_PLUGIN_VER, true);

		// Modernization Assets
		wp_register_script('awl-ig-admin-js', IG_PLUGIN_URL . 'assets/js/ig-admin.js', array('jquery'), IG_PLUGIN_VER, true);
		wp_register_script('awl-ig-frontend-js', IG_PLUGIN_URL . 'assets/js/ig-frontend.js', array('jquery', 'awl-imagesloaded-pkgd-js', 'awl-ig-isotope-js'), IG_PLUGIN_VER, true);
		wp_register_style('awl-ig-docs-css', IG_PLUGIN_URL . 'assets/css/ig-docs.css', array(), IG_PLUGIN_VER);


	}

	add_action('wp_enqueue_scripts', 'awplife_igp_register_scripts');
	add_action('admin_enqueue_scripts', 'awplife_igp_register_scripts');

	/**

	 * Instantiates the Class
	 * @since     3.0
	 * @global    object	$ig_gallery_object
	 */
	$ig_gallery_object = new New_Image_Gallery();
} // end of class exists

?>