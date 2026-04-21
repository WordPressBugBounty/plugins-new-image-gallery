<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
wp_enqueue_style('awl-ig-docs-css');
?>
<div class="wrap igp-docs-wrap">

    <div class="igp-docs-sidebar">
        <div class="igp-sidebar-logo">
            <span class="dashicons dashicons-images-alt2"></span>
            <span><?php esc_html_e('Mastery Guide', 'new-image-gallery'); ?></span>
        </div>
        <ul class="igp-toc">
            <li><a href="#tab-1"><span class="dashicons dashicons-format-image"></span> <?php esc_html_e('1. Add Images', 'new-image-gallery'); ?></a></li>
            <li><a href="#tab-2"><span class="dashicons dashicons-layout"></span> <?php esc_html_e('2. Layout & Design', 'new-image-gallery'); ?></a></li>
            <li><a href="#tab-3"><span class="dashicons dashicons-welcome-view-site"></span> <?php esc_html_e('3. Lightbox', 'new-image-gallery'); ?></a></li>

            <li><a href="#section-deployment"><span class="dashicons dashicons-shortcode"></span> <?php esc_html_e('Shortcode Deployment', 'new-image-gallery'); ?></a></li>
        </ul>
    </div>

    <div class="igp-docs-content">
        <header class="igp-main-header">
            <h1><?php esc_html_e('Image Gallery: Complete Encyclopedia', 'new-image-gallery'); ?> <span class="igp-version-badge">v<?php echo esc_html(IG_PLUGIN_VER); ?></span></h1>
            <p><?php esc_html_e('A step-by-step tutorial aligned with your gallery settings workflow.', 'new-image-gallery'); ?></p>
        </header>

        <!-- Tab 1: Add Images -->
        <section id="tab-1" class="igp-info-section">
            <h2><span class="dashicons dashicons-format-image"></span> <?php esc_html_e('Step 1: Content Management (Add Images)', 'new-image-gallery'); ?></h2>
            <div class="igp-tutorial-card">
                <h3><?php esc_html_e('Image Assets & Metadata', 'new-image-gallery'); ?></h3>
                <p><?php esc_html_e('This tab controls the source images and their individual properties.', 'new-image-gallery'); ?></p>
                <table class="igp-settings-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Setting / Tool', 'new-image-gallery'); ?></th>
                            <th><?php esc_html_e('Description & Impact', 'new-image-gallery'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong><?php esc_html_e('Drag & Drop Sort', 'new-image-gallery'); ?></strong></td>
                            <td><?php esc_html_e('Use the handle on the top-left of any image card to manually specify the sequence.', 'new-image-gallery'); ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php esc_html_e('Sort ASC / DESC', 'new-image-gallery'); ?></strong></td>
                            <td><?php esc_html_e('Instant bulk-sorting buttons to reorganize your entire stack in seconds.', 'new-image-gallery'); ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php esc_html_e('Delete All', 'new-image-gallery'); ?></strong></td>
                            <td><?php esc_html_e('A bulk action to clear the gallery. Use with caution.', 'new-image-gallery'); ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php esc_html_e('Meta Fields', 'new-image-gallery'); ?></strong></td>
                            <td><?php esc_html_e('Configure custom Titles, Alt tags, and Lightbox Descriptions for every image.', 'new-image-gallery'); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Tab 2: Layout & Design -->
        <section id="tab-2" class="igp-info-section">
            <h2><span class="dashicons dashicons-layout"></span> <?php esc_html_e('Step 2: Architecture & Styling (Layout & Design)', 'new-image-gallery'); ?></h2>
            <div class="igp-tutorial-card">
                <h3><?php esc_html_e('Layout Configuration', 'new-image-gallery'); ?></h3>
                <table class="igp-settings-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Setting', 'new-image-gallery'); ?></th>
                            <th><?php esc_html_e('How it Works', 'new-image-gallery'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong><?php esc_html_e('Thumbnail Layout', 'new-image-gallery'); ?></strong></td>
                            <td><?php esc_html_e('Standardized Masonry (Natural) layout for modern aesthetics.', 'new-image-gallery'); ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php esc_html_e('Thumbnail Resolution', 'new-image-gallery'); ?></strong></td>
                            <td><?php esc_html_e('Select specific WordPress image sizes. Use "Thumbnail" for speed or "Full" for ultra-high pixel density.', 'new-image-gallery'); ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php esc_html_e('Title Position', 'new-image-gallery'); ?></strong></td>
                            <td><?php esc_html_e('Titles are displayed as a professional bottom-bar overlay directly on the image.', 'new-image-gallery'); ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php esc_html_e('Responsive Columns', 'new-image-gallery'); ?></strong></td>
                            <td><?php esc_html_e('Set precise column counts (1-12) for X-Large, Desktop, Tablet, and Mobile screens.', 'new-image-gallery'); ?></td>
                        </tr>
                    </tbody>
                </table>

                <h3 style="margin-top:40px;"><?php esc_html_e('Advanced Card Styling', 'new-image-gallery'); ?></h3>
                <p><?php esc_html_e('Fine-tune the "Card" aesthetics using design tokens:', 'new-image-gallery'); ?></p>
                <ul class="igp-bullet-list">
                    <li><strong><?php esc_html_e('Border Thickness:', 'new-image-gallery'); ?></strong> <?php esc_html_e('Creates an internal frame (padding) around the photo.', 'new-image-gallery'); ?></li>
                    <li><strong><?php esc_html_e('Opacity Tokens:', 'new-image-gallery'); ?></strong> <?php esc_html_e('Adjust transparency for both Borders and Card Backgrounds (0-100%).', 'new-image-gallery'); ?></li>
                    <li><strong><?php esc_html_e('Border Radius:', 'new-image-gallery'); ?></strong> <?php esc_html_e('Apply rounded corners to your cards for a modern feel.', 'new-image-gallery'); ?></li>
                </ul>

                <h3 style="margin-top:40px;"><?php esc_html_e('Hover Animations', 'new-image-gallery'); ?></h3>
                <div class="igp-split-list">

                    <div class="igp-list-col" style="grid-column: span 2;">
                        <h4><?php esc_html_e('Shadow Styles', 'new-image-gallery'); ?></h4>
                        <ul>
                            <li><?php esc_html_e('Grow Shadow', 'new-image-gallery'); ?></li>
                            <li><?php esc_html_e('Float Shadow', 'new-image-gallery'); ?></li>
                            <li><?php esc_html_e('Glow Overlay', 'new-image-gallery'); ?></li>
                            <li><?php esc_html_e('Box Shadow Outset', 'new-image-gallery'); ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- Tab 3: Lightbox -->
        <section id="tab-3" class="igp-info-section">
            <h2><span class="dashicons dashicons-welcome-view-site"></span> <?php esc_html_e('Step 3: Interaction Logic (Lightbox)', 'new-image-gallery'); ?></h2>
            <div class="igp-tutorial-card">
                <table class="igp-settings-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Setting', 'new-image-gallery'); ?></th>
                            <th><?php esc_html_e('Description', 'new-image-gallery'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong><?php esc_html_e('Lightbox Script', 'new-image-gallery'); ?></strong></td>
                            <td><?php esc_html_e('Includes a high-performance LD Lightbox. "None" disables the popup.', 'new-image-gallery'); ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php esc_html_e('Sorting Logic', 'new-image-gallery'); ?></strong></td>
                            <td><?php esc_html_e('Automatically order by Oldest (ASC), Newest (DESC), or Shuffle (RANDOM) on the frontend.', 'new-image-gallery'); ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php esc_html_e('Lightbox Titles', 'new-image-gallery'); ?></strong></td>
                            <td><?php esc_html_e('Image titles are automatically displayed inside the lightbox for enhanced context.', 'new-image-gallery'); ?></td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </section>



        <!-- Final Step: Deployment -->
        <section id="section-deployment" class="igp-info-section">
            <h2><span class="dashicons dashicons-shortcode"></span> <?php esc_html_e('Deployment & Shortcuts', 'new-image-gallery'); ?></h2>
            <div class="igp-tutorial-card">
                <p><?php esc_html_e('Once configured, copy the shortcode from the sidebar metabox and paste it into any page:', 'new-image-gallery'); ?></p>
                <div class="igp-code-sample">
                    <code>[IMG-Gal id=<?php esc_html_e('XXXX', 'new-image-gallery'); ?>]</code>
                </div>
            </div>
        </section>

        <footer class="igp-content-footer">
            <p><?php esc_html_e('Documentation synchronized with Image Gallery Version', 'new-image-gallery'); ?> <?php echo esc_html(IG_PLUGIN_VER); ?></p>
        </footer>
    </div>
</div>


