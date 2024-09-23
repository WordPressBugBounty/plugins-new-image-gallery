<?php
// toggle button CSS
wp_enqueue_style('nig-admin-bootstrap-css', IG_PLUGIN_URL . 'assets/css/admin-bootstrap.css');
wp_enqueue_style('nig-toogle-button-css', IG_PLUGIN_URL . 'assets/css/toogle-button.css');
wp_enqueue_style('nig-metabox-css', IG_PLUGIN_URL . 'assets/css/metabox.css');
// js
wp_enqueue_script('jquery');
wp_enqueue_script('nig-bootstrap-js', IG_PLUGIN_URL . 'assets/js/bootstrap.min.js', array('jquery'), '', false);

?>
<style>
	.col-1,
	.col-2,
	.col-3,
	.col-4,
	.col-5,
	.col-6,
	.col-7,
	.col-8,
	.col-9,
	.col-10,
	.col-11,
	.col-12,
	.col,
	.col-auto,
	.col-sm-1,
	.col-sm-2,
	.col-sm-3,
	.col-sm-4,
	.col-sm-5,
	.col-sm-6,
	.col-sm-7,
	.col-sm-8,
	.col-sm-9,
	.col-sm-10,
	.col-sm-11,
	.col-sm-12,
	.col-sm,
	.col-sm-auto,
	.col-md-1,
	.col-md-2,
	.col-md-3,
	.col-md-4,
	.col-md-5,
	.col-md-6,
	.col-md-7,
	.col-md-8,
	.col-md-9,
	.col-md-10,
	.col-md-11,
	.col-md-12,
	.col-md,
	.col-md-auto,
	.col-lg-1,
	.col-lg-2,
	.col-lg-3,
	.col-lg-4,
	.col-lg-5,
	.col-lg-6,
	.col-lg-7,
	.col-lg-8,
	.col-lg-9,
	.col-lg-10,
	.col-lg-11,
	.col-lg-12,
	.col-lg,
	.col-lg-auto,
	.col-xl-1,
	.col-xl-2,
	.col-xl-3,
	.col-xl-4,
	.col-xl-5,
	.col-xl-6,
	.col-xl-7,
	.col-xl-8,
	.col-xl-9,
	.col-xl-10,
	.col-xl-11,
	.col-xl-12,
	.col-xl,
	.col-xl-auto {
		float: left;
	}

	#comment-link-box,
	#edit-slug-box {
		display: none;
	}
