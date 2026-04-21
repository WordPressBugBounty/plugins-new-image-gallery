<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Common Gallery Loop Template
 * Used by all lightbox sub-templates to maintain consistency.
 */

if (isset($gallery_settings['slide-ids']) && is_array($gallery_settings['slide-ids']) && count($gallery_settings['slide-ids']) > 0) {
    $slide_ids = $gallery_settings['slide-ids'];
    $total_images = count($slide_ids);
    
    // Handle Ordering while preserving original indices for metadata lookups
    $ordered_slides = array();
    foreach ($gallery_settings['slide-ids'] as $idx => $id) {
        $ordered_slides[] = array('id' => $id, 'index' => $idx);
    }
    
    if ($thumbnail_order == "DESC") {
        $ordered_slides = array_reverse($ordered_slides);
    } elseif ($thumbnail_order == "RANDOM") {
        srand($rand_seed); // Deterministic shuffle
        shuffle($ordered_slides);
    }


    ?>
    <div id="image_gallery_<?php echo esc_attr($image_gallery_id); ?>" 
         class="ig-row igp-gallery all-images_<?php echo esc_attr($image_gallery_id); ?>">
        <div class="grid-sizer"></div>
        <?php
        for ($i = 0; $i < $total_images; $i++) {
            echo wp_kses_post(ig_render_gallery_item($ordered_slides[$i]['id'], $gallery_settings, $image_gallery_id, $ordered_slides[$i]['index']));
        }
        ?>
    </div>



    <?php
} else {
    echo '<p class="ig-no-images">' . esc_html__('No images found in this gallery.', 'new-image-gallery') . '</p>';
}
