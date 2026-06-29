<?php
if (! defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * Register all IG frontend assets early so they're available in any context.
 */
add_action('wp_enqueue_scripts', 'awl_ig_elementor_register_assets', 5);
function awl_ig_elementor_register_assets() {
    wp_register_style('awl-ig-frontend-grid-css', IG_PLUGIN_URL . 'assets/css/ig-frontend-grid.css', array(), IG_PLUGIN_VER);
    wp_register_style('awl-ld-lightbox-css', IG_PLUGIN_URL . 'include/lightbox/ld-lightbox/css/lightbox.css', array(), IG_PLUGIN_VER);
    wp_register_script('awl-imagesloaded-pkgd-js', IG_PLUGIN_URL . 'assets/js/imagesloaded.pkgd.js', array('jquery'), IG_PLUGIN_VER, true);
    wp_register_script('awl-ig-isotope-js', IG_PLUGIN_URL . 'assets/js/isotope.pkgd.min.js', array('jquery'), IG_PLUGIN_VER, true);
    wp_register_script('awl-ig-hash-guard-js', IG_PLUGIN_URL . 'assets/js/ig-hash-guard.js', array(), IG_PLUGIN_VER, false); // Load in head
    wp_register_script('awl-ld-lightbox-js', IG_PLUGIN_URL . 'include/lightbox/ld-lightbox/js/lightbox.js', array('jquery'), IG_PLUGIN_VER, true);
    wp_register_script('awl-ig-frontend-js', IG_PLUGIN_URL . 'assets/js/ig-frontend.js', array('jquery', 'awl-imagesloaded-pkgd-js', 'awl-ig-isotope-js'), IG_PLUGIN_VER, true);
}

/**
 * Force-enqueue gallery assets on Elementor preview pages.
 */
add_action('elementor/preview/enqueue_styles', 'awl_ig_elementor_enqueue_preview_assets');
function awl_ig_elementor_enqueue_preview_assets() {
    wp_enqueue_style('awl-ig-frontend-grid-css');
    wp_enqueue_style('awl-ld-lightbox-css');
    wp_enqueue_script('jquery');
    wp_enqueue_script('awl-imagesloaded-pkgd-js');
    wp_enqueue_script('awl-ig-isotope-js');
    wp_enqueue_script('awl-ig-hash-guard-js');
    wp_enqueue_script('awl-ld-lightbox-js');
    wp_enqueue_script('awl-ig-frontend-js');
}

/**
 * Register Elementor Widget for Image Gallery
 */
add_action('elementor/widgets/register', 'awl_image_gallery_register_elementor_widget');
function awl_image_gallery_register_elementor_widget($widgets_manager) {
    if (class_exists('\Elementor\Widget_Base')) {

        class Elementor_Image_Gallery_Widget extends \Elementor\Widget_Base {

            public function get_name() {
                return 'image_gallery_widget';
            }

            public function get_title() {
                return esc_html__('Image Gallery', 'new-image-gallery');
            }

            public function get_icon() {
                return 'eicon-gallery-grid';
            }

            public function get_categories() {
                return array('general');
            }

            public function get_keywords() {
                return array('image', 'gallery', 'photo', 'grid');
            }

            public function get_style_depends() {
                return array('awl-ig-frontend-grid-css', 'awl-ld-lightbox-css');
            }

            public function get_script_depends() {
                return array('awl-imagesloaded-pkgd-js', 'awl-ig-isotope-js', 'awl-ig-hash-guard-js', 'awl-ld-lightbox-js', 'awl-ig-frontend-js');
            }

            protected function register_controls() {
                $this->start_controls_section(
                    'section_content',
                    array(
                        'label' => esc_html__('Gallery Source Settings', 'new-image-gallery'),
                        'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
                    )
                );

                // Fetch Galleries List
                $all_galleries = get_posts(array(
                    'post_type'      => 'image_gallery',
                    'posts_per_page' => -1,
                    'post_status'    => 'any',
                    'orderby'        => 'title',
                    'order'          => 'ASC',
                ));

                $gallery_options = array('' => esc_html__('-- Select Gallery --', 'new-image-gallery'));
                if (!empty($all_galleries)) {
                    foreach ($all_galleries as $g) {
                        $gallery_options[$g->ID] = $g->post_title ? $g->post_title . ' (ID: ' . $g->ID . ')' : esc_html__('(no title)', 'new-image-gallery') . ' (ID: ' . $g->ID . ')';
                    }
                }

                $this->add_control(
                    'gallery_id',
                    array(
                        'label'     => esc_html__('Select Gallery', 'new-image-gallery'),
                        'type'      => \Elementor\Controls_Manager::SELECT,
                        'options'   => $gallery_options,
                        'default'   => '',
                    )
                );

                $this->end_controls_section();
            }

            protected function render() {
                $settings = $this->get_settings_for_display();
                
                if (empty($settings['gallery_id'])) {
                    echo '<div style="padding:20px; border:1px dashed #ccc; text-align:center;">' . esc_html__('Please select an Image Gallery.', 'new-image-gallery') . '</div>';
                    return;
                }

                $gallery_id = (int)$settings['gallery_id'];

                // Detect Elementor editor/preview context
                $is_elementor_editor = false;
                if (class_exists('\Elementor\Plugin')) {
                    if (\Elementor\Plugin::$instance->editor->is_edit_mode() || \Elementor\Plugin::$instance->preview->is_preview_mode()) {
                        $is_elementor_editor = true;
                    }
                }

                if ($is_elementor_editor) {
                    // In Elementor editor: inject CSS <link> tags directly into the HTML output
                    // because wp_enqueue_style() calls are ignored during AJAX widget re-renders.
                    $css_files = array(
                        IG_PLUGIN_URL . 'assets/css/ig-frontend-grid.css',
                        IG_PLUGIN_URL . 'include/lightbox/ld-lightbox/css/lightbox.css',
                    );
                    $ver = IG_PLUGIN_VER;
                    foreach ($css_files as $css_url) {
                        $css_url_versioned = esc_url($css_url) . '?ver=' . esc_attr($ver);
                        echo '<link rel="stylesheet" href="' . $css_url_versioned . '" type="text/css" media="all" />' . "\n";
                    }
                }

                // Render the gallery shortcode
                echo do_shortcode('[IMG-Gal id=' . $gallery_id . ']');

                if ($is_elementor_editor) {
                    // In Elementor editor: inject inline JS to initialize Isotope and show gallery.
                    // Inline <script> in shortcode output doesn't execute during AJAX re-render,
                    // so we use a self-executing script that runs immediately.
                    ?>
                    <script type="text/javascript">
                    (function() {
                        function igInitGallery() {
                            if (typeof jQuery === 'undefined') return;
                            var $ = jQuery;
                            var $wrapper = $('#image_gallery_wrap_<?php echo esc_js($gallery_id); ?>');
                            var $grid = $('.all-images_<?php echo esc_js($gallery_id); ?>');
                            if (!$grid.length) return;

                            // Force visibility immediately
                            $grid.css('opacity', '1').addClass('ig-loaded');
                            $grid.find('.single-image').css({
                                'opacity': '1',
                                'animation': 'none'
                            });

                            // Initialize Isotope if available
                            if (typeof $.fn.isotope !== 'undefined') {
                                var layoutMode = $wrapper.attr('data-layout') || 'masonry';
                                var isotopeMode = (layoutMode === 'grid') ? 'fitRows' : 'masonry';
                                $grid.imagesLoaded(function() {
                                    $grid.isotope({
                                        itemSelector: '.single-image',
                                        layoutMode: isotopeMode,
                                        masonry: {
                                            columnWidth: '.grid-sizer',
                                            percentPosition: true
                                        },
                                        transitionDuration: '0.6s'
                                    });
                                    $grid.isotope('layout');
                                });
                            }
                        }

                        // Try immediately and also after a short delay
                        igInitGallery();
                        setTimeout(igInitGallery, 500);
                        setTimeout(igInitGallery, 1500);
                    })();
                    </script>
                    <?php
                }
            }
        }

        // Register widget instance
        $widgets_manager->register(new \Elementor_Image_Gallery_Widget());
    }
}

