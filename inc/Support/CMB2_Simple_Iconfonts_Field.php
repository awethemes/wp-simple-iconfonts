<?php
namespace WP_Simple_Iconfonts\Support;

class CMB2_Simple_Iconfonts_Field {
	/**
	 * Field type.
	 *
	 * @var string
	 */
	protected $type = 'simple_iconfonts';

	/**
	 * Init the CMB2 `simple_icon` type.
	 */
	public function __construct() {
		add_action( "cmb2_render_{$this->type}", [ $this, 'display' ], 10, 2 );
		add_filter( "cmb2_sanitize_{$this->type}", [ $this, 'sanitization' ], 10, 2 );
	}

	/**
	 * Display the field.
	 *
	 * @param  \CMB2_Field $field         CMB2 Field instance.
	 * @param  array|mixed $escaped_value Escaped value.
	 * @return void
	 */
	public function display( $field, $escaped_value ) {
		wp_simple_iconfonts_field( array(
			'id'    => esc_attr( $field->_id() ),
			'name'  => esc_attr( $field->_name() ),
			'value' => $escaped_value,
		) );
	}

	/**
	 * Sanitization field value before save.
	 *
	 * @param  mixed|array $override_value Not used.
	 * @param  mixed|array $value          Raw field value.
	 * @return array
	 */
	public function sanitization( $override_value, $value ) {
		return wp_parse_args( $value, array(
			'type' => '',
			'icon' => '',
		));
	}
}
