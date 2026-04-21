jQuery(document).ready(function($) {
    console.log('New Image Gallery Admin JS Loaded');

    // Tab Navigation - using delegation for robustness
    $(document).on('click', ".awl-ig-tabs-nav .nav-item", function(e) {
        e.preventDefault();
        console.log('Tab clicked:', $(this).data("target"));
        
        $(".awl-ig-tabs-nav .nav-item").removeClass("active");
        $(this).addClass("active");
        
        var target = $(this).data("target");
        $(".awl-ig-tab-content").removeClass("active");
        $("#" + target).addClass("active");
    });


    // Hover Effect Logic Show/Hide
    function updateHoverSettings(animate = true) {
        var val = $('input[name="image_hover_effect_type"]:checked').val();
        if (val == "no") {
            if (animate) {
                $('.ig-inline-options').slideUp();
            } else {
                $('.ig-inline-options').hide();
            }
        } else {
            if (animate) {
                $('.ig-inline-options').slideDown().css('display', 'flex');
            } else {
                $('.ig-inline-options').show().css('display', 'flex');
            }
            if (val == "sg") {
                $('.he_four').show();
            }
        }
    }
    
    $('input[name="image_hover_effect_type"]').on('change', function() {
        updateHoverSettings(true);
    });
    
    updateHoverSettings(false);
});

// Sorting Helper (Global scope as called by onclick)
function IGPSortSlides(order) {
    if (order == "ASC") {
        jQuery(".igp-listitems li").sort(sort_li_asc).appendTo('.igp-listitems');
    }
    if (order == "DESC") {
        jQuery(".igp-listitems li").sort(sort_li_desc).appendTo('.igp-listitems');
    }
    function sort_li_asc(a, b) {
        return (jQuery(b).data('position')) > (jQuery(a).data('position')) ? 1 : -1;
    }
    function sort_li_desc(a, b) {
        return (jQuery(b).data('position')) < (jQuery(a).data('position')) ? 1 : -1;
    }
}
