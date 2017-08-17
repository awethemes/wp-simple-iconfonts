<?php
/**
 * Third-party supports.
 *
 * All support use a `simple_icon` type or control name.
 *
 * @package WP_Simple_Iconfonts
 */

/**
 * Support ACF field.
 *
 * @link https://wordpress.org/plugins/advanced-custom-fields/
 */
if ( class_exists( 'acf' ) ) {
	new WP_Simple_Iconfonts\Support\ACF_Simple_Icon_Field;
}

/**
 * Support CMB2 field.
 *
 * @link https://wordpress.org/plugins/cmb2/
 */
if ( defined( 'CMB2_LOADED' ) ) {
	new WP_Simple_Iconfonts\Support\CMB2_Simple_Icon_Field;
}