</style>
<div class="row">
	<div class="col-lg-12 bhoechie-tab-container">
		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 bhoechie-tab-menu">
			<div class="list-group">
				<a href="#" class="list-group-item active text-center">
					<span class="dashicons dashicons-editor-table"></span><br />
					<?php esc_html_e('Add Images', 'new-image-gallery'); ?>
				</a>
				<a href="#" class="list-group-item text-center">
					<span class="dashicons dashicons-admin-generic"></span><br />
					<?php esc_html_e('Configure', 'new-image-gallery'); ?>
				</a>
				<a href="#" class="list-group-item text-center">
					<span class="dashicons dashicons-admin-appearance"></span><br />
					<?php esc_html_e('Animation Effect', 'new-image-gallery'); ?>
				</a>
				<a href="#" class="list-group-item text-center">
					<span class="dashicons dashicons-admin-customizer"></span><br />
					<?php esc_html_e('LightBox Settings', 'new-image-gallery'); ?>
				</a>
				<a href="#" class="list-group-item text-center">
					<span class="dashicons dashicons-editor-code"></span><br />
					<?php esc_html_e('Custom Css', 'new-image-gallery'); ?>
				</a>
				<a href="#" class="list-group-item text-center">
					<span class="dashicons dashicons-cart"></span><br />
					<?php esc_html_e('Upgrade To Pro', 'new-image-gallery'); ?>
				</a>
			</div>
		</div>
		<div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 bhoechie-tab">
			<div class="bhoechie-tab-content active">
				<h1>
					<?php esc_html_e('Add Images', 'new-image-gallery'); ?>
					<?php wp_nonce_field( 'igp_add_images', 'igp_add_images_nonce' ); ?>
				</h1>
				<hr>
				<div id="slider-gallery">
					<input type="button" id="remove-all-slides" name="remove-all-slides"
						class="button button-large remove-all-slides" rel=""
						value="<?php esc_html_e('Delete All Images', 'new-image-gallery'); ?>">
					<ul id="remove-slides" class="sbox">
						<?php
						$post_id = esc_attr($post->ID);

						if (!function_exists('is_sr_serialized')) {
							function is_sr_serialized($str) {
								return ($str == serialize(false) || @unserialize($str) !== false);
							}
						}

						// Retrieve the base64 encoded data
						$encodedData = get_post_meta($post_id, 'awl_ig_settings_' . $post_id, true);

						// Decode the base64 encoded data
						$decodedData = base64_decode($encodedData);

						// Check if the data is serialized
						if (is_sr_serialized($decodedData)) {

							// The data is serialized, so unserialize it
							$gallery_settings = unserialize($decodedData);
							// Optionally, convert the unserialized data to JSON and save it back in base64 encoding for future access
							// This step is optional but recommended to transition your data format
						
							$jsonEncodedData = json_encode($gallery_settings);
							update_post_meta($post_id, 'awl_ig_settings_' . $post_id, $jsonEncodedData);

							// Now, to use the newly saved format, fetch and decode again
							$encodedData = get_post_meta($post_id, 'awl_ig_settings_' . $post_id, true);
							$gallery_settings = json_decode(($encodedData), true);

						} else {
							// Assume the data is in JSON format
							$jsonData = get_post_meta($post_id, 'awl_ig_settings_' . $post_id, true);
							// Decode the JSON string into an associative array
							$gallery_settings = json_decode($jsonData, true); // Ensure true is passed to get an associative array
						}
						if (isset($gallery_settings['slide-ids'])) {
							$count = 0;
							foreach ($gallery_settings['slide-ids'] as $id) {
								$image_alt = $gallery_settings['slide-alt'][$count];
								$thumbnail = wp_get_attachment_image_src($id, 'medium', true);
								$attachment = get_post($id);
								?>
								<li class="slide">
									<img class="new-slide" src="<?php echo esc_url($thumbnail[0]); ?>"
										alt="<?php echo esc_html(get_the_title($id)); ?>"
										style="height: 150px; width: 98%; border-radius: 8px;">
									<input type="hidden" id="slide-ids[]" name="slide-ids[]"
										value="<?php echo esc_attr($id); ?>" />
									<input type="text" name="slide-title[]" id="slide-title[]" style="width: 98%;"
										placeholder="<?php esc_html_e('Image Title', 'new-image-gallery'); ?>"
										value="<?php echo esc_html(get_the_title($id)); ?>">
									<input type="text" name="slide-alt[]" id="slide-alt[]" style="width: 98%;"
										placeholder="<?php esc_html_e('Image Alt', 'new-image-gallery'); ?>"
										value="<?php echo esc_html($image_alt); ?>">
									<a class="pw-trash-icon" name="remove-slide" id="remove-slide" href="#"><span
											class="dashicons dashicons-trash"></span></a>
								</li>
								<?php
								$count++;
							} // end of foreach
						} //end of if
						?>
					</ul>
				</div>
			</div>

			<div class="bhoechie-tab-content">
				<h1>
					<?php esc_html_e('profile settings', 'new-image-gallery'); ?>
				</h1>
				<hr>

				<div class="col-md-4">
					<div class="ma_field_discription">
						<h6>
							<?php esc_html_e('Gallery Thumbnail Size', 'new-image-gallery'); ?>
						</h6>
						<p>
							<?php esc_html_e('Select gallery thumbnails size to display into gallery', 'new-image-gallery'); ?>
						</p>
					</div>
				</div>
				<div class="col-md-8">
					<div class="ma_field p-4">
						<?php
						if (isset($gallery_settings['gal_thumb_size'])) {
							$gal_thumb_size = $gallery_settings['gal_thumb_size'];
						} else {
							$gal_thumb_size = 'thumbnail';
						}
						?>
						<select id="gal_thumb_size" name="gal_thumb_size" style="width:50%">
							<option value="thumbnail" <?php
							if ($gal_thumb_size == 'thumbnail') {
								echo 'selected=selected';
							}
							?>>
								<?php esc_html_e('Thumbnail – 150 × 150', 'new-image-gallery'); ?>
							</option>
							<option value="medium" <?php
							if ($gal_thumb_size == 'medium') {
								echo 'selected=selected';
							}
							?>>
								<?php esc_html_e('Medium – 300 × 169', 'new-image-gallery'); ?>
							</option>
							<option value="large" <?php
							if ($gal_thumb_size == 'large') {
								echo 'selected=selected';
							}
							?>>
								<?php esc_html_e('Large – 840 × 473', 'new-image-gallery'); ?>
							</option>
							<option value="full" <?php
							if ($gal_thumb_size == 'full') {
								echo 'selected=selected';
							}
							?>>
								<?php esc_html_e('Full Size – 1280 × 720', 'new-image-gallery'); ?>
							</option>
						</select>
					</div>
				</div>
				<div class="col-md-4">
					<div class="ma_field_discription">
						<h6>
							<?php esc_html_e('Columns On Large Desktops', 'new-image-gallery'); ?>
						</h6>
						<p>
							<?php esc_html_e('Select gallery column layout for large desktop devices', 'new-image-gallery'); ?>
						</p>
					</div>
				</div>
				<div class="col-md-8">
					<div class="ma_field p-4">
						<?php
						if (isset($gallery_settings['col_large_desktops'])) {
							$col_large_desktops = $gallery_settings['col_large_desktops'];
						} else {
							$col_large_desktops = 'col-lg-2';
						}
						?>
						<select id="col_large_desktops" name="col_large_desktops" style="width:40%">
							<option value="col-lg-12" <?php
							if ($col_large_desktops == 'col-lg-12') {
								echo 'selected=selected';
							}
							?>>
								<?php esc_html_e('1 Column', 'new-image-gallery'); ?>
							</option>
							<option value="col-lg-6" <?php
							if ($col_large_desktops == 'col-lg-6') {
								echo 'selected=selected';
							}
							?>>
								<?php esc_html_e('2 Column', 'new-image-gallery'); ?>
							</option>
							<option value="col-lg-4" <?php
							if ($col_large_desktops == 'col-lg-4') {
								echo 'selected=selected';
							}
							?>>
								<?php esc_html_e('3 Column', 'new-image-gallery'); ?>
							</option>
							<option value="col-lg-3" <?php
							if ($col_large_desktops == 'col-lg-3') {
								echo 'selected=selected';
							}
							?>>
								<?php esc_html_e('4 Column', 'new-image-gallery'); ?>
							</option>
							<option value="col-lg-2" <?php
							if ($col_large_desktops == 'col-lg-2') {
								echo 'selected=selected';
							}
							?>>
								<?php esc_html_e('6 Column', 'new-image-gallery'); ?>
							</option>
							<option value="col-lg-1" <?php
							if ($col_large_desktops == 'col-lg-1') {
								echo 'selected=selected';
							}
							?>>
								<?php esc_html_e('12 Column', 'new-image-gallery'); ?>
							</option>
						</select>
					</div>
				</div>

				<div class="col-md-4">
					<div class="ma_field_discription">
						<h6>
							<?php esc_html_e('Columns On Desktops', 'new-image-gallery'); ?>
						</h6>
						<p>
							<?php esc_html_e('Select gallery column layout for desktop devices', 'new-image-gallery'); ?>
						</p>
					</div>
				</div>
				<div class="col-md-8">
					<div class="ma_field p-4">
						<?php
						if (isset($gallery_settings['col_desktops'])) {
							$col_desktops = $gallery_settings['col_desktops'];
						} else {
							$col_desktops = 'col-md-3';
						}
						?>
						<select id="col_desktops" name="col_desktops" style="width:40%">
							<option value="col-md-12" <?php
							if ($col_desktops == 'col-md-12') {
								echo 'selected=selected';
							}
							?>>
								<?php esc_html_e('1 Column', 'new-image-gallery'); ?>
							</option>
							<option value="col-md-6" <?php
							if ($col_desktops == 'col-md-6') {
								echo 'selected=selected';
							}
							?>>
								<?php esc_html_e('2 Column', 'new-image-gallery'); ?>
							</option>
							<option value="col-md-4" <?php
							if ($col_desktops == 'col-md-4') {
								echo 'selected=selected';
							}
							?>>
								<?php esc_html_e('3 Column', 'new-image-gallery'); ?>
							</option>
							<option value="col-md-3" <?php
							if ($col_desktops == 'col-md-3') {
								echo 'selected=selected';
							}
							?>>
								<?php esc_html_e('4 Column', 'new-image-gallery'); ?>
							</option>
							<option value="col-md-2" <?php
							if ($col_desktops == 'col-md-2') {
								echo 'selected=selected';
							}
							?>>
								<?php esc_html_e('6 Column', 'new-image-gallery'); ?>
							</option>
							<option value="col-md-1" <?php
							if ($col_desktops == 'col-md-1') {
								echo 'selected=selected';
							}
							?>>
								<?php esc_html_e('12 Column', 'new-image-gallery'); ?>
							</option>
						</select>
					</div>
				</div>
				<div class="col-md-4">
					<div class="ma_field_discription">
						<h6>
							<?php esc_html_e('Columns On Tablets', 'new-image-gallery'); ?>
						</h6>
						<p>
							<?php esc_html_e('Select gallery column layout for tablet devices', 'new-image-gallery'); ?>
						</p>
					</div>
				</div>
				<div class="col-md-8">
					<div class="ma_field p-4">
						<?php
						if (isset($gallery_settings['col_tablets'])) {
							$col_tablets = $gallery_settings['col_tablets'];
						} else {
							$col_tablets = 'col-sm-4';
						}
						?>
						<select id="col_tablets" name="col_tablets" style="width:40%">
							<option value="col-sm-12" <?php
							if ($col_tablets == 'col-sm-12') {
								echo 'selected=selected';
							}
							?>>
								<?php esc_html_e('1 Column', 'new-image-gallery'); ?>
							</option>
							<option value="col-sm-6" <?php
							if ($col_tablets == 'col-sm-12') {
								echo 'selected=selected';
							}
							?>>
								<?php esc_html_e('2 Column', 'new-image-gallery'); ?>
							</option>
							<option value="col-sm-4" <?php
							if ($col_tablets == 'col-sm-4') {
								echo 'selected=selected';
							}
							?>>
								<?php esc_html_e('3 Column', 'new-image-gallery'); ?>
							</option>
							<option value="col-sm-3" <?php
							if ($col_tablets == 'col-sm-3') {
								echo 'selected=selected';
							}
							?>>
								<?php esc_html_e('4 Column', 'new-image-gallery'); ?>
							</option>
							<option value="col-sm-2" <?php
							if ($col_tablets == 'col-sm-2') {
								echo 'selected=selected';
							}
							?>>
								<?php esc_html_e('6 Column', 'new-image-gallery'); ?>
							</option>
						</select>
					</div>
				</div>
				<div class="col-md-4">
					<div class="ma_field_discription">
						<h6>
							<?php esc_html_e('Colums On Phones', 'new-image-gallery'); ?>
						</h6>
						<p>
							<?php esc_html_e('Select gallery column layout for phone devices', 'new-image-gallery'); ?>
						</p>
					</div>
				</div>
				<div class="col-md-8">
					<div class="ma_field p-4">
						<?php
						if (isset($gallery_settings['col_phones'])) {
							$col_phones = $gallery_settings['col_phones'];
						} else {
							$col_phones = 'col-6';
						}
						?>
						<select id="col_phones" name="col_phones" style="width:40%">
							<option value="col-12" <?php
							if ($col_phones == 'col-12') {
								echo 'selected=selected';
							}
							?>>
								<?php esc_html_e('1 Column', 'new-image-gallery'); ?>
							</option>
							<option value="col-6" <?php
							if ($col_phones == 'col-6') {
								echo 'selected=selected';
							}
							?>>
								<?php esc_html_e('2 Column', 'new-image-gallery'); ?>
							</option>
							<option value="col-4" <?php
							if ($col_phones == 'col-4') {
								echo 'selected=selected';
							}
							?>>
								<?php esc_html_e('3 Column', 'new-image-gallery'); ?>
							</option>
							<option value="col-3" <?php
							if ($col_phones == 'col-3') {
								echo 'selected=selected';
							}
							?>>
								<?php esc_html_e('4 Column', 'new-image-gallery'); ?>
							</option>
						</select>
					</div>
				</div>
				<div class="col-md-4">
					<div class="ma_field_discription">
						<h6>
							<?php esc_html_e('Hide Thumbnails Title', 'new-image-gallery'); ?>
						</h6>
						<p>
							<?php esc_html_e('Hide Thumbnails Title Yes / No', 'new-image-gallery'); ?>
						</p>
					</div>
				</div>
				<div class="col-md-8">
					<div class="ma_field p-4 switch-field em_size_field">
						<?php
						if (isset($gallery_settings['img_title'])) {
							$img_title = $gallery_settings['img_title'];
						} else {
							$img_title = 0;
						}
						?>
						<input type="radio" name="img_title" id="img_title1" value="1" <?php
						if ($img_title == 1) {
							echo 'checked=checked';
						}
						?>>
						<label for="img_title1">
							<?php esc_html_e('Yes', 'new-image-gallery'); ?>
						</label>
						<input type="radio" name="img_title" id="img_title2" value="0" <?php
						if ($img_title == 0) {
							echo 'checked=checked';
						}
						?>>
						<label for="img_title2">
							<?php esc_html_e('No', 'new-image-gallery'); ?>
						</label>
					</div>
				</div>
				<div class="col-md-4">
					<div class="ma_field_discription">
						<h6>
							<?php esc_html_e('Hide Thumbnails Spacing', 'new-image-gallery'); ?>
						</h6>
						<p>
							<?php esc_html_e('Hide gap / margin / padding / spacing between gallery', 'new-image-gallery'); ?>
						</p>
					</div>
				</div>
				<div class="col-md-8">
					<div class="ma_field p-4 switch-field em_size_field">
						<?php
						if (isset($gallery_settings['no_spacing'])) {
							$no_spacing = $gallery_settings['no_spacing'];
						} else {
							$no_spacing = 0;
						}
						?>
						<input type="radio" name="no_spacing" id="no_spacing1" value="1" <?php
						if ($no_spacing == 1) {
							echo 'checked=checked';
						}
						?>>
						<label for="no_spacing1">
							<?php esc_html_e('Yes', 'new-image-gallery'); ?>
						</label>
						<input type="radio" name="no_spacing" id="no_spacing2" value="0" <?php
						if ($no_spacing == 0) {
							echo 'checked=checked';
						}
						?>>
						<label for="no_spacing2">
							<?php esc_html_e('No', 'new-image-gallery'); ?>
						</label>
					</div>
				</div>
				<div class="col-md-4">
					<div class="ma_field_discription">
						<h6>
							<?php esc_html_e('Gallery Thumbnail Order', 'new-image-gallery'); ?>
						</h6>
						<p>
							<?php esc_html_e('Set a image order in which you want to display gallery thumbnails', 'new-image-gallery'); ?>
						</p>
					</div>
				</div>
				<div class="col-md-8">
					<div class="ma_field p-4 switch-field em_size_field">
						<?php
						if (isset($gallery_settings['thumbnail_order'])) {
							$thumbnail_order = $gallery_settings['thumbnail_order'];
						} else {
							$thumbnail_order = 'ASC';
						}
						?>
						<input type="radio" name="thumbnail_order" id="thumbnail_order1" value="ASC" <?php
						if ($thumbnail_order == 'ASC') {
							echo 'checked=checked';
						}
						?>>
						<label for="thumbnail_order1">
							<?php esc_html_e('Old First', 'new-image-gallery'); ?>
						</label>
						<input type="radio" name="thumbnail_order" id="thumbnail_order2" value="DESC" <?php
						if ($thumbnail_order == 'DESC') {
							echo 'checked=checked';
						}
						?>>
						<label for="thumbnail_order2">
							<?php esc_html_e('New First', 'new-image-gallery'); ?>
						</label>
						<input type="radio" name="thumbnail_order" id="thumbnail_order3" value="RANDOM" <?php
						if ($thumbnail_order == 'RANDOM') {
							echo 'checked=checked';
						}
						?>>
						<label for="thumbnail_order3">
							<?php esc_html_e('Random', 'new-image-gallery'); ?>
						</label>
					</div>
				</div>
				<div class="col-md-4">
					<div class="ma_field_discription">
						<h6>
							<?php esc_html_e('Image Loop', 'new-image-gallery'); ?>
						</h6>
						<p>
							<?php esc_html_e('Image Looping In Lightbox True & False', 'new-image-gallery'); ?>
						</p>
					</div>
				</div>
				<div class="col-md-8">
					<div class="ma_field p-4 switch-field em_size_field">
						<?php
						if (isset($gallery_settings['igp_loop_st'])) {
							$igp_loop_st = $gallery_settings['igp_loop_st'];
						} else {
							$igp_loop_st = 'false';
						}
						?>
						<input type="radio" name="igp_loop_st" id="igp_loop_st1" value="true" <?php
						if ($igp_loop_st == 'true') {
							echo 'checked=checked';
						}
						?>>
						<label for="igp_loop_st1">
							<?php esc_html_e('Yes', 'new-image-gallery'); ?>
						</label>
						<input type="radio" name="igp_loop_st" id="igp_loop_st2" value="false" <?php
						if ($igp_loop_st == 'false') {
							echo 'checked=checked';
						}
						?>>
						<label for="igp_loop_st2">
							<?php esc_html_e('No', 'new-image-gallery'); ?>
						</label>
					</div>
				</div>
			</div>

			<div class="bhoechie-tab-content">
				<h1>
					<?php esc_html_e('Image Hover Effect Type', 'new-image-gallery'); ?>
				</h1>
				<hr>

				<div class="col-md-4">
					<div class="ma_field_discription">
						<h6>
							<?php esc_html_e('Hover Effect Type', 'new-image-gallery'); ?>
						</h6>
						<p>
							<?php esc_html_e('Select and Set a image hover effect type for Gallery', 'new-image-gallery'); ?>
						</p>
					</div>
				</div>
				<div class="col-md-8">
					<div class="ma_field p-4 switch-field em_size_field">
						<?php
						if (isset($gallery_settings['image_hover_effect_type'])) {
							$image_hover_effect_type = $gallery_settings['image_hover_effect_type'];
						} else {
							$image_hover_effect_type = 'sg';
						}
						?>
						<input type="radio" name="image_hover_effect_type" id="image_hover_effect_type1" value="no"
							<?php
							if ($image_hover_effect_type == 'no') {
								echo 'checked=checked';
							}
							?>>
						<label for="image_hover_effect_type1">
							<?php esc_html_e('None', 'new-image-gallery'); ?>
						</label>
						<input type="radio" name="image_hover_effect_type" id="image_hover_effect_type2" value="sg"
							<?php
							if ($image_hover_effect_type == 'sg') {
								echo 'checked=checked';
							}
							?>>
						<label for="image_hover_effect_type2">
							<?php esc_html_e('2D Transitions', 'new-image-gallery'); ?>
						</label>
					</div>
				</div>
				<div class="he_four">
					<div class="col-md-4">
						<div class="ma_field_discription">
							<h6>
								<?php esc_html_e('Image Hover Effects', 'new-image-gallery'); ?>
							</h6>
							<p>
								<?php esc_html_e('Select and Set a image hover effect type for Gallery', 'new-image-gallery'); ?>
							</p>
						</div>
					</div>
					<div class="col-md-8">
						<div class="ma_field p-4">
							<?php
							if (isset($gallery_settings['image_hover_effect_four'])) {
								$image_hover_effect_four = $gallery_settings['image_hover_effect_four'];
							} else {
								$image_hover_effect_four = 'hvr-glow';
							}
							?>
							<select name="image_hover_effect_four" id="image_hover_effect_four" style="width:40%">
								<optgroup
									label="<?php esc_html_e('Shadow and Glow Transitions Effects', 'new-image-gallery'); ?>"
									class="sg">
									<option value="hvr-grow-shadow" <?php
									if ($image_hover_effect_four == 'hvr-grow-shadow') {
										echo 'selected=selected';
									}
									?>>
										<?php esc_html_e('Grow Shadow', 'new-image-gallery'); ?>
									</option>
									<option value="hvr-float-shadow" <?php
									if ($image_hover_effect_four == 'hvr-float-shadow') {
										echo 'selected=selected';
									}
									?>>
										<?php esc_html_e('Float Shadow', 'new-image-gallery'); ?>
									</option>
									<option value="hvr-glow" <?php
									if ($image_hover_effect_four == 'hvr-glow') {
										echo 'selected=selected';
									}
									?>>
										<?php esc_html_e('Glow', 'new-image-gallery'); ?>
									</option>
									<option value="hvr-box-shadow-inset" <?php
									if ($image_hover_effect_four == 'hvr-box-shadow-inset') {
										echo 'selected=selected';
									}
									?>>
										<?php esc_html_e('Box Shadow Inset', 'new-image-gallery'); ?>
									</option>
									<option value="hvr-box-shadow-outset" <?php
									if ($image_hover_effect_four == 'hvr-box-shadow-outset') {
										echo 'selected=selected';
									}
									?>>
										<?php esc_html_e('Box Shadow Outset', 'new-image-gallery'); ?>
									</option>
								</optgroup>
							</select>
						</div>
					</div>
				</div>
			</div>

			<div class="bhoechie-tab-content">
				<h1>
					<?php esc_html_e('Light Box Style', 'new-image-gallery'); ?>
				</h1>
				<hr>

				<div class="col-md-4">
					<div class="ma_field_discription">
						<h6>
							<?php esc_html_e('Light Box', 'new-image-gallery'); ?>
						</h6>
						<p>
							<?php esc_html_e('Select a light box style', 'new-image-gallery'); ?>
						</p>
					</div>
				</div>
				<div class="col-md-8">
					<div class="ma_field p-4">
						<?php
						if (isset($gallery_settings['light_box'])) {
							$light_box = $gallery_settings['light_box'];
						} else {
							$light_box = 4;
						}
						?>
						<select name="light_box" id="light_box" style="width:50%">
							<option value="0" <?php
							if ($light_box == 0) {
								echo 'selected=selected';
							}
							?>>
								<?php esc_html_e('None', 'new-image-gallery'); ?>
							</option>
							<option value="6" <?php
							if ($light_box == 6) {
								echo 'selected=selected';
							}
							?>>
								<?php esc_html_e('Bootstrap Light Box', 'new-image-gallery'); ?>
							</option>
							<option value="4" <?php
							if ($light_box == 4) {
								echo 'selected=selected';
							}
							?>>
								<?php esc_html_e('LD Light Box', 'new-image-gallery'); ?>
							</option>
						</select>
					</div>
				</div>
			</div>

			<div class="bhoechie-tab-content">
				<h1>
					<?php esc_html_e('Custom CSS', 'new-image-gallery'); ?>
				</h1>
				<hr>

				<div class="col-md-4">
					<div class="ma_field_discription">
						<h6>
							<?php esc_html_e('Custom CSS', 'new-image-gallery'); ?>
						</h6>
						<p>
							<?php esc_html_e('Apply own CSS on image gallery and do not use style tag', 'new-image-gallery'); ?>
						</p>
					</div>
				</div>
				<div class="col-md-8">
					<div class="ma_field p-4">
						<?php
						if (isset($gallery_settings['custom-css'])) {
							$custom_css = $gallery_settings['custom-css'];
						} else {
							$custom_css = '';
						}
						?>
						<textarea name="custom-css" id="custom-css" style="width: 100%; height: 120px;"
							placeholder="<?php esc_html_e('Type direct CSS code here. Do not use <style>...</style> tag.', 'new-image-gallery'); ?>"><?php echo esc_html($custom_css); ?></textarea>
					</div>
				</div>
			</div>

			<div class="bhoechie-tab-content">
				<h1>
					<?php esc_html_e('Upgrade To Pro', 'new-image-gallery'); ?>
				</h1>
				<hr>
				<!--Grid-->
				<div class="" style="padding-left: 10px;">
					<p class="ms-title">Upgrade To Premium For Unloack More Features & Settings</p>
				</div>

				<div class="">
					<h1><strong>Offer:</strong> Upgrade To Premium Just In Half Price <strike>$15</strike>
						<strong>$12</strong></h1>
					<br>
					<a href="https://awplife.com/demo/image-gallery-free-wordpress-plugin/" target="_blank"
						class="button button-primary button-hero load-customize hide-if-no-customize">Check Free Plugin
						Demo</a>
					<a href="https://awplife.com/demo/image-gallery-premium/" target="_blank"
						class="button button-primary button-hero load-customize hide-if-no-customize">Check Pro Plugin
						Demo</a>
					<a href="https://awplife.com/wordpress-plugins/image-gallery-wordpress-plugin/" target="_blank"
						class="button button-primary button-hero load-customize hide-if-no-customize">Premium Version
						Details</a>
					<a href="https://awplife.com/demo/image-gallery-premium-admin-demo/" target="_blank"
						class="button button-primary button-hero load-customize hide-if-no-customize">Try Pro
						Version</a>
				</div>

			</div>

		</div>
	</div>
