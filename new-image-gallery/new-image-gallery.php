<?php
/**
@package New Image Gallery
Plugin Name: New Image Gallery
Plugin URI:  http://awplife.com/
Description: The best image gallery plugin with responsive design multiple columns lightbox preview.
Version:     1.5.5
Author:      A WP Life
Author URI:  https://awplife.com/
Text Domain: new-image-gallery
Domain Path: /languages
License:     GPL2

New Image Gallery is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

New Image Gallery is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with New Image Gallery. If not, see https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html.
 */

if (!class_exists('New_Image_Gallery')) {

	class New_Image_Gallery
	{

		protected $protected_plugin_api;
		protected $ajax_plugin_nonce;

		public function __construct()
		{
			$this->_constants();
			$this->_hooks();
		}

		protected function _constants()
		{
			// Plugin Version
			define('IG_PLUGIN_VER', '1.5.5');

			// Plugin Text Domain
			define('IGP_TXTDM', 'new-image-gallery');

			// Plugin Slug
			define('IG_PLUGIN_SLUG', 'image_gallery');

			// Plugin Directory Path
			define('IG_PLUGIN_DIR', plugin_dir_path(__FILE__));

			// Plugin Directory URL
			define('IG_PLUGIN_URL', plugin_dir_url(__FILE__));

			/**
			 * Create a key for the .htaccess secure download link.
			 *
			 * @uses    NONCE_KEY     Defined in the WP root config.php
			 */
			define('IG_SECURE_KEY', md5(NONCE_KEY));

		} // end of constructor function


		/**
		 * Setup the default filters and actions
		 *
		 * @uses      add_action()  To add various actions
		 * @access    private
		 * @return    void
		 */
		protected function _hooks()
		{

			// Load text domain
			add_action('plugins_loaded', array($this, '_load_textdomain'));

			// add gallery menu item, change menu filter for multisite
			add_action('admin_menu', array($this, '_srgallery_menu'), 101);

			// add gallery menu item, change menu filter for multisite
			add_action('admin_menu', array($this, '_featured_menu'), 105);

			// Create Image Gallery Custom Post
			add_action('init', array($this, '_New_Image_Gallery'));

			// Add meta box to custom post
			add_action('add_meta_boxes', array($this, '_admin_add_meta_box'));

			// loaded during admin init
			add_action('admin_init', array($this, '_admin_add_meta_box'));

			add_action('wp_ajax_image_gallery_js', array(&$this, '_ajax_image_gallery'));

			add_action('save_post', array(&$this, '_ig_save_settings'));

			// Shortcode Compatibility in Text Widgets
			add_filter('widget_text', 'do_shortcode');

			// add pfg cpt shortcode column - manage_{$post_type}_posts_columns
			add_filter('manage_image_gallery_posts_columns', array(&$this, 'set_image_gallery_shortcode_column_name'));

			// add pfg cpt shortcode column data - manage_{$post_type}_posts_custom_column
			add_action('manage_image_gallery_posts_custom_column', array(&$this, 'custom_image_gallery_shodrcode_data'), 10, 2);

			add_action('wp_enqueue_scripts', array(&$this, 'image_enqueue_scripts_in_header'));

		} // end of hook function

		public function image_enqueue_scripts_in_header()
		{
			wp_enqueue_script('jquery');
		}


		// Pricing table cpt shortcode column before date columns
		public function set_image_gallery_shortcode_column_name($defaults)
		{
			$new = array();
			$shortcode = $columns['image_gallery_shortcode'];  // save the tags column
			unset($defaults['tags']);   // remove it from the columns list

			foreach ($defaults as $key => $value) {
				if ($key == 'date') {  // when we find the date column
					$new['image_gallery_shortcode'] = __('Shortcode', 'new-image-gallery');  // put the tags column before it
				}
				$new[$key] = $value;
			}
			return $new;
		}

		// image gallery cpt shortcode column data
		public function custom_image_gallery_shodrcode_data($column, $post_id)
		{
			switch ($column) {
				case 'image_gallery_shortcode':
					echo "<input type='text' class='button button-primary' id='image-gallery-shortcode-" . esc_attr($post_id) . "' value='[IMG-Gal id=" . esc_attr($post_id) . "]' style='font-weight:bold; background-color:#32373C; color:#FFFFFF; text-align:center;' />";
					echo "<input type='button' class='button button-primary' onclick='return IMAGECopyShortcode" . esc_attr($post_id) . "();' readonly value='Copy' style='margin-left:4px;' />";
					echo "<span id='copy-msg-" . esc_attr($post_id) . "' class='button button-primary' style='display:none; background-color:#32CD32; color:#FFFFFF; margin-left:4px; border-radius: 4px;'>copied</span>";
					echo '<script>
						function IMAGECopyShortcode' . esc_attr($post_id) . "() {
							var copyText = document.getElementById('image-gallery-shortcode-" . esc_attr($post_id) . "');
							copyText.select();
							document.execCommand('copy');
							
							//fade in and out copied message
							jQuery('#copy-msg-" . esc_attr($post_id) . "').fadeIn('1000', 'linear');
							jQuery('#copy-msg-" . esc_attr($post_id) . "').fadeOut(2500,'swing');
						}
						</script>
					";
					break;
			}
		}


		/**
		 * Loads the text domain.
		 *
		 * @return    void
		 * @access    private
		 */
		public function _load_textdomain()
		{
			load_plugin_textdomain('new-image-gallery', false, dirname(plugin_basename(__FILE__)) . '/languages');
		}

		/**
		 * Adds the Gallery menu item
		 *
		 * @access    private
		 * @return    void
		 */
		public function _srgallery_menu()
		{
			$help_menu = add_submenu_page('edit.php?post_type=' . IG_PLUGIN_SLUG, __('Docs', 'new-image-gallery'), __('Docs', 'new-image-gallery'), 'administrator', 'ig-sr-doc-page', array($this, '_ig_doc_page'));
			$theme_menu = add_submenu_page('edit.php?post_type=' . IG_PLUGIN_SLUG, __('Our Theme', 'new-image-gallery'), __('Our Theme', 'new-image-gallery'), 'administrator', 'ig-sr-theme-page', array($this, '_ig_theme_page'));
		}

		public function _featured_menu()
		{
			$menu_feature_plugin = add_submenu_page('edit.php?post_type=' . IG_PLUGIN_SLUG, __('Featured Plugins', 'new-image-gallery'), __('Featured Plugins', 'new-image-gallery'), 'administrator', 'fp-page', array($this, '_ig_fp_page'));
		}

		/**
		 * Image Gallery Custom Post
		 * Create gallery post type in admin dashboard.
		 *
		 * @access    private
		 * @return    void      Return custom post type.
		 */
		public function _New_Image_Gallery()
		{
			$labels = array(
				'name' => _x('Image Gallery', 'Post Type General Name', 'new-image-gallery'),
				'singular_name' => _x('Image Gallery', 'Post Type Singular Name', 'new-image-gallery'),
				'menu_name' => __('New Image Gallery', 'new-image-gallery'),
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
				'public' => true,
				'show_ui' => true,
				'show_in_menu' => true,
				'menu_position' => 65,
				'menu_icon' => 'dashicons-images-alt2',
				'show_in_admin_bar' => true,
				'show_in_nav_menus' => true,
				'can_export' => true,
				'has_archive' => true,
				'exclude_from_search' => false,
				'publicly_queryable' => true,
				'capability_type' => 'page',
			);
			register_post_type('image_gallery', $args);

		} // end of post type function

		/**
		 * Adds Meta Boxes
		 *
		 * @access    private
		 * @return    void
		 */
		public function _admin_add_meta_box()
		{
			// Syntax: add_meta_box( $id, $title, $callback, $screen, $context, $priority, $callback_args );
			add_meta_box('1', __('Copy Image Gallery Shortcode', 'new-image-gallery'), array(&$this, '_ig_shortcode_left_metabox'), 'image_gallery', 'side', 'default');
			add_meta_box(__('Add Image', 'new-image-gallery'), __('Add Image', 'new-image-gallery'), array(&$this, 'ig_upload_multiple_images'), 'image_gallery', 'normal', 'default');
			add_meta_box(__('Rate Our Plugin', 'new-image-gallery'), __('Rate Our Plugin', 'new-image-gallery'), array(&$this, 'ig_rate_plugin'), 'image_gallery', 'side', 'default');
		}

		// image gallery copy shortcode meta box under publish button
		public function _ig_shortcode_left_metabox($post)
		{ ?>
			<p class="input-text-wrap">
				<input type="text" name="IGCopyShortcode" id="IGCopyShortcode"
					value="<?php echo '[IMG-Gal id=' . esc_attr($post->ID) . ']'; ?>" readonly
					style="height: 60px; text-align: center; width:100%;  font-size: 24px; border: 2px dashed;">
			<p id="igm-copy-code">
				<?php esc_html_e('Shortcode copied to clipboard!', 'new-image-gallery'); ?>
			</p>
			<p style="margin-top: 10px">
				<?php esc_html_e('Copy & Embed shotcode into any Page/ Post / Text Widget to display gallery.', 'new-image-gallery'); ?>
			</p>
			</p>
			<span onclick="copyToClipboard('#IGCopyShortcode')" class="igm-copy dashicons dashicons-clipboard"></span>
			<style>
				.igm-copy {
					position: absolute;
					top: 9px;
					right: 24px;
					font-size: 26px;
					cursor: pointer;
				}
			</style>
			<script>
				jQuery("#igm-copy-code").hide();
				function copyToClipboard(element) {
					var $temp = jQuery("<input>");
					jQuery("body").append($temp);
					$temp.val(jQuery(element).val()).select();
					document.execCommand("copy");
					$temp.remove();
					jQuery("#IGCopyShortcode").select();
					jQuery("#igm-copy-code").fadeIn();
				}
			</script>
			<?php
		}

		// meta rate us
		public function ig_rate_plugin()
		{
			?>
			<div style="text-align:center">
				<p>If you like our plugin then please <b>Rate us</b> on WordPress</p>
			</div>
			<div style="text-align:center">
				<span class="dashicons dashicons-star-filled"></span>
				<span class="dashicons dashicons-star-filled"></span>
				<span class="dashicons dashicons-star-filled"></span>
				<span class="dashicons dashicons-star-filled"></span>
				<span class="dashicons dashicons-star-filled"></span>
			</div>
			<br>
			<div style="text-align:center">
				<a href="https://wordpress.org/support/plugin/new-image-gallery/reviews/?filter=5" target="_new"
					class="button button-primary button-large" style="background: #496481; text-shadow: none;"><span
						class="dashicons dashicons-heart" style="line-height:1.4;"></span> Please Rate Us</a>
			</div>
			<?php
		}

		public function ig_upload_multiple_images($post)
		{
			wp_enqueue_script('media-upload');
			wp_enqueue_script('awl-ig-uploader.js', IG_PLUGIN_URL . 'assets/js/awl-ig-uploader.js', array('jquery'));
			wp_enqueue_style('awl-ig-uploader-css', IG_PLUGIN_URL . 'assets/css/awl-ig-uploader.css');
			wp_enqueue_media();
			?>
			<div class="row">
				<!--Add New Image Button-->
				<div class="file-upload">
					<div class="image-upload-wrap">
						<input class="add-new-slider file-upload-input" id="add-new-slider" name="add-new-slider"
							value="Upload Image" />
						<div class="drag-text">
							<h3>
								<?php esc_html_e('ADD IMAGES', 'new-image-gallery'); ?>
								<?php wp_nonce_field( 'igp_add_images', 'igp_add_images_nonce' ); ?>
							</h3>
						</div>
					</div>
				</div>
			</div>
			<div style="clear:left;"></div>


			<?php
			require_once 'include/gallery-settings.php';
		} // end of upload multiple image

		public function _ig_ajax_callback_function($id)
		{

			// wp_get_attachment_image_src ( int $attachment_id, string|array $size = 'thumbnail', bool $icon = false )
			// thumb, thumbnail, medium, large, post-thumbnail
			$thumbnail = wp_get_attachment_image_src($id, 'medium', true);
			$attachment = get_post($id); // $id = attachment id
			?>
			<li class="slide">
				<img class="new-slide" src="<?php echo esc_url($thumbnail[0]); ?>"
					alt="<?php echo esc_html(get_the_title($id)); ?>" style="height: 150px; width: 98%; border-radius: 8px;">
				<input type="hidden" id="slide-ids[]" name="slide-ids[]" value="<?php echo esc_attr($id); ?>" />
				<input type="text" name="slide-title[]" id="slide-title[]" style="width: 98%;"
					placeholder="<?php esc_attr_e('Image Title', 'new-image-gallery'); ?>"
					value="<?php echo esc_html(get_the_title($id)); ?>">
				<input type="text" name="slide-alt[]" id="slide-alt[]" style="width: 98%;"
					placeholder="<?php esc_attr_e('Image Alt', 'new-image-gallery'); ?>"
					value="<?php echo esc_attr(get_post_meta($id, '_wp_attachment_image_alt', true)); ?>">
				<a class="pw-trash-icon" name="remove-slide" id="remove-slide" href="#"><span
						class="dashicons dashicons-trash"></span></a>
			</li>
			<?php
		}

		public function _ajax_image_gallery()
		{
			if (current_user_can('manage_options')) {
				if (isset($_POST['igp_add_images_nonce']) && wp_verify_nonce($_POST['igp_add_images_nonce'], 'igp_add_images')) {
					echo esc_attr($this->_ig_ajax_callback_function($_POST['slideId']));
				} else {
					print 'Sorry, your nonce did not verify.';
					exit;
				}
			}
		}

		public function _ig_save_settings($post_id)
		{
			if (current_user_can('manage_options')) {
				if (isset($_POST['igp_save_nonce'])) {
					if (isset($_POST['igp_save_nonce']) && wp_verify_nonce($_POST['igp_save_nonce'], 'ig_save_settings')) {

						$gal_thumb_size = sanitize_text_field($_POST['gal_thumb_size']);
						$col_large_desktops = sanitize_text_field($_POST['col_large_desktops']);
						$col_desktops = sanitize_text_field($_POST['col_desktops']);
						$col_tablets = sanitize_text_field($_POST['col_tablets']);
						$col_phones = sanitize_text_field($_POST['col_phones']);
						$img_title = sanitize_text_field($_POST['img_title']);
						$no_spacing = sanitize_text_field($_POST['no_spacing']);
						$thumbnail_order = sanitize_text_field($_POST['thumbnail_order']);
						$igp_loop_st = sanitize_text_field($_POST['igp_loop_st']);
						$image_hover_effect_type = sanitize_text_field($_POST['image_hover_effect_type']);
						$image_hover_effect_four = sanitize_text_field($_POST['image_hover_effect_four']);
						$light_box = sanitize_text_field($_POST['light_box']);
						$custom_css = sanitize_textarea_field($_POST['custom-css']);
						$i = 0;

						$image_ids = array();
						$image_titles = array();

						$image_ids_val = isset($_POST['slide-ids']) ? (array) $_POST['slide-ids'] : array();
						$image_ids_val = array_map('sanitize_text_field', $image_ids_val);

						foreach ($image_ids_val as $image_id) {

							$image_ids[] = sanitize_text_field($_POST['slide-ids'][$i]);
							$image_titles[] = sanitize_text_field($_POST['slide-title'][$i]);
							$image_alt[] = sanitize_text_field($_POST['slide-alt'][$i]);

							$single_image_update = array(
								'ID' => $image_id,
								'post_title' => $image_titles[$i],
							);

							wp_update_post($single_image_update);
							$i++;
						}

						$gallery_settings = array(
							'slide-ids' => $image_ids,
							'slide-title' => $image_titles,
							'slide-alt' => $image_alt,
							'gal_thumb_size' => $gal_thumb_size,
							'col_large_desktops' => $col_large_desktops,
							'col_desktops' => $col_desktops,
							'col_tablets' => $col_tablets,
							'col_phones' => $col_phones,
							'img_title' => $img_title,
							'no_spacing' => $no_spacing,
							'thumbnail_order' => $thumbnail_order,
							'igp_loop_st' => $igp_loop_st,
							'image_hover_effect_type' => $image_hover_effect_type,
							'image_hover_effect_four' => $image_hover_effect_four,
							'light_box' => $light_box,
							'custom-css' => $custom_css,
						);
						$awl_image_gallery_shortcode_setting = 'awl_ig_settings_' . $post_id;
						update_post_meta($post_id, $awl_image_gallery_shortcode_setting, json_encode($gallery_settings));
					}
				}
			}
		}//end _ig_save_settings()

		/**
		 * Image Gallery Docs Page
		 * Create doc page to help user to setup plugin
		 *
		 * @access    private
		 * @return    void.
		 */
		public function _ig_doc_page()
		{
			require_once 'include/docs.php';
		}

		public function _ig_fp_page()
		{
			require_once 'featured-plugins/featured-plugins.php';
		}

		// theme page
		public function _ig_theme_page()
		{
			require_once 'our-theme/awp-theme.php';
		}

	} // end of class

	// register sf scripts
	function awplife_igp_register_scripts()
	{

		// css & JS
		wp_register_script('awl-ig-bootstrap-js', plugin_dir_url(__FILE__) . 'assets/js/bootstrap.min.js', array('jquery'), '', false);
		wp_register_script('awl-imagesloaded-pkgd-js', plugin_dir_url(__FILE__) . 'assets/js/imagesloaded.pkgd.js');
		wp_register_script('awl-ig-isotope-js', plugin_dir_url(__FILE__) . 'assets/js/isotope.pkgd.min.js');
		wp_register_style('awl-bootstrap-css', plugin_dir_url(__FILE__) . 'assets/css/bootstrap.css');
		// css & JS

		// lightbox // css & JS
		wp_register_script('awl-ld-lightbox-js', plugin_dir_url(__FILE__) . 'include/lightbox/ld-lightbox/js/lightbox.js', array('jquery'), '', false);
		wp_register_style('awl-ld-lightbox-css', plugin_dir_url(__FILE__) . 'include/lightbox/ld-lightbox/css/lightbox.css');

		wp_register_style('awl-bootstrap-lightbox-css', plugin_dir_url(__FILE__) . 'include/lightbox/bootstrap/css/ekko-lightbox.css');
		wp_register_script('awl-bootstrap-lightbox-js', plugin_dir_url(__FILE__) . 'include/lightbox/bootstrap/js/ekko-lightbox.js');

	}
	add_action('wp_enqueue_scripts', 'awplife_igp_register_scripts');


	// Plugin Recommend
	add_action('tgmpa_register', 'IGP_TXTDM_plugin_recommend');
	function IGP_TXTDM_plugin_recommend()
	{
		$plugins = array(
			array(
				'name' => 'Album Gallery – WordPress Gallery',
				'slug' => 'new-album-gallery',
				'required' => false,
			),
			array(
				'name' => 'Slider Responsive Slideshow',
				'slug' => 'slider-responsive-slideshow',
				'required' => false,
			),
			array(
				'name' => 'Slider – Image Video Link Carousal',
				'slug' => 'media-slider',
				'required' => false,
			),
		);
		tgmpa($plugins);
	}


	/**
	 * Instantiates the Class
	 *
	 * @global    object    $ig_gallery_object
	 */
	$ig_gallery_object = new New_Image_Gallery();
	require_once 'include/shortcode.php';
	require_once 'class-tgm-plugin-activation.php';

} // end of class exists
?>