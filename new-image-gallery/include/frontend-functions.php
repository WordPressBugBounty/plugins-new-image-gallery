<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Frontend Helper Functions for Image Gallery
 */

if (!function_exists('ig_render_gallery_item')) {
    /**
     * Renders a single gallery item tile
     */
    function ig_render_gallery_item($attachment_id, $gallery_settings, $gallery_id, $index = 0) {
        // Extract settings
        $gal_thumb_size   = isset($gallery_settings['gal_thumb_size']) ? $gallery_settings['gal_thumb_size'] : 'thumbnail';
        
        // Backward compatible column classes for DOM stability
        $col_l            = isset($gallery_settings['col_large_desktops']) ? ig_prefix_col_class($gallery_settings['col_large_desktops']) : 'ig-col-lg-3';
        $col_m            = isset($gallery_settings['col_desktops']) ? ig_prefix_col_class($gallery_settings['col_desktops']) : 'ig-col-md-4';
        $col_s            = isset($gallery_settings['col_tablets']) ? ig_prefix_col_class($gallery_settings['col_tablets']) : 'ig-col-sm-6';
        $col_xs           = isset($gallery_settings['col_phones']) ? ig_prefix_col_class($gallery_settings['col_phones']) : 'ig-col-xs-12';

        $img_title_show   = isset($gallery_settings['img_title']) ? $gallery_settings['img_title'] : 1;

        $hover_type       = isset($gallery_settings['image_hover_effect_type']) ? $gallery_settings['image_hover_effect_type'] : 'sg';
        
        // Determine hover effect class
        $hover_class = '';
        if ($hover_type !== 'no') {
            $hover_class = isset($gallery_settings['image_hover_effect_four']) ? $gallery_settings['image_hover_effect_four'] : 'hvr-box-shadow-outset';
        }

        // Get attachment data
        $attachment = get_post($attachment_id);
        if (!$attachment) return '';

        $title       = $attachment->post_title;
        $image_alt   = isset($gallery_settings['slide-alt'][$index]) && !empty($gallery_settings['slide-alt'][$index]) ? $gallery_settings['slide-alt'][$index] : get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
        if (empty($image_alt)) $image_alt = $title;


        $no_spacing  = isset($gallery_settings['no_spacing']) ? $gallery_settings['no_spacing'] : 0;
        $card_class  = ($no_spacing == 0) ? 'ig-has-card-border' : '';
        
        // Image Sources
        $src_thumb   = wp_get_attachment_image_src($attachment_id, $gal_thumb_size, true);
        $src_full    = wp_get_attachment_image_src($attachment_id, 'full', true);
        

        $full_url    = $src_full[0];

        // Start Output
        ob_start();
        ?>
        <?php 
        $light_box = isset($gallery_settings['light-box']) ? (int)$gallery_settings['light-box'] : 1;
        $show_anchor = ($light_box > 0);
        ?>
        <div class="ig-col single-image igp-item <?php echo esc_attr("{$col_l} {$col_m} {$col_s} {$col_xs}"); ?>" data-index="<?php echo esc_attr($index); ?>">
            <div class="ig-image-card igp-card <?php echo esc_attr($card_class); ?> <?php echo esc_attr($hover_class); ?>">
                <?php if ($show_anchor) : 
                    $lb_title_attr = ' data-title="' . esc_html($title) . '"';
                    ?>
                    <a class="ig-lightbox-item" href="<?php echo esc_url($full_url); ?>" data-lightbox="ig-gallery-<?php echo esc_attr($gallery_id); ?>"<?php echo $lb_title_attr; ?> data-width="<?php echo esc_attr($src_full[1]); ?>" data-height="<?php echo esc_attr($src_full[2]); ?>">
                <?php endif; ?>
                    


                    <div class="ig-image-container">
                        <?php echo wp_get_attachment_image($attachment_id, $gal_thumb_size, false, array('class' => 'ig-thumbnail loading', 'alt' => $image_alt)); ?>
                        
                        <?php if ($img_title_show) : ?>
                            <div class="ig-overlay ig-overlay-bottom">
                                <span class="ig-item-title"><?php echo esc_html($title); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>



                <?php if ($show_anchor) : ?>
                    </a>
                <?php endif; ?>
            </div>

        </div>
        <?php
        return ob_get_clean();
    }
}

/**
 * Returns a standardized and migrated configuration array for a specific gallery.
 * This function handles Base64-to-JSON migration and legacy no_spacing rules.
 */
if (!function_exists('ig_get_gallery_config')) {
    function ig_get_gallery_config($gallery_id) {
        $meta_key = 'awl_ig_settings_' . $gallery_id;
        $encoded_data = get_post_meta($gallery_id, $meta_key, true);
        $settings = array();

        // 1. Initial Decoding
        if (!empty($encoded_data)) {
            if (is_array($encoded_data)) {
                $settings = $encoded_data;
            } else {
                // Try Base64/Serialized first
                $decoded = base64_decode($encoded_data);
                if (($unserialized = awl_ig_safe_unserialize($decoded)) !== false && is_array($unserialized)) {
                    $settings = $unserialized;
                    // Auto-migrate to JSON format for future performance
                    update_post_meta($gallery_id, $meta_key, json_encode($settings));
                } else {
                    // Try JSON
                    $settings = json_decode($encoded_data, true);
                }
            }
        }

        if (!is_array($settings)) {
            $settings = array();
        }

        // 2. Legacy Key Mapping & Migration Logic
        
        // Map old light_box key to light-box
        if (!isset($settings['light-box']) && isset($settings['light_box'])) {
            $settings['light-box'] = $settings['light_box'];
        }

        // Map old igp_loop_st key to show_lightbox_loop
        if (!isset($settings['show_lightbox_loop']) && isset($settings['igp_loop_st'])) {
            // Convert 'true'/'false' strings if they exist
            $loop_val = $settings['igp_loop_st'];
            if ($loop_val === "true") $loop_val = 1;
            if ($loop_val === "false") $loop_val = 0;
            $settings['show_lightbox_loop'] = (int) $loop_val;
        }

        // Apply defaults based on the simplified yes/no spacing toggle
        $no_spacing = isset($settings['no_spacing']) ? (int)$settings['no_spacing'] : 0;
        
        // 3. Apply Modern Global Defaults
        $defaults = array(
            'gal_thumb_size'         => 'thumbnail',
            'no_spacing'             => 0,
            'img_title'              => 1,

            'col_large_desktops'     => 4,
            'col_desktops'           => 3,
            'col_tablets'            => 2,
            'col_phones'             => 1,
            'light-box'              => 1,
            'thumbnail_order'        => 'ASC',

            'image_hover_effect_type'=> 'sg',
            'image_hover_effect_four' => 'hvr-grow-shadow',


            'show_lightbox_loop'     => 1,
        );

        $settings = array_merge($defaults, $settings);
        $settings['thumb_layout'] = 'masonry'; // Force masonry in free version

        return $settings;
    }
}
