<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// Enqueue Indigo CSS for the new admin layout
wp_enqueue_style('awl-ig-admin-style-css', IG_PLUGIN_URL . 'assets/css/ig-admin-style.css', array(), IG_PLUGIN_VER);
wp_enqueue_script('awl-ig-admin-js');


// Extract post ID
$post_id = esc_attr($post->ID);

// Retrieves current, legacy, or default configuration
$gallery_settings = ig_get_gallery_config($post_id);

// Normalize column settings for Admin UI consistency
$col_lg_val = ig_get_column_count($gallery_settings['col_large_desktops'], 4);
$col_md_val = ig_get_column_count($gallery_settings['col_desktops'], 3);
$col_sm_val = ig_get_column_count($gallery_settings['col_tablets'], 2);
$col_xs_val = ig_get_column_count($gallery_settings['col_phones'], 1);
?>

<div class="awl-ig-settings-wrapper">
	<!-- Navigation Tabs -->
	<div class="awl-ig-tabs-nav">
		<a href="#" class="nav-item active" data-target="tab-add-images">
			<span class="dashicons dashicons-format-image"></span> <?php esc_html_e('Add Images', 'new-image-gallery'); ?>
		</a>
		<a href="#" class="nav-item" data-target="tab-layout-design">
			<span class="dashicons dashicons-layout"></span> <?php esc_html_e('Layout & Design', 'new-image-gallery'); ?>
		</a>
		<a href="#" class="nav-item" data-target="tab-lightbox-links">
			<span class="dashicons dashicons-welcome-view-site"></span> <?php esc_html_e('Lightbox', 'new-image-gallery'); ?>
		</a>
		<a href="#" class="nav-item ig-pro-tab" data-target="tab-upgrade-pro" style="color: #f59e0b; font-weight: 600;">
			<span class="dashicons dashicons-star-filled" style="color: #f59e0b;"></span> <?php esc_html_e('Upgrade to Pro', 'new-image-gallery'); ?>
		</a>

	</div>

	<!-- Content Area -->
	<div class="awl-ig-tabs-content-wrapper">
		
		<!-- Tab 1: Add Images -->
		<div class="awl-ig-tab-content active" id="tab-add-images">
			<div class="file-upload">
				<div class="image-upload-wrap">
					<input class="add-new-slider file-upload-input" id="add-new-slider" name="add-new-slider"
						value="Upload Image" />
					<div class="drag-text">
                        <span class="dashicons dashicons-cloud-upload" style="font-size: 40px; width: 40px; height: 40px; color: var(--ig-primary); margin-bottom: 15px; display: block; margin-left: auto; margin-right: auto;"></span>
						<h3>
							<?php esc_html_e('ADD IMAGES', 'new-image-gallery'); ?>
						</h3>
						<?php wp_nonce_field('igp_add_images', 'igp_add_images_nonce'); ?>
					</div>
				</div>
			</div>
			
			<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
				<div class="ig-button-group">
					<button type="button" class="ig-btn ig-btn-secondary" onclick="return IGPSortSlides('ASC');">
						<span class="dashicons dashicons-sort"></span> <?php esc_html_e('Ascending', 'new-image-gallery'); ?>
					</button>
					<button type="button" class="ig-btn ig-btn-secondary" onclick="return IGPSortSlides('DESC');">
						<span class="dashicons dashicons-sort"></span> <?php esc_html_e('Descending', 'new-image-gallery'); ?>
					</button>
				</div>
				<button type="button" id="remove-all-slides" class="ig-btn ig-btn-danger">
					<span class="dashicons dashicons-trash"></span> <?php esc_html_e('Delete All Images', 'new-image-gallery'); ?>
				</button>
			</div>

			<ul id="remove-slides" class="sbox igp-listitems">
				<?php
				if (isset($gallery_settings['slide-ids']) && is_array($gallery_settings['slide-ids'])) {
					$count = 0;
					foreach ($gallery_settings['slide-ids'] as $id) {
						if (isset($gallery_settings['slide-alt'][$count]) && !empty($gallery_settings['slide-alt'][$count])) {
							$image_alt = $gallery_settings['slide-alt'][$count];
						} else {
							$image_alt = get_post_meta($id, '_wp_attachment_image_alt', true);
							if (empty($image_alt)) {
								$image_alt = get_the_title($id);
							}
						}
						$thumbnail = wp_get_attachment_image_src($id, 'medium', true);
						$attachment = get_post($id);
						$attachment = get_post($id);
						?>
						<li class="ig-image-slide" id="<?php echo esc_attr($id); ?>" data-position="<?php echo esc_attr($id); ?>">
							<div class="ig-image-preview">
								<div class="ig-image-controls">
									<div class="ig-move-handle" title="<?php esc_attr_e('Drag to reorder', 'new-image-gallery'); ?>"><span class="dashicons dashicons-move"></span></div>
									<a class="pw-trash-icon remove-slide" name="remove-slide" href="#" title="<?php esc_attr_e('Delete image', 'new-image-gallery'); ?>"><span class="dashicons dashicons-trash"></span></a>
								</div>
								<img src="<?php echo esc_url($thumbnail[0]); ?>" alt="<?php echo esc_html(get_the_title($id)); ?>">
							</div>
							<div class="ig-image-info">
								<input type="hidden" name="slide-ids[]" value="<?php echo esc_attr($id); ?>" />
								<input type="text" name="slide-title[]" placeholder="<?php esc_html_e('Title', 'new-image-gallery'); ?>" value="<?php echo esc_attr(get_the_title($id)); ?>">
								<input type="text" name="slide-alt[]" placeholder="<?php esc_html_e('Alt Text', 'new-image-gallery'); ?>" value="<?php echo esc_attr($image_alt); ?>">

							</div>
						</li>
						<?php
						$count++;
					}
				}
				?>
			</ul>
		</div>

		<!-- Tab 2: Layout & Design -->
		<div class="awl-ig-tab-content" id="tab-layout-design">
            
            <!-- Group 1: Gallery Core Layout -->
            <div class="awl-ig-card ig-card-compact">

				<!-- Thumbnail Size -->
				<div class="awl-ig-setting-row">
					<div class="awl-ig-setting-label">
						<h4><span class="dashicons dashicons-image-filter"></span> <?php esc_html_e('Thumbnail Resolution', 'new-image-gallery'); ?></h4>
						<p><?php esc_html_e('Choose Thumbnail Resolution Size.', 'new-image-gallery'); ?></p>
					</div>
					<div class="awl-ig-setting-field">
						<?php $gal_thumb_size = isset($gallery_settings['gal_thumb_size']) ? $gallery_settings['gal_thumb_size'] : "medium"; ?>
						<select id="gal_thumb_size" name="gal_thumb_size" class="ig-select">
							<option value="thumbnail" <?php selected($gal_thumb_size, 'thumbnail'); ?>><?php esc_html_e('Thumbnail – 150 × 150', 'new-image-gallery'); ?></option>
							<option value="medium" <?php selected($gal_thumb_size, 'medium'); ?>><?php esc_html_e('Medium – 300 × 169', 'new-image-gallery'); ?></option>
							<option value="large" <?php selected($gal_thumb_size, 'large'); ?>><?php esc_html_e('Large – 840 × 473', 'new-image-gallery'); ?></option>
							<option value="full" <?php selected($gal_thumb_size, 'full'); ?>><?php esc_html_e('Full Size – Original', 'new-image-gallery'); ?></option>
						</select>
					</div>
				</div>

                <!-- Thumbnail Spacing -->
				<div class="awl-ig-setting-row">
					<div class="awl-ig-setting-label">
						<h4><span class="dashicons dashicons-editor-expand"></span> <?php esc_html_e('Thumbnail Spacing (Gap)', 'new-image-gallery'); ?></h4>
						<p><?php esc_html_e('Adjust the pixel gap between thumbnails.', 'new-image-gallery'); ?></p>
					</div>
					<div class="awl-ig-setting-field">
                        <?php 
                        $no_spacing = isset($gallery_settings['no_spacing']) ? $gallery_settings['no_spacing'] : 0;
                        ?>
                        <div class="ig-segmented-control">
                            <input type="radio" id="thumb_spacing_yes" name="no_spacing" value="0" <?php checked($no_spacing, 0); ?>>
                            <label for="thumb_spacing_yes"><?php esc_html_e('Yes', 'new-image-gallery'); ?></label>
                            
                            <input type="radio" id="thumb_spacing_no" name="no_spacing" value="1" <?php checked($no_spacing, 1); ?>>
                            <label for="thumb_spacing_no"><?php esc_html_e('No', 'new-image-gallery'); ?></label>
                        </div>
					</div>
				</div>

                <!-- Thumb Title (From Lightbox) -->
                <div class="awl-ig-setting-row">
					<div class="awl-ig-setting-label">
						<h4><span class="dashicons dashicons-editor-quote"></span> <?php esc_html_e('Thumbnail Title', 'new-image-gallery'); ?></h4>
						<p><?php esc_html_e('Titles overlaid on thumbnails.', 'new-image-gallery'); ?></p>
					</div>
					<div class="awl-ig-setting-field">
                        <?php $img_title = isset($gallery_settings['img_title']) ? $gallery_settings['img_title'] : 1; ?>
                        <div class="ig-segmented-control">
                            <input type="radio" id="img_title_yes" name="img_title" value="1" <?php checked($img_title, 1); ?>>
                            <label for="img_title_yes"><?php esc_html_e('Yes', 'new-image-gallery'); ?></label>
                            
                            <input type="radio" id="img_title_no" name="img_title" value="0" <?php checked($img_title, 0); ?>>
                            <label for="img_title_no"><?php esc_html_e('No', 'new-image-gallery'); ?></label>
                        </div>
					</div>
				</div>




            </div>

            <!-- Group 2: Responsive Columns -->
            <div class="awl-ig-card ig-card-compact">
				<!-- Columns Large Desktops -->
				<div class="awl-ig-setting-row">
					<div class="awl-ig-setting-label">
						<h4><span class="dashicons dashicons-desktop"></span> <?php esc_html_e('Gallery Columns on Screens', 'new-image-gallery'); ?></h4>
						<p><?php esc_html_e('Set columns for different device sizes.', 'new-image-gallery'); ?></p>
					</div>
					<div class="awl-ig-setting-field">
                        <div class="ig-responsive-cols">
                            <!-- X-Large -->
                            <div class="ig-col-item">
                                <label><?php esc_html_e('X-Large Screens', 'new-image-gallery'); ?></label>
                                <select id="col_large_desktops" name="col_large_desktops" class="ig-select">
                                    <?php foreach (array(1, 2, 3, 4, 6) as $i) : ?>
                                        <option value="<?php echo esc_attr($i); ?>" <?php selected($col_lg_val, $i); ?>>
                                            <?php echo ($i === 1) ? esc_html__('1 Column', 'new-image-gallery') : esc_html(sprintf(esc_html__('%d Columns', 'new-image-gallery'), (int)$i)); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Desktop -->
                            <div class="ig-col-item">
                                <label><?php esc_html_e('Desktop', 'new-image-gallery'); ?></label>
                                <select id="col_desktops" name="col_desktops" class="ig-select">
                                    <?php foreach (array(1, 2, 3, 4, 6) as $i) : ?>
                                        <option value="<?php echo esc_attr($i); ?>" <?php selected($col_md_val, $i); ?>>
                                            <?php echo ($i === 1) ? esc_html__('1 Column', 'new-image-gallery') : esc_html(sprintf(esc_html__('%d Columns', 'new-image-gallery'), (int)$i)); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Tablet -->
                            <div class="ig-col-item">
                                <label><?php esc_html_e('Tablet', 'new-image-gallery'); ?></label>
                                <select id="col_tablets" name="col_tablets" class="ig-select">
                                    <?php foreach (array(1, 2, 3, 4, 6) as $i) : ?>
                                        <option value="<?php echo esc_attr($i); ?>" <?php selected($col_sm_val, $i); ?>>
                                            <?php echo ($i === 1) ? esc_html__('1 Column', 'new-image-gallery') : esc_html(sprintf(esc_html__('%d Columns', 'new-image-gallery'), (int)$i)); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Phone -->
                            <div class="ig-col-item">
                                <label><?php esc_html_e('Phone', 'new-image-gallery'); ?></label>
                                <select id="col_phones" name="col_phones" class="ig-select">
                                    <?php foreach (array(1, 2, 3) as $i) : ?>
                                        <option value="<?php echo esc_attr($i); ?>" <?php selected($col_xs_val, $i); ?>>
                                            <?php echo ($i === 1) ? esc_html__('1 Column', 'new-image-gallery') : esc_html(sprintf(esc_html__('%d Columns', 'new-image-gallery'), (int)$i)); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
					</div>
				</div>
            </div>

            <!-- Group 4: Image Hover Animations -->
            <div class="awl-ig-card ig-card-compact">
                <!-- Hover Effect Type -->
				<div class="awl-ig-setting-row">
					<div class="awl-ig-setting-label">
						<h4><span class="dashicons dashicons-admin-appearance"></span> <?php esc_html_e('Hover Effect', 'new-image-gallery'); ?></h4>
						<p><?php esc_html_e('Choose the style for item mouseover.', 'new-image-gallery'); ?></p>
					</div>
					<div class="awl-ig-setting-field ig-flex-wrap">
                        <?php $image_hover_effect_type = isset($gallery_settings['image_hover_effect_type']) ? $gallery_settings['image_hover_effect_type'] : "sg"; ?>
                        <div class="ig-segmented-control">
                            <input type="radio" id="het_sg" name="image_hover_effect_type" value="sg" <?php checked($image_hover_effect_type, 'sg'); ?>>
                            <label for="het_sg"><?php esc_html_e('Shadows', 'new-image-gallery'); ?></label>

                            <input type="radio" id="het_no" name="image_hover_effect_type" value="no" <?php checked($image_hover_effect_type, 'no'); ?>>
                            <label for="het_no"><?php esc_html_e('None', 'new-image-gallery'); ?></label>
                        </div>

                        <div class="ig-inline-options" <?php echo ($image_hover_effect_type == 'no') ? 'style="display:none;"' : ''; ?>>
                            <span class="ig-option-label"><?php esc_html_e('OPTION:', 'new-image-gallery'); ?></span>
                            


                            <!-- Shadow Glow (Overlay) -->
                            <div class="he_four" <?php echo ($image_hover_effect_type != 'sg') ? 'style="display:none;"' : ''; ?>>
                                <?php $image_hover_effect_four = isset($gallery_settings['image_hover_effect_four']) ? $gallery_settings['image_hover_effect_four'] : "hvr-grow-shadow"; ?>
                                <select name="image_hover_effect_four" class="ig-select" id="image_hover_effect_four">
                                    <option value="hvr-grow-shadow" <?php selected($image_hover_effect_four, 'hvr-grow-shadow'); ?>><?php esc_html_e('Grow Shadow', 'new-image-gallery'); ?></option>
                                    <option value="hvr-float-shadow" <?php selected($image_hover_effect_four, 'hvr-float-shadow'); ?>><?php esc_html_e('Float Shadow', 'new-image-gallery'); ?></option>
                                    <option value="hvr-glow" <?php selected($image_hover_effect_four, 'hvr-glow'); ?>><?php esc_html_e('Glow', 'new-image-gallery'); ?></option>
                                    <option value="hvr-box-shadow-outset" <?php selected($image_hover_effect_four, 'hvr-box-shadow-outset'); ?>><?php esc_html_e('Box Shadow Outset', 'new-image-gallery'); ?></option>
                                </select>
                            </div>
                        </div>
					</div>
				</div>
            </div>

            <!-- Group 6: Logical Ordering -->
            <div class="awl-ig-card ig-card-compact">
                <!-- Order -->
                <div class="awl-ig-setting-row">
					<div class="awl-ig-setting-label">
						<h4><span class="dashicons dashicons-sort"></span> <?php esc_html_e('Automatic Sort Order', 'new-image-gallery'); ?></h4>
						<p><?php esc_html_e('Set the display order for frontend thumbnails.', 'new-image-gallery'); ?></p>
					</div>
					<div class="awl-ig-setting-field">
                        <?php $thumbnail_order = isset($gallery_settings['thumbnail_order']) ? $gallery_settings['thumbnail_order'] : "ASC"; ?>
                        <div class="ig-segmented-control">
                            <input type="radio" id="order_asc" name="thumbnail_order" value="ASC" <?php checked($thumbnail_order, 'ASC'); ?>>
                            <label for="order_asc"><?php esc_html_e('Oldest First', 'new-image-gallery'); ?></label>
                            
                            <input type="radio" id="order_desc" name="thumbnail_order" value="DESC" <?php checked($thumbnail_order, 'DESC'); ?>>
                            <label for="order_desc"><?php esc_html_e('Newest First', 'new-image-gallery'); ?></label>
                            
                            <input type="radio" id="order_rnd" name="thumbnail_order" value="RANDOM" <?php checked($thumbnail_order, 'RANDOM'); ?>>
                            <label for="order_rnd"><?php esc_html_e('Random', 'new-image-gallery'); ?></label>
                        </div>
					</div>
				</div>
            </div>

            <!-- Group 5: Right Click Protection -->
            <div class="awl-ig-card ig-card-compact">
				<div class="awl-ig-setting-row">
					<div class="awl-ig-setting-label">
						<h4><span class="dashicons dashicons-lock"></span> <?php esc_html_e('Right Click Protection', 'new-image-gallery'); ?></h4>
						<p><?php esc_html_e('If you want to disable right click and image dragging on your site, we have a dedicated plugin for it. Just install and use it.', 'new-image-gallery'); ?></p>
					</div>
					<div class="awl-ig-setting-field">
                        <?php
                        $rcb_plugin = 'right-click-disable-or-ban/right-click-disable-or-ban.php';
                        if ( ! function_exists( 'is_plugin_active' ) ) {
                            require_once ABSPATH . 'wp-admin/includes/plugin.php';
                        }
                        $is_rcb_installed = file_exists(WP_PLUGIN_DIR . '/' . $rcb_plugin);
                        $is_rcb_active = $is_rcb_installed && is_plugin_active($rcb_plugin);

                        if ($is_rcb_active) : ?>
                            <div class="rcb-status-container" style="display: flex; flex-direction: column; gap: 10px; align-items: flex-start;">
                                <div style="display: inline-flex; align-items: center; gap: 8px; background: #e0f2fe; color: #0369a1; padding: 6px 12px; border-radius: 20px; font-size: 13px; font-weight: 600;">
                                    <span class="dashicons dashicons-shield" style="font-size: 16px; width: 16px; height: 16px; margin: 0;"></span>
                                    <?php esc_html_e('Active & Protecting', 'new-image-gallery'); ?>
                                </div>
                                <a href="<?php echo esc_url(admin_url('admin.php?page=right-click-disable-or-ban-free')); ?>" class="ig-btn ig-btn-secondary">
                                    <span class="dashicons dashicons-admin-generic"></span> <?php esc_html_e('Configure Protection Settings', 'new-image-gallery'); ?>
                                </a>
                            </div>
                        <?php elseif ($is_rcb_installed) : ?>
                            <div class="rcb-status-container" style="display: flex; flex-direction: column; gap: 10px; align-items: flex-start;">
                                <div style="display: inline-flex; align-items: center; gap: 8px; background: #ffedd5; color: #c2410c; padding: 6px 12px; border-radius: 20px; font-size: 13px; font-weight: 600;">
                                    <span class="dashicons dashicons-warning" style="font-size: 16px; width: 16px; height: 16px; margin: 0;"></span>
                                    <?php esc_html_e('Installed (Inactive)', 'new-image-gallery'); ?>
                                </div>
                                <a href="<?php echo esc_url(wp_nonce_url(self_admin_url('plugins.php?action=activate&plugin=' . $rcb_plugin), 'activate-plugin_' . $rcb_plugin)); ?>" class="ig-btn ig-btn-primary">
                                    <span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Activate Right Click Ban', 'new-image-gallery'); ?>
                                </a>
                            </div>
                        <?php else : ?>
                            <div class="rcb-status-container" style="display: flex; flex-direction: column; gap: 10px; align-items: flex-start;">
                                <div style="display: inline-flex; align-items: center; gap: 8px; background: #f1f5f9; color: #475569; padding: 6px 12px; border-radius: 20px; font-size: 13px; font-weight: 600;">
                                    <span class="dashicons dashicons-info" style="font-size: 16px; width: 16px; height: 16px; margin: 0;"></span>
                                    <?php esc_html_e('Recommended Plugin', 'new-image-gallery'); ?>
                                </div>
                                <a href="<?php echo esc_url(wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=right-click-disable-or-ban'), 'install-plugin_right-click-disable-or-ban')); ?>" class="ig-btn ig-btn-primary">
                                    <span class="dashicons dashicons-download"></span> <?php esc_html_e('Install Free Right Click Ban Plugin', 'new-image-gallery'); ?>
                                </a>
                            </div>
                        <?php endif; ?>
					</div>
				</div>
            </div>
		</div>

		<!-- Tab 3: Lightbox -->
		<div class="awl-ig-tab-content" id="tab-lightbox-links">
            <div class="awl-ig-card ig-card-compact">
				<!-- Lightbox Script -->
				<div class="awl-ig-setting-row">
					<div class="awl-ig-setting-label">
						<h4><span class="dashicons dashicons-visibility"></span> <?php esc_html_e('Active Lightbox Script', 'new-image-gallery'); ?></h4>
						<p><?php esc_html_e('Select the script core for the image popup.', 'new-image-gallery'); ?></p>
					</div>
					<div class="awl-ig-setting-field">
						<?php 
						$light_box = isset($gallery_settings['light-box']) ? (int)$gallery_settings['light-box'] : 1;
						if ($light_box != 0) {
						    $light_box = 1;
						}
						?>
						<select name="light-box" id="light-box" class="ig-select">
							<option value="0" <?php selected($light_box, 0); ?>><?php esc_html_e('None (Disable Popup)', 'new-image-gallery'); ?></option>
							<option value="1" <?php selected($light_box, 1); ?>><?php esc_html_e('LD Lightbox', 'new-image-gallery'); ?></option>
						</select>
					</div>
				</div>



				<!-- Loop Images in Lightbox -->
				<div class="awl-ig-setting-row">
					<div class="awl-ig-setting-label">
						<h4><span class="dashicons dashicons-update"></span> <?php esc_html_e('Loop Images in Lightbox', 'new-image-gallery'); ?></h4>
						<p><?php esc_html_e('Allow navigation to restart from the beginning after the last image.', 'new-image-gallery'); ?></p>
					</div>
					<div class="awl-ig-setting-field">
						<div class="ig-segmented-control">
							<input type="radio" id="lb_loop_yes" name="show_lightbox_loop" value="1" <?php checked($gallery_settings['show_lightbox_loop'], 1); ?>>
							<label for="lb_loop_yes"><?php esc_html_e('Yes', 'new-image-gallery'); ?></label>
							
							<input type="radio" id="lb_loop_no" name="show_lightbox_loop" value="0" <?php checked($gallery_settings['show_lightbox_loop'], 0); ?>>
							<label for="lb_loop_no"><?php esc_html_e('No', 'new-image-gallery'); ?></label>
						</div>
					</div>
				</div>
                
                


            </div>
		</div>

		<!-- Tab 4: Upgrade to Pro -->
		<div class="awl-ig-tab-content" id="tab-upgrade-pro">
			<div class="ig-pro-upgrade-container">
				<!-- Header section -->
				<div class="ig-pro-header">
					<div class="ig-pro-badge"><?php esc_html_e('PREMIUM FEATURES', 'new-image-gallery'); ?></div>
					<h2><?php esc_html_e('Experience the Best with Pro Version', 'new-image-gallery'); ?></h2>
					<p><?php esc_html_e('Take your image galleries to the next level with advanced features, powerful tools, and priority support.', 'new-image-gallery'); ?></p>
					
					<!-- Top Buy & Demo Buttons -->
					<div class="ig-pro-top-cta" style="margin-top: 25px; display: flex; justify-content: center; gap: 15px; flex-wrap: wrap;">
						<a href="https://awplife.com/wordpress-plugins/image-gallery-premium/" target="_blank" class="ig-btn ig-btn-premium lg">
							<span class="dashicons dashicons-cart"></span> <?php esc_html_e('Get the Pro Version Now', 'new-image-gallery'); ?>
						</a>
						<a href="https://awplife.com/demo/image-gallery-premium/" target="_blank" class="ig-btn ig-btn-secondary lg">
							<span class="dashicons dashicons-welcome-view-site"></span> <?php esc_html_e('Check Live Demo', 'new-image-gallery'); ?>
						</a>
					</div>
				</div>

				<!-- Feature Grid -->
				<div class="ig-pro-grid">
					<div class="ig-pro-feature-card">
						<div class="ig-pro-icon"><span class="dashicons dashicons-admin-page"></span></div>
						<h3><?php esc_html_e('Duplicate Gallery', 'new-image-gallery'); ?></h3>
						<p><?php esc_html_e('Clone any gallery instantly with its settings and images. Perfect for repetitive layouts.', 'new-image-gallery'); ?></p>
					</div>
					<div class="ig-pro-feature-card">
						<div class="ig-pro-icon"><span class="dashicons dashicons-clock"></span></div>
						<h3><?php esc_html_e('AJAX Load More', 'new-image-gallery'); ?></h3>
						<p><?php esc_html_e('Enhance performance with high-speed AJAX pagination, featuring stunning professional Solid, Outline, Glass, Neon, and Gradient button presets.', 'new-image-gallery'); ?></p>
					</div>
					<div class="ig-pro-feature-card">
						<div class="ig-pro-icon"><span class="dashicons dashicons-images-alt2"></span></div>
						<h3><?php esc_html_e('WebP Optimization Engine', 'new-image-gallery'); ?></h3>
						<p><?php esc_html_e('Automatically convert and serve gallery images in WebP format site-wide for significantly faster page load speeds and bandwidth optimization.', 'new-image-gallery'); ?></p>
					</div>
					<div class="ig-pro-feature-card">
						<div class="ig-pro-icon"><span class="dashicons dashicons-layout"></span></div>
						<h3><?php esc_html_e('Masonry, Grid & Circle Layouts', 'new-image-gallery'); ?></h3>
						<p><?php esc_html_e('Unlock the uniform grid and circle layouts. Grid mode includes 6 aspect ratio presets (1:1 Square, 4:3 Landscape, 16:9 Cinema, etc.).', 'new-image-gallery'); ?></p>
					</div>
					<div class="ig-pro-feature-card">
						<div class="ig-pro-icon"><span class="dashicons dashicons-admin-users"></span></div>
						<h3><?php esc_html_e('Custom Profile Header', 'new-image-gallery'); ?></h3>
						<p><?php esc_html_e('Display standard, centered, or compact branding headers on top of your gallery with avatar upload, bio tag editor, and statistics.', 'new-image-gallery'); ?></p>
					</div>
					<div class="ig-pro-feature-card">
						<div class="ig-pro-icon"><span class="dashicons dashicons-welcome-view-site"></span></div>
						<h3><?php esc_html_e('6 Premium Lightbox Scripts', 'new-image-gallery'); ?></h3>
						<p><?php esc_html_e('Integrate G Lightbox, PhotoSwipe v4, Modal, Viewer, Blue Imp, and LD Lightbox. Show title, description, and configure navigation loops.', 'new-image-gallery'); ?></p>
					</div>
					<div class="ig-pro-feature-card">
						<div class="ig-pro-icon"><span class="dashicons dashicons-admin-links"></span></div>
						<h3><?php esc_html_e('Custom Slide Link URLs', 'new-image-gallery'); ?></h3>
						<p><?php esc_html_e('Attach custom hyperlinks to individual images to redirect visitors to specific posts, pages, or external URLs with tab target control.', 'new-image-gallery'); ?></p>
					</div>
					<div class="ig-pro-feature-card">
						<div class="ig-pro-icon"><span class="dashicons dashicons-desktop"></span></div>
						<h3><?php esc_html_e('16-Column Ultra-Wide Support', 'new-image-gallery'); ?></h3>
						<p><?php esc_html_e('Unlock the missing 5, 7, and 8 to 16 column options for perfect balance on high-resolution and ultra-wide displays.', 'new-image-gallery'); ?></p>
					</div>
					<div class="ig-pro-feature-card">
						<div class="ig-pro-icon"><span class="dashicons dashicons-forms"></span></div>
						<h3><?php esc_html_e('Advanced Border & Spacing', 'new-image-gallery'); ?></h3>
						<p><?php esc_html_e('Fine-tune card aesthetics. Adjust corner radius (0-100px), card background colors, border thickness, border opacity, and shadow effects.', 'new-image-gallery'); ?></p>
					</div>
				</div>

				<!-- Comparison Table -->
				<div class="ig-pro-comparison">
					<h3><?php esc_html_e('Compare Versions', 'new-image-gallery'); ?></h3>
					<table class="ig-comparison-table">
						<thead>
							<tr>
								<th><?php esc_html_e('Feature', 'new-image-gallery'); ?></th>
								<th><?php esc_html_e('Free Version', 'new-image-gallery'); ?></th>
								<th><?php esc_html_e('Pro Version', 'new-image-gallery'); ?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><?php esc_html_e('Available Columns', 'new-image-gallery'); ?></td>
								<td><?php esc_html_e('1, 2, 3, 4, 6 Columns', 'new-image-gallery'); ?></td>
								<td><?php esc_html_e('1 to 16 Columns', 'new-image-gallery'); ?></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Layout Modes', 'new-image-gallery'); ?></td>
								<td><?php esc_html_e('Masonry', 'new-image-gallery'); ?></td>
								<td><?php esc_html_e('Masonry, Grid, Circle', 'new-image-gallery'); ?></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Grid Image Aspect Ratios', 'new-image-gallery'); ?></td>
								<td><span class="dashicons dashicons-no-alt" style="color: #ef4444;"></span></td>
								<td><?php esc_html_e('Yes (6 Presets)', 'new-image-gallery'); ?></td>
							</tr>
							<tr>
								<td><?php esc_html_e('AJAX Load More', 'new-image-gallery'); ?></td>
								<td><span class="dashicons dashicons-no-alt" style="color: #ef4444;"></span></td>
								<td><span class="dashicons dashicons-yes" style="color: #10b981;"></span> <?php esc_html_e('Yes (5 Button Style Presets)', 'new-image-gallery'); ?></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Gallery Loading Icon', 'new-image-gallery'); ?></td>
								<td><span class="dashicons dashicons-no-alt" style="color: #ef4444;"></span></td>
								<td><span class="dashicons dashicons-yes" style="color: #10b981;"></span></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Duplicate Gallery', 'new-image-gallery'); ?></td>
								<td><span class="dashicons dashicons-no-alt" style="color: #ef4444;"></span></td>
								<td><span class="dashicons dashicons-yes" style="color: #10b981;"></span></td>
							</tr>
							
							<tr>
								<td><?php esc_html_e('Advanced Border & Spacing Control', 'new-image-gallery'); ?></td>
								<td><span class="dashicons dashicons-no-alt" style="color: #ef4444;"></span></td>
								<td><span class="dashicons dashicons-yes" style="color: #10b981;"></span> <?php esc_html_e('Yes (Thickness, Radius & Card Colors)', 'new-image-gallery'); ?></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Instagram Style Profile Header', 'new-image-gallery'); ?></td>
								<td><span class="dashicons dashicons-no-alt" style="color: #ef4444;"></span></td>
								<td><?php esc_html_e('Yes (3 Layouts, Bio Editor & Statistics)', 'new-image-gallery'); ?></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Custom Slide Link URLs', 'new-image-gallery'); ?></td>
								<td><span class="dashicons dashicons-no-alt" style="color: #ef4444;"></span></td>
								<td><span class="dashicons dashicons-yes" style="color: #10b981;"></span></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Custom CSS Field', 'new-image-gallery'); ?></td>
								<td><span class="dashicons dashicons-no-alt" style="color: #ef4444;"></span></td>
								<td><span class="dashicons dashicons-yes" style="color: #10b981;"></span></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Global Settings Page', 'new-image-gallery'); ?></td>
								<td><span class="dashicons dashicons-no-alt" style="color: #ef4444;"></span></td>
								<td><span class="dashicons dashicons-yes" style="color: #10b981;"></span> <?php esc_html_e('Yes (Lazy Load, WebP, Skeleton, Loaders, Backup Import/Export)', 'new-image-gallery'); ?></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Image Order', 'new-image-gallery'); ?></td>
								<td><span class="dashicons dashicons-yes" style="color: #10b981;"></span></td>
								<td><span class="dashicons dashicons-yes" style="color: #10b981;"></span></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Image Hover Effects', 'new-image-gallery'); ?></td>
								<td><?php esc_html_e('Basic', 'new-image-gallery'); ?></td>
								<td><?php esc_html_e('Advance (2D, Shadow, Glow & Overlay)', 'new-image-gallery'); ?></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Lightbox Scripts', 'new-image-gallery'); ?></td>
								<td><?php esc_html_e('1 Type', 'new-image-gallery'); ?></td>
								<td><?php esc_html_e('6 Types (G Lightbox, PhotoSwipe, etc.)', 'new-image-gallery'); ?></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Lightbox Image', 'new-image-gallery'); ?></td>
								<td><span class="dashicons dashicons-yes" style="color: #10b981;"></span></td>
								<td><span class="dashicons dashicons-yes" style="color: #10b981;"></span></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Lightbox Image Description', 'new-image-gallery'); ?></td>
								<td><span class="dashicons dashicons-no-alt" style="color: #ef4444;"></span></td>
								<td><span class="dashicons dashicons-yes" style="color: #10b981;"></span></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Title Positioning', 'new-image-gallery'); ?></td>
								<td><?php esc_html_e('On Image only', 'new-image-gallery'); ?></td>
								<td><?php esc_html_e('Hover & Below Image', 'new-image-gallery'); ?></td>
							</tr>
						</tbody>
					</table>
				</div>

				<!-- Footer CTA -->
				<div class="ig-pro-cta" style="display: flex; flex-direction: column; align-items: center; gap: 15px;">
					<div style="display: flex; justify-content: center; gap: 15px; flex-wrap: wrap;">
						<a href="https://awplife.com/wordpress-plugins/image-gallery-premium/" target="_blank" class="ig-btn ig-btn-premium lg">
							<span class="dashicons dashicons-cart"></span> <?php esc_html_e('Grab Image Gallery Pro Now!', 'new-image-gallery'); ?>
						</a>
						<a href="https://awplife.com/demo/image-gallery-premium/" target="_blank" class="ig-btn ig-btn-secondary lg">
							<span class="dashicons dashicons-welcome-view-site"></span> <?php esc_html_e('Check Live Demo', 'new-image-gallery'); ?>
						</a>
					</div>
					<p style="margin: 0;"><?php esc_html_e('One-time payment. Lifetime updates. 100% Satisfaction.', 'new-image-gallery'); ?></p>
				</div>
			</div>
		</div>



	</div>
</div>

<?php wp_nonce_field('ig_save_settings', 'igp_save_nonce'); ?>