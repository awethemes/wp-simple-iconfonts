<?php
/**
 * Plugin Name:     WP Simple Iconfonts
 * Plugin URI:      https://github.com/awethemes/wp-simple-iconfonts
 * Description:     A dead simple, an icon fonts manager and picker for WordPress
 * Author:          awethemes
 * Author URI:      https://awethemes.com/
 * Text Domain:     wp_simple_iconfonts
 * Domain Path:     /languages
 * Version:         0.5.1
 *
 * @package         WP_Simple_Iconfonts
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Silence is golden.' );
}

/**
 * Plugin works only with PHP 5.4.0 or later.
 */
if ( version_compare( phpversion(), '5.4.0', '<' ) ) {
	/**
	 * Adds a message for outdate PHP version.
	 */
	function wp_simple_iconfonts_php_upgrade_notice() {
		$message = sprintf( esc_html__( 'WP Simple Iconfonts requires at least PHP version 5.4.0 to works, you are running version %s. Please contact to your administrator to upgrade PHP version!', 'wp_simple_iconfonts' ), phpversion() );
		printf( '<div class="error"><p>%s</p></div>', $message ); // WPCS: XSS OK.

		deactivate_plugins( array( 'wp-simple-iconfonts/wp-simple-iconfonts.php' ) );
	}

	add_action( 'admin_notices', 'wp_simple_iconfonts_php_upgrade_notice' );
	return;
}

if ( ! class_exists( 'WP_Simple_Iconfonts\\Iconfonts' ) ) :
	define( 'WP_SIMPLE_ICONFONTS_PATH', __FILE__ );

	// First, require the autoloader.
	require trailingslashit( __DIR__ ) . 'autoload.php';

	if ( file_exists( trailingslashit( __DIR__ ) . 'vendor/autoload.php' ) ) {
		require trailingslashit( __DIR__ ) . 'vendor/autoload.php';
	}

	require trailingslashit( __DIR__ ) . 'inc/functions.php';

	/**
	 * Get the Iconfonts object instance.
	 *
	 * @return WP_Simple_Iconfonts\Iconfonts
	 */
	function wp_simple_iconfonts() {
		return WP_Simple_Iconfonts\Iconfonts::get_instance();
	}

	// Share main class into global variable.
	$GLOBALS['wp_simple_iconfonts'] = new WP_Simple_Iconfonts\Iconfonts;

	// Init the supports.
	WP_Simple_Iconfonts\Support\Nav_Menu_Icon::instance();
	WP_Simple_Iconfonts\Support\Shortcode_Icon::instance();

	// Third party supports.
	require_once trailingslashit( __DIR__ ) . 'third-party-supports.php';
endif;
