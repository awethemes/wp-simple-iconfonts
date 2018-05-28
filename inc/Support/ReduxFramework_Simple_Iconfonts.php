<?php

/**
 * Important: Class must have no namespace.
 *
 * @package WP_Simple_Iconfonts
 */
class ReduxFramework_Simple_Iconfonts {
	/**
	 * Field constructor.
	 *
	 * @param array           $field  An array field values.
	 * @param mixed           $value  Field value.
	 * @param \ReduxFramework $parent Parent instance.
	 */
	public function __construct( $field = array(), $value = '', $parent ) {
		$this->parent = $parent;
		$this->field  = $field;
		$this->value  = $value;
	}

	/**
	 * Enqueue scripts.
	 */
	public function enqueue() {
		wp_enqueue_media();

		foreach ( wp_simple_iconfonts()->all() as $iconpack ) {
			$iconpack->enqueue_styles();
		}

		wp_enqueue_style( 'simple-iconfonts-picker' );
		wp_enqueue_script( 'simple-iconfonts-picker' );
	}

	/**
	 * Field render.
	 */
	public function render() {
		$default = isset( $this->field['default'] ) ? $this->field['default'] : array();

		$values = wp_parse_args( $this->value, array(
			'type' => '',
			'icon' => '',
		));

		if ( empty( $values['type'] ) && empty( $values['icon'] ) &&
			! empty( $default['type'] ) && ! empty( $default['icon'] ) ) {
			$values = $default;
		}

		wp_simple_iconfonts_field( array(
			'id'    => esc_attr( $this->field['id'] ),
			'name'  => esc_attr( $this->field['name'] . $this->field['name_suffix'] ),
			'value' => $values,
		) );
	}
}
