<?php
if (! defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * Register Elementor Widget for Image Gallery
 */
add_action('elementor/widgets/register', 'awl_image_gallery_register_elementor_widget');
function awl_image_gallery_register_elementor_widget($widgets_manager) {
    if (class_exists('\Elementor\Widget_Base')) {
        // Register widget instance
        $widgets_manager->register(new \Elementor_Image_Gallery_Widget());
    }
}

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
                'post_status'    => 'publish',
                'orderby'        => 'title',
                'order'          => 'ASC',
            ));

            $gallery_options = array('' => esc_html__('-- Select Gallery --', 'new-image-gallery'));
            if (!empty($all_galleries)) {
                foreach ($all_galleries as $g) {
                    $gallery_options[$g->ID] = $g->post_title . ' (ID: ' . $g->ID . ')';
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
            
            if (!empty($settings['gallery_id'])) {
                $gallery_id = (int)$settings['gallery_id'];
                echo do_shortcode('[IMG-Gal id=' . $gallery_id . ']');
            } else {
                echo '<div style="padding:20px; border:1px dashed #ccc; text-align:center;">' . esc_html__('Please select an Image Gallery.', 'new-image-gallery') . '</div>';
            }
        }
    }
}
