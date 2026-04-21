<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * LD Lightbox Load File 
 */
wp_enqueue_style('awl-ld-lightbox-css');
wp_enqueue_script('awl-ld-lightbox-js');

require(plugin_dir_path(__FILE__) . 'common-loop.php');