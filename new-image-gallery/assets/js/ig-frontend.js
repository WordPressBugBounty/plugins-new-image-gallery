jQuery(document).ready(function ($) {
    
    function initIGGallery(wrapper) {
        var $wrapper = $(wrapper);
        var galleryID = $wrapper.attr('id').replace('image_gallery_wrap_', '');
        var layoutMode = $wrapper.attr('data-layout') || 'masonry';
        var loopEnabled = $wrapper.attr('data-lb-loop') === '1';
        var isotopeMode = (layoutMode === 'grid') ? 'fitRows' : 'masonry';

        var $grid = $wrapper.find('.igp-gallery').isotope({
            itemSelector: '.single-image',
            layoutMode: isotopeMode,
            masonry: {
                columnWidth: '.grid-sizer',
                percentPosition: true
            },
            transitionDuration: '0.6s'
        });

        // Handle Layout after images load
        $grid.imagesLoaded().done(function () {
            $grid.isotope('layout');
        }).progress(function () {
            $grid.isotope('layout');
        });

        // Force layout update on window resize to sync with CSS variables
        var resizeTimer;
        $(window).on('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                $grid.isotope('layout');
            }, 100);
        });

        // Lightbox Logic (if applicable)
        function updateLDLightboxOptions() {
            if (typeof lightbox !== 'undefined') {
                var albumLabel = $wrapper.attr('data-lb-label') || "Image %1 of %2";
                lightbox.option({
                    'wrapAround': loopEnabled,
                    'alwaysShowNavOnTouchDevices': true,
                    'albumLabel': albumLabel
                });
            }
        }

        // Set options on interaction for LD Lightbox
        $wrapper.on('mouseenter mousedown touchstart', '.ig-lightbox-item', function() {
            updateLDLightboxOptions();
        });

        // Initial setup
        updateLDLightboxOptions();
    }

    // Initialize all galleries on the page
    $('.ig-gallery-outer-wrap').each(function() {
        initIGGallery(this);
    });

});
