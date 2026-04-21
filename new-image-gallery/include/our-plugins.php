<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Fetch plugin information from WordPress.org
if ( ! function_exists( 'plugins_api' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
}

$author_slug = 'awordpresslife';
$api_args = array(
	'author' => $author_slug,
	'fields' => array(
		'icons'             => true,
		'banners'           => true,
		'active_installs'   => true,
		'short_description' => true,
		'rating'            => true,
		'num_ratings'       => true,
	),
);

$response = plugins_api( 'query_plugins', $api_args );
$plugins = array();

if ( ! is_wp_error( $response ) && isset( $response->plugins ) ) {
	$plugins = $response->plugins;
}

// Filter out the current plugin (New Image Gallery) if desired, but usually, it's fine to show it.
// Let's sort them by active installs descending.
usort( $plugins, function ( $a, $b ) {
    $a = (array) $a;
    $b = (array) $b;
    $a_installs = isset($a['active_installs']) ? (int) $a['active_installs'] : 0;
    $b_installs = isset($b['active_installs']) ? (int) $b['active_installs'] : 0;
	return $b_installs - $a_installs;
} );

?>
<div class="wrap ig-our-plugins-wrap">
    <header class="ig-our-plugins-header">
        <h1><?php esc_html_e( 'Our WordPress Ecosystem', 'new-image-gallery' ); ?></h1>
        <p><?php esc_html_e( 'Discover more powerful tools designed to simplify your WordPress workflow. High-performance plugins built by A WP Life.', 'new-image-gallery' ); ?></p>
    </header>

	<?php if ( is_wp_error( $response ) ) : ?>
        <div class="ig-error-wrap">
            <span class="dashicons dashicons-warning"></span>
            <h2><?php esc_html_e( 'Unable to fetch our plugins', 'new-image-gallery' ); ?></h2>
            <p><?php echo esc_html( $response->get_error_message() ); ?></p>
            <a href="<?php echo esc_url( 'https://profiles.wordpress.org/awordpresslife/#content-plugins' ); ?>" target="_blank" class="ig-btn ig-btn-primary" style="margin-top: 20px;">
				<?php esc_html_e( 'Visit Our WordPress Profile', 'new-image-gallery' ); ?>
            </a>
        </div>
	<?php elseif ( empty( $plugins ) ) : ?>
        <div class="ig-loading-wrap">
            <div class="ig-spinner"></div>
            <h3><?php esc_html_e( 'Refreshing our plugin collection...', 'new-image-gallery' ); ?></h3>
        </div>
	<?php else : ?>
        <div class="ig-plugins-grid">
			<?php foreach ( $plugins as $plugin ) :
                // Ensure we handle both object and array response formats safely
                $plugin = (array) $plugin;
				$icons = isset($plugin['icons']) ? (array) $plugin['icons'] : array();
				$icon = ! empty( $icons['2x'] ) ? $icons['2x'] : ( ! empty( $icons['1x'] ) ? $icons['1x'] : '' );
				
                $banners = isset($plugin['banners']) ? (array) $plugin['banners'] : array();
				$banner = ! empty( $banners['high'] ) ? $banners['high'] : ( ! empty( $banners['low'] ) ? $banners['low'] : '' );
				
				// Fallback banner if none exists
				if ( empty( $banner ) ) {
					$banner = 'https://s.w.org/plugins/geopattern-icon/' . $plugin['slug'] . '.svg';
				}

                $rating = isset($plugin['rating']) ? $plugin['rating'] : 0;
				$stars = ( $rating / 100 ) * 5;
                
                $active_installs = isset($plugin['active_installs']) ? $plugin['active_installs'] : 0;
				$install_count = $active_installs >= 1000 ? ( floor( $active_installs / 1000 ) . 'k+' ) : $active_installs;
				
				// Check if plugin is already installed
				$is_installed = file_exists( WP_PLUGIN_DIR . '/' . $plugin['slug'] );
				?>
                <div class="ig-plugin-card">
					<?php if ( $is_installed ) : ?>
                        <div class="ig-plugin-status"><?php esc_html_e( 'INSTALLED', 'new-image-gallery' ); ?></div>
					<?php endif; ?>
                    
                    <div class="ig-plugin-banner">
                        <img src="<?php echo esc_url( $banner ); ?>" alt="<?php echo esc_attr( $plugin['name'] ); ?>">
                    </div>

                    <div class="ig-plugin-content">
                        <h2><?php echo esc_html( $plugin['name'] ); ?></h2>
                        <div class="ig-plugin-description">
							<?php echo esc_html( wp_trim_words( $plugin['short_description'], 20 ) ); ?>
                        </div>

                        <div class="ig-plugin-meta">
                            <div class="ig-plugin-meta-item" title="<?php echo esc_attr( $rating ); ?>%">
                                <span class="dashicons dashicons-star-filled"></span>
								<?php echo esc_html( number_format( $stars, 1 ) ); ?>
                            </div>
                            <div class="ig-plugin-meta-item">
                                <span class="dashicons dashicons-download"></span>
								<?php echo esc_html( $install_count ); ?> <?php esc_html_e( 'Installs', 'new-image-gallery' ); ?>
                            </div>
                        </div>

                        <div class="ig-plugin-actions">
                            <a href="<?php echo esc_url( 'https://wordpress.org/plugins/' . $plugin['slug'] . '/' ); ?>" target="_blank" class="ig-btn ig-btn-secondary">
								<?php esc_html_e( 'Details', 'new-image-gallery' ); ?>
                            </a>
                            <a href="<?php echo esc_url( admin_url( 'plugin-install.php?tab=plugin-information&plugin=' . $plugin['slug'] . '&TB_iframe=true&width=772&height=550' ) ); ?>" class="ig-btn ig-btn-primary thickbox">
								<?php esc_html_e( 'Install Now', 'new-image-gallery' ); ?>
                            </a>
                        </div>
                    </div>
                </div>
			<?php endforeach; ?>
        </div>
	<?php endif; ?>
</div>
