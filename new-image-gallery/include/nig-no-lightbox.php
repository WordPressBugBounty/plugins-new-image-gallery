<?php
$allslides = array(
	'p' => $image_gallery_id,
	'post_type' => 'image_gallery',
	'orderby' => 'ASC',
);
$loop = new WP_Query($allslides);
while ($loop->have_posts()):
	$loop->the_post();

	$post_id = esc_attr(get_the_ID());
	// Check if the data is serialized
	if (is_ig_serialized($decodedData)) {

		// The data is serialized, so unserialize it
		$gallery_settings = unserialize($decodedData);
		// Optionally, convert the unserialized data to JSON and save it back in base64 encoding for future access
		// This step is optional but recommended to transition your data format

		$jsonEncodedData = json_encode($gallery_settings);
		update_post_meta($image_gallery_id, 'awl_ig_settings_' . $image_gallery_id, $jsonEncodedData);

		// Now, to use the newly saved format, fetch and decode again
		$encodedData = get_post_meta($image_gallery_id, 'awl_ig_settings_' . $image_gallery_id, true);
		$gallery_settings = json_decode(($encodedData), true);

	} else {

		// Assume the data is in JSON format
		$jsonData = get_post_meta($image_gallery_id, 'awl_ig_settings_' . $image_gallery_id, true);
		// Decode the JSON string into an associative array
		$gallery_settings = json_decode($jsonData, true); // Ensure true is passed to get an associative array
	}
	count($gallery_settings['slide-ids']);
	// start the image gallery contents
	?>
	<div id="image_gallery_<?php echo esc_attr($image_gallery_id); ?>" class="row all-images">
		<?php
		if (isset($gallery_settings['slide-ids']) && count($gallery_settings['slide-ids']) > 0) {
			$count = 0;
			if ($thumbnail_order == 'DESC') {
				$gallery_settings['slide-ids'] = array_reverse($gallery_settings['slide-ids']);
			}
			if ($thumbnail_order == 'RANDOM') {
				shuffle($gallery_settings['slide-ids']);
			}

			foreach ($gallery_settings['slide-ids'] as $attachment_id) {
				$thumb = wp_get_attachment_image_src($attachment_id, 'thumb', true);
				$thumbnail = wp_get_attachment_image_src($attachment_id, 'thumbnail', true);
				$medium = wp_get_attachment_image_src($attachment_id, 'medium', true);
				$large = wp_get_attachment_image_src($attachment_id, 'large', true);
				$full = wp_get_attachment_image_src($attachment_id, 'full', true);
				$postthumbnail = wp_get_attachment_image_src($attachment_id, 'post-thumbnail', true);
				$attachment_details = get_post($attachment_id);
				$href = get_permalink($attachment_details->ID);
				$src = $attachment_details->guid;
				$title = $attachment_details->post_title;
				$description = $attachment_details->post_content;

				if (!empty($gallery_settings['slide-alt'][$count])) {
					$image_alt = $gallery_settings['slide-alt'][$count];
				} else {
					$image_alt = $title;
				}

				// set thumbnail size
				if ($gal_thumb_size == 'thumbnail') {
					$thumbnail_url = $thumbnail[0];
				}
				if ($gal_thumb_size == 'medium') {
					$thumbnail_url = $medium[0];
				}
				if ($gal_thumb_size == 'large') {
					$thumbnail_url = $large[0];
				}
				if ($gal_thumb_size == 'full') {
					$thumbnail_url = $full[0];
				}
				?>
				<div
					class="single-image <?php echo esc_attr($col_large_desktops); ?> <?php echo esc_attr($col_desktops); ?> <?php echo esc_attr($col_tablets); ?> <?php echo esc_attr($col_phones); ?>">
					<img class="thumbnail <?php echo esc_attr($image_hover_effect); ?>"
						src="<?php echo esc_url($thumbnail_url); ?>" alt="<?php echo esc_html($image_alt); ?>"
						alt="<?php echo esc_html($title); ?>">
					<?php if ($img_title == 0) { ?>
						<span class="item-title">
							<?php echo esc_html($title); ?>
						</span>
					<?php } ?>
				</div>
				<?php
				$count++;
			}// end of attachment foreach
		} else {
			esc_html_e('Sorry! No image gallery found.', 'new-image-gallery');
			echo ": [IMG-Gal id=" . esc_attr($post_id) . "]";
		} // end of if esle of slides avaialble check into slider
		?>
	</div>
	<?php
endwhile;
wp_reset_query();
?>
<script>
	jQuery(document).ready(function () {
		// Method 1 - Initialize Isotope, then trigger layout after each image loads.
		var $grid = jQuery('.all-images').isotope({
			// options...
			itemSelector: '.single-image',
		});
		// layout Isotope after each image loads
		$grid.imagesLoaded().progress(function () {
			$grid.isotope('layout');
		});

	});
</script>