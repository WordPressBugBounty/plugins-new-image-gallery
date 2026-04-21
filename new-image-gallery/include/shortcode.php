<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Image Gallery Shortcode
 *
 * @access    public
 * @since     3.0
 *
 * @return    Create Fontend Gallery Output
 */
add_shortcode('IMG-Gal', 'awl_image_gallery_shortcode');

function awl_image_gallery_shortcode($post_id)
{
	ob_start();
	//js
	//wp_enqueue_script('jquery');
	wp_enqueue_script('awl-ig-hash-guard-js');
	wp_enqueue_script('awl-imagesloaded-pkgd-js');
	wp_enqueue_script('awl-ig-isotope-js');
	wp_enqueue_script('awl-ig-frontend-js');
	wp_enqueue_style('awl-ig-frontend-grid-css');


	$image_gallery_id = esc_attr($post_id['id']);
	$gallery_settings = ig_get_gallery_config($image_gallery_id);

	// Normalization for CSS Variables

	$col_lg           = ig_get_column_count($gallery_settings['col_large_desktops'], 4);
	$col_md           = ig_get_column_count($gallery_settings['col_desktops'], 3);
	$col_sm           = ig_get_column_count($gallery_settings['col_tablets'], 2);
	$col_xs           = ig_get_column_count($gallery_settings['col_phones'], 1);
	
	$no_spacing       = isset($gallery_settings['no_spacing']) ? (int)$gallery_settings['no_spacing'] : 0;
	$spacing_val      = ($no_spacing == 0) ? 8 : 0;
	$gutter           = $spacing_val . 'px';
	$light_box        = (int)$gallery_settings['light-box'];
	$thumbnail_order  = $gallery_settings['thumbnail_order'];


	// Layout Mode
	$layout_class = 'ig-layout-masonry';

	// Hover Effect Logic
	$hover_type       = $gallery_settings['image_hover_effect_type'];

?>
	<!-- Gallery Engine Assets -->
	<style>
		#image_gallery_wrap_<?php echo esc_attr($image_gallery_id); ?> {
            --ig-gutter: <?php echo esc_attr($gutter); ?>;
            --ig-cols-lg: <?php echo esc_attr($col_lg); ?>;
            --ig-cols-md: <?php echo esc_attr($col_md); ?>;
            --ig-cols-sm: <?php echo esc_attr($col_sm); ?>;
            --ig-cols-xs: <?php echo esc_attr($col_xs); ?>;
            
            /* Dynamic Border Variables */
            <?php 
            $b_radius = $spacing_val;
            $b_thickness = $spacing_val;
            

            ?>
            --ig-radius: <?php echo esc_attr($b_radius); ?>px;
            --ig-card-radius: <?php echo esc_attr($b_radius); ?>px;
            --ig-card-padding: <?php echo esc_attr($b_thickness); ?>px;


        }
	</style>
    <?php 
    $wrapper_classes = array('ig-gallery-outer-wrap', $layout_class);
    $wrapper_classes = apply_filters('ig_gallery_wrapper_class', $wrapper_classes, $image_gallery_id);
    ?>
    <div id="image_gallery_wrap_<?php echo esc_attr($image_gallery_id); ?>" 
        class="<?php echo esc_attr(implode(' ', $wrapper_classes)); ?>" 
        data-layout="masonry" 
        data-lb-loop="<?php echo (isset($gallery_settings['show_lightbox_loop']) ? (int)$gallery_settings['show_lightbox_loop'] : 1); ?>" 
        data-lb-label="<?php esc_attr_e('Image %1 of %2', 'new-image-gallery'); ?>"
        data-version="<?php echo esc_attr(IG_PLUGIN_VER); ?>">

<?php
	// load without lightbox gallery output
	if ($light_box == 0) {
		require(plugin_dir_path(__FILE__) . 'common-loop.php');
	}
	// load ld lightbox gallery output
	if ($light_box != 0) {
		require(plugin_dir_path(__FILE__) . 'nig-ld-lightbox.php');
	}


    ?>
    </div><?php
	return ob_get_clean();
}
?>