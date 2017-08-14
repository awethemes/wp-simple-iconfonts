<?php
/**
 * WP_Simple_Iconfonts autoload.
 *
 * @package WP_Simple_Iconfonts
 */

/**
 * WP_Simple_Iconfonts PSR-4 autoload implementation.
 *
 * @link http://www.php-fig.org/psr/psr-4/examples/
 *
 * @param string $class The fully-qualified class name.
 * @return void
 */
spl_autoload_register(function ( $class ) {
	// Project-specific namespace prefix.
	$prefix = 'WP_Simple_Iconfonts\\';

	// Base directory for the namespace prefix.
	$base_dir = __DIR__ . '/inc/';

	// Does the class use the namespace prefix?
	$len = strlen( $prefix );
	if ( strncmp( $prefix, $class, $len ) !== 0 ) {
		// No, move to the next registered autoloader.
		return;
	}

	// Get the relative class name.
	$relative_class = substr( $class, $len );

	// Replace the namespace prefix with the base directory, replace namespace
	// separators with directory separators in the relative class name, append
	// with .php.
	$file = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';

	// Ff the file exists, require it.
	if ( file_exists( $file ) ) {
		require $file;
	}
});
