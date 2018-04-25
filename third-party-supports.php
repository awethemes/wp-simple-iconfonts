<?php
/**
 * Third-party supports.
 *
 * All support use a `simple_icon` type or control name.
 *
 * @package WP_Simple_Iconfonts
 */

/**
 * Support Kirki.
 *
 * @link https://wordpress.org/plugins/kirki/
 *
 * @param  array $controls Available controls.
 * @return array
 */
function wp_simple_iconfonts_support_kirki( $controls ) {
	$controls['simple_iconfonts'] = 'WP_Simple_Iconfonts\\Support\\WP_Simple_Iconfonts_Control';

	return $controls;
}

/**
 * Support WP Customizer.
 *
 * @param  WP_Customize_Manager $wp_customize WP_Customize_Manager instance.
 * @return void
 */
function wp_simple_iconfonts_support_customizer( $wp_customize ) {
	if ( class_exists( 'Kirki' ) ) {
		add_filter( 'kirki/control_types', 'wp_simple_iconfonts_support_kirki' );
	}
}
add_action( 'customize_register', 'wp_simple_iconfonts_support_customizer' );

if ( class_exists( 'acf' ) ) {
	/**
	 * Register the field in ACF (free version).
	 *
	 * @return void
	 */
	function wp_simple_iconfonts_support_acf() {
		new WP_Simple_Iconfonts\Support\ACF_Simple_Iconfonts_Field;
	}
	add_action( 'acf/register_fields', 'wp_simple_iconfonts_support_acf' );

	/**
	 * Register the field in ACF v5 (pro version).
	 *
	 * @return void
	 */
	function wp_simple_iconfonts_support_acf5() {
		new WP_Simple_Iconfonts\Support\ACF5_Simple_Iconfonts_Field;
	}
	add_action( 'acf/include_field_types', 'wp_simple_iconfonts_support_acf5' );
}

if ( defined( 'CMB2_LOADED' ) ) {
	/**
	 * Support CMB2.
	 *
	 * @link https://wordpress.org/plugins/cmb2/
	 */
	new WP_Simple_Iconfonts\Support\CMB2_Simple_Iconfonts_Field;
}

if ( class_exists( 'ReduxFramework' ) ) {
	/**
	 * Support ReduxFramework.
	 *
	 * Just include `ReduxFramework_Simple_Icon` when redux loaded.
	 *
	 * @return void
	 */
	function wp_simple_iconfonts_support_redux_framework() {
		require_once trailingslashit( __DIR__ ) . 'inc/Support/ReduxFramework_Simple_Iconfonts.php';
	}
	add_action( 'redux/loaded', 'wp_simple_iconfonts_support_redux_framework' );
}

if ( class_exists( 'TitanFramework' ) ) {
	/**
	 * Support TitanFramework.
	 *
	 * @link https://wordpress.org/plugins/titan-framework/
	 */
	function wp_simple_iconfonts_support_titan_framework() {
		require_once trailingslashit( __DIR__ ) . 'inc/Support/TitanFrameworkOptionSimple_Iconfonts.php';
	}
	add_action( 'tf_create_options', 'wp_simple_iconfonts_support_titan_framework' );
}

if ( defined( 'CMB_VERSION' ) ) {
	/**
	 * Support Custom Meta Boxes.
	 *
	 * @link https://github.com/humanmade/Custom-Meta-Boxes
	 * @see  https://github.com/humanmade/Custom-Meta-Boxes/wiki/Adding-your-own-field-types
	 *
	 * @param  array $field_types Available CMB field types.
	 * @return array
	 */
	function wp_simple_iconfonts_support_cmb( $field_types ) {
		$field_types['simple_iconfonts'] = 'WP_Simple_Iconfonts\\Support\\CMB_Simple_Iconfonts_Field';

		return $field_types;
	}
	add_filter( 'cmb_field_types', 'wp_simple_iconfonts_support_cmb' );
}

if ( defined( 'RWMB_VER' ) ) {
	/**
	 * Support metabox.io
	 *
	 * Just include `RWMB_Simple_Icon_Field` class when init fired.
	 *
	 * @link https://wordpress.org/plugins/meta-box/
	 */
	function wp_simple_iconfonts_support_metabox_io() {
		require_once trailingslashit( __DIR__ ) . 'inc/Support/RWMB_Simple_Iconfonts_Field.php';
	}
	add_action( 'init', 'wp_simple_iconfonts_support_metabox_io', 1 );
}

if ( defined( 'WPB_VC_VERSION' ) ) {
	/**
	 * Support Visual Composer.
	 */
	function wp_simple_iconfonts_support_js_composer() {
		new WP_Simple_Iconfonts\Support\JS_Composer_Simple_Iconfonts_Param;
	}
	add_action( 'vc_after_init', 'wp_simple_iconfonts_support_js_composer' );
}
