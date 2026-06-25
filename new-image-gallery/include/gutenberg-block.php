<?php
if (! defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * Register Gutenberg Block for Image Gallery
 */
add_action('init', 'awl_image_gallery_register_gutenberg_block');
function awl_image_gallery_register_gutenberg_block() {
    if (!function_exists('register_block_type')) {
        return;
    }
    
    wp_register_script(
        'awl-ig-gutenberg-block-js',
        IG_PLUGIN_URL . 'assets/js/gutenberg-block.js',
        array('wp-blocks', 'wp-element', 'wp-components', 'wp-block-editor', 'wp-editor', 'jquery'),
        IG_PLUGIN_VER,
        true
    );

    register_block_type('new-image-gallery/image-gallery-block', array(
        'editor_script'   => 'awl-ig-gutenberg-block-js',
        'render_callback' => 'awl_image_gallery_block_render',
    ));
}

add_action('enqueue_block_editor_assets', 'awl_image_gallery_gutenberg_localize');
function awl_image_gallery_gutenberg_localize() {
    $all_galleries = get_posts(array(
        'post_type'      => 'image_gallery',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'orderby'        => 'title',
        'order'          => 'ASC',
    ));
    
    $galleries_data = array();
    if (!empty($all_galleries)) {
        foreach ($all_galleries as $g) {
            $galleries_data[] = array(
                'id'    => $g->ID,
                'title' => $g->post_title ? $g->post_title : __('(no title)', 'new-image-gallery'),
            );
        }
    }
    
    wp_localize_script('awl-ig-gutenberg-block-js', 'igp_gutenberg_data', array(
        'galleries'  => $galleries_data,
    ));
}

/**
 * Gutenberg Block Render Callback
 */
function awl_image_gallery_block_render($attributes) {
    $gallery_id = isset($attributes['galleryId']) ? (int)$attributes['galleryId'] : 0;
    if ($gallery_id) {
        return do_shortcode('[IMG-Gal id=' . $gallery_id . ']');
    }
    return '';
}
