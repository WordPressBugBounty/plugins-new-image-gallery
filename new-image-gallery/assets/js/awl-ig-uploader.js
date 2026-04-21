jQuery(function(jQuery) {
    
    var file_frame,
    awl_image_gallery = {
        ul: '',
        init: function() {
            this.ul = jQuery('.sbox');
            this.ul.sortable({
                items: '.ig-image-slide',
                handle: '.ig-move-handle',
                placeholder: 'ig-sortable-placeholder',
                forcePlaceholderSize: true,
                tolerance: 'pointer',
                opacity: 0.8,
                revert: false, // Set to false for instant drops (faster feel)
                scroll: true,
                scrollSensitivity: 100,
                scrollSpeed: 40,
                start: function(event, ui) {
                    ui.placeholder.height(ui.item.outerHeight());
                    ui.placeholder.width(ui.item.outerWidth());
                }
            });			
			
            /**
			 * Add Images Callback Function
			 */
            jQuery('#add-new-slider').on('click', function(event) {
				var igp_add_images_nonce = jQuery("#igp_add_images_nonce").val();
                event.preventDefault();
                if (file_frame) {
                    file_frame.open();
                    return;
                }
                file_frame = wp.media.frames.file_frame = wp.media({
                    title: igp_uploader_vars.media_title,
                    button: {
                        text: igp_uploader_vars.button_text
                    },
                    multiple: true
                });

                file_frame.on('select', function() {
                    var selection = file_frame.state().get('selection');
                    var ids = selection.map(function(attachment) {
                        return attachment.id;
                    });
                    
                    if (ids.length > 0) {
                        awl_image_gallery.get_thumbnails(ids, igp_add_images_nonce);
                    }
                });
                file_frame.open();
            });
			
			/**
			 * Delete Slide Callback Function
			 */
            this.ul.on('click', '.remove-slide', function() {
                if (confirm(igp_uploader_vars.confirm_delete)) {
                    jQuery(this).closest('.ig-image-slide').fadeOut(300, function() {
                        jQuery(this).remove();
                    });
                }
                return false;
            });
			
			/**
			 * Delete All Slides Callback Function
			 */
			jQuery('#remove-all-slides').on('click', function() {
                if (confirm(igp_uploader_vars.confirm_delete_all)) {
                    awl_image_gallery.ul.empty();
                }
                return false;
            });
           
        },
        get_thumbnails: function(ids, igp_add_images_nonce) {
            // Show loading state if needed
            var data = {
                action: 'image_gallery_js',
                slideId: ids, // Can be an array now
				igp_add_images_nonce: igp_add_images_nonce,
            };
            jQuery.post(ajaxurl, data, function(response) {
                awl_image_gallery.ul.append(response);
            });
        }
    };
    awl_image_gallery.init();

    /**
     * Clipboard Copy Functionality
     */
    jQuery(document).on('click', '.igm-copy', function() {
        var targetSelector = jQuery(this).data('target');
        var $input = jQuery(targetSelector);
        
        if ($input.length) {
            // Select text
            $input.select();
            // Copy
            document.execCommand('copy');
            
            // Show feedback
            var $feedback = jQuery('#igm-copy-code');
            if ($feedback.length) {
                $feedback.stop().fadeIn(200).delay(1500).fadeOut(400);
            }

            // Visual pulse on icon
            var $icon = jQuery(this);
            $icon.css('transform', 'translateY(-50%) scale(0.9)');
            setTimeout(function() {
                $icon.css('transform', 'translateY(-50%) scale(1.05)');
            }, 100);
        }
    });
});