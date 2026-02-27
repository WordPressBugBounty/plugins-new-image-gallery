<style>
	.ig-docs-section h3 {
		color: var(--wp-admin-theme-color, #2271b1);
	}

	.ig-docs-section p,
	.ig-docs-section li {
		font-size: 14px;
		color: var(--wp--preset--color--text, #3c434a);
		line-height: 1.5;
	}

	.ig-docs-section strong {
		color: var(--wp--preset--color--text, #1d2327);
	}

	.ig-docs-section code {
		color: var(--wp-admin-theme-color, #2271b1);
	}
</style>
<div class="wrap">
	<div id="welcome-panel" class="">
		<div class="welcome-panel-content">
			<h1><?php esc_html_e('Welcome to New Image Gallery Documentation', 'new-image-gallery'); ?></h1>
			<p class="about-description"><?php esc_html_e('Getting started with the New Image Gallery plugin - Follow these simple steps to create, configure, and publish your beautiful galleries.', 'new-image-gallery'); ?></p>
			<hr>

			<div class="ig-docs-section">
				<h3><span class="dashicons dashicons-download" style="vertical-align: middle;"></span> <?php esc_html_e('Step 1: Install & Activate', 'new-image-gallery'); ?></h3>
				<p><?php esc_html_e('If you have downloaded the plugin zip file from WordPress.org:', 'new-image-gallery'); ?></p>
				<ol>
					<li><?php esc_html_e('Log in to your WordPress admin dashboard.', 'new-image-gallery'); ?></li>
					<li><?php esc_html_e('Navigate to ', 'new-image-gallery'); ?><strong><?php esc_html_e('Plugins > Add New', 'new-image-gallery'); ?></strong><?php esc_html_e(' and click on the ', 'new-image-gallery'); ?><strong><?php esc_html_e('Upload Plugin', 'new-image-gallery'); ?></strong><?php esc_html_e(' button.', 'new-image-gallery'); ?></li>
					<li><?php esc_html_e('Choose the downloaded zip file, install it, and then click ', 'new-image-gallery'); ?><strong><?php esc_html_e('Activate', 'new-image-gallery'); ?></strong>.</li>
				</ol>
			</div>

			<div class="ig-docs-section">
				<h3><span class="dashicons dashicons-format-gallery" style="vertical-align: middle;"></span> <?php esc_html_e('Step 2: Create a New Gallery', 'new-image-gallery'); ?></h3>
				<p><?php esc_html_e('Now that the plugin is active, let\'s create your first gallery:', 'new-image-gallery'); ?></p>
				<ol>
					<li><?php esc_html_e('Go to the ', 'new-image-gallery'); ?><strong><?php esc_html_e('New Image Gallery > Add Image Gallery', 'new-image-gallery'); ?></strong><?php esc_html_e(' menu in your WordPress dashboard.', 'new-image-gallery'); ?></li>
					<li><?php esc_html_e('Enter a title for your gallery at the top.', 'new-image-gallery'); ?></li>
					<li><?php esc_html_e('Click the ', 'new-image-gallery'); ?><strong><?php esc_html_e('Add Images', 'new-image-gallery'); ?></strong><?php esc_html_e(' button to upload or select images from your WordPress Media Library.', 'new-image-gallery'); ?></li>
					<li><?php esc_html_e('You can drag and drop images to reorder them, or add a specific Title and Alt Text for each image.', 'new-image-gallery'); ?></li>
				</ol>
			</div>

			<div class="ig-docs-section">
				<h3><span class="dashicons dashicons-admin-generic" style="vertical-align: middle;"></span> <?php esc_html_e('Step 3: Configure Settings', 'new-image-gallery'); ?></h3>
				<p><?php esc_html_e('Customize your gallery to match your site\'s design by exploring the configuration tabs:', 'new-image-gallery'); ?></p>
				<ul style="list-style-type: disc; margin-left: 20px;">
					<li><strong><?php esc_html_e('Configure:', 'new-image-gallery'); ?></strong> <?php esc_html_e('Set the Gallery Thumbnail Size, adjust Column Layouts for different devices (Desktops, Tablets, Phones), toggle Thumbnail Titles or Spacing, and define the Image Order.', 'new-image-gallery'); ?></li>
					<li><strong><?php esc_html_e('Animation Effect:', 'new-image-gallery'); ?></strong> <?php esc_html_e('Choose a smooth 2D Transition (like Grow, Float, or Glow) to play when users hover over your images.', 'new-image-gallery'); ?></li>
					<li><strong><?php esc_html_e('LightBox Settings:', 'new-image-gallery'); ?></strong> <?php esc_html_e('Select a Light Box style (e.g., Bootstrap Light Box or LD Light Box) to display your images in a beautiful popup when clicked.', 'new-image-gallery'); ?></li>
					<li><strong><?php esc_html_e('Custom CSS:', 'new-image-gallery'); ?></strong> <?php esc_html_e('Add your own CSS rules perfectly tailored to this specific gallery.', 'new-image-gallery'); ?></li>
				</ul>
				<p><?php esc_html_e('Once you are satisfied with your settings, click the ', 'new-image-gallery'); ?><strong><?php esc_html_e('Publish', 'new-image-gallery'); ?></strong><?php esc_html_e(' (or Update) button to save your gallery.', 'new-image-gallery'); ?></p>
			</div>

			<div class="ig-docs-section">
				<h3><span class="dashicons dashicons-shortcode" style="vertical-align: middle;"></span> <?php esc_html_e('Step 4: Display Gallery On Your Site', 'new-image-gallery'); ?></h3>
				<p><?php esc_html_e('After publishing, you need to embed the gallery into a post or page using its unique shortcode.', 'new-image-gallery'); ?></p>
				<ol>
					<li><?php esc_html_e('Look for the ', 'new-image-gallery'); ?><strong><?php esc_html_e('Gallery Shortcode', 'new-image-gallery'); ?></strong><?php esc_html_e(' meta box on the right side of the gallery editor page.', 'new-image-gallery'); ?></li>
					<li><?php esc_html_e('Copy the highlighted shortcode. It will look something like this:', 'new-image-gallery'); ?>
						<div style="margin: 10px 0;">
							<code style="background: #f0f0f1; padding: 5px 10px; border-left: 4px solid var(--wp-admin-theme-color, #00a0d2); display: inline-block; font-size: 14px;">[IMG-Gal id=4]</code>
						</div>
						<em><?php esc_html_e('(Here, "id=4" is the unique ID of your gallery.)', 'new-image-gallery'); ?></em>
					</li>
					<li><?php esc_html_e('Go to ', 'new-image-gallery'); ?><strong><?php esc_html_e('Pages > Add New', 'new-image-gallery'); ?></strong><?php esc_html_e(' (or Posts).', 'new-image-gallery'); ?></li>
					<li><?php esc_html_e('Paste the copied shortcode directly into the content editor (or use a Shortcode block if you are using the Gutenberg editor).', 'new-image-gallery'); ?></li>
					<li><?php esc_html_e('Publish the page and view it on the front-end to see your stunning image gallery in action!', 'new-image-gallery'); ?></li>
				</ol>
			</div>
			<hr>
		</div>

		<div class="welcome-panel-content">
			<div style="background: #fff8e5; border-left: 4px solid #ffb900; padding: 20px; margin-top: 10px;">
				<h2 style="margin-top: 0; color: #d63638;"><strong><?php esc_html_e('Early Bird Offer:', 'new-image-gallery'); ?></strong> <?php esc_html_e('Upgrade To Premium at Discounted Price', 'new-image-gallery'); ?> <strike>$15</strike> <strong>$12</strong></h2>
				<p style="font-size: 15px; margin-bottom: 20px; line-height: 1.5;"><?php esc_html_e('Unlock advanced features, more stunning hover effects, additional lightbox styles, masonry layouts, unlimited priority support, and much more by upgrading to the Pro version today!', 'new-image-gallery'); ?></p>
				<p>
					<a href="https://awplife.com/wordpress-plugins/image-gallery-premium/" target="_blank" class="button button-primary button-hero load-customize hide-if-no-customize" style="margin-right: 10px; margin-bottom: 10px;"><?php esc_html_e('Premium Version Details', 'new-image-gallery'); ?></a>
					<a href="https://awplife.com/demo/image-gallery-premium/" target="_blank" class="button button-primary button-hero load-customize hide-if-no-customize" style="margin-right: 10px; margin-bottom: 10px;"><?php esc_html_e('Check Live Demo', 'new-image-gallery'); ?></a>
					<a href="https://awplife.com/account/signup/image-gallery-premium" target="_blank" class="button button-primary button-hero load-customize hide-if-no-customize" style="margin-bottom: 10px;"><?php esc_html_e('Buy Premium Version', 'new-image-gallery'); ?></a>
				</p>
			</div>
		</div>
	</div>
</div>