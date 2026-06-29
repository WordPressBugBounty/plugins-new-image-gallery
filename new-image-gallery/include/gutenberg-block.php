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

    // Register all frontend styles/scripts on init so they're available for editor_style
    wp_register_style('awl-ig-frontend-grid-css', IG_PLUGIN_URL . 'assets/css/ig-frontend-grid.css', array(), IG_PLUGIN_VER);
    wp_register_style('awl-ld-lightbox-css', IG_PLUGIN_URL . 'include/lightbox/ld-lightbox/css/lightbox.css', array(), IG_PLUGIN_VER);

    // Register editor-only override styles (force visibility in editor preview)
    wp_register_style('awl-ig-block-editor-css', false);
    wp_add_inline_style('awl-ig-block-editor-css', '
        .ig-row { opacity: 1 !important; display: flow-root !important; }
        .ig-row .single-image { opacity: 1 !important; animation: none !important; }
    ');
    
    wp_register_script(
        'awl-ig-gutenberg-block-js',
        IG_PLUGIN_URL . 'assets/js/gutenberg-block.js',
        array('wp-blocks', 'wp-element', 'wp-components', 'wp-block-editor', 'wp-server-side-render', 'jquery'),
        IG_PLUGIN_VER,
        true
    );

    register_block_type('new-image-gallery/image-gallery-block', array(
        'api_version'     => 3,
        'editor_script'   => 'awl-ig-gutenberg-block-js',
        'editor_style'    => array('awl-ig-frontend-grid-css', 'awl-ld-lightbox-css', 'awl-ig-block-editor-css'),
        'render_callback' => 'awl_image_gallery_block_render',
        'attributes'      => array(
            'galleryId' => array(
                'type'    => 'string',
                'default' => '',
            ),
        ),
    ));
}

add_action('enqueue_block_editor_assets', 'awl_image_gallery_gutenberg_localize');
function awl_image_gallery_gutenberg_localize() {
    $all_galleries = get_posts(array(
        'post_type'      => 'image_gallery',
        'posts_per_page' => -1,
        'post_status'    => 'any',
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
 * Used for both frontend and ServerSideRender editor preview.
 */
function awl_image_gallery_block_render($attributes) {
    $gallery_id = isset($attributes['galleryId']) ? (int)$attributes['galleryId'] : 0;
    if ($gallery_id) {
        return do_shortcode('[IMG-Gal id=' . $gallery_id . ']');
    }
    return '';
}