</div>

<?php
// syntax: wp_nonce_field( 'name_of_my_action', 'name_of_nonce_field' );
wp_nonce_field('ig_save_settings', 'igp_save_nonce');
?>

<!-- Return to Top -->

<script>


	var effect_type = jQuery('input[name="image_hover_effect_type"]:checked').val();
	//alert(effect_type);
	if (effect_type == "no") {
		jQuery('.he_four').hide();
	}
	if (effect_type == "sg") {
		jQuery('.he_four').show();
	}

	//on change effect
	jQuery(document).ready(function () {
		jQuery('input[name="image_hover_effect_type"]').change(function () {
			var effect_type = jQuery('input[name="image_hover_effect_type"]:checked').val();
			if (effect_type == "no") {
				jQuery('.he_four').hide();
			}
			if (effect_type == "sg") {
				jQuery('.he_four').show();
			}
		});
	});

	// tab
	jQuery("div.bhoechie-tab-menu>div.list-group>a").click(function (e) {
		e.preventDefault();
		jQuery(this).siblings('a.active').removeClass("active");
		jQuery(this).addClass("active");
		var index = jQuery(this).index();
		jQuery("div.bhoechie-tab>div.bhoechie-tab-content").removeClass("active");
		jQuery("div.bhoechie-tab>div.bhoechie-tab-content").eq(index).addClass("active");
	});

</script>