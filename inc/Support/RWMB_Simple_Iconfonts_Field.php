<?php

/**
 * Important: Class must have no namespace.
 *
 * @package WP_Simple_Iconfonts
 */
class RWMB_Simple_Iconfonts_Field extends RWMB_Field {
	/**
	 * Get field HTML.
	 *
	 * @param mixed $meta  Meta value.
	 * @param array $field Field parameters.
	 *
	 * @return string
	 */
	public static function html( $meta, $field ) {
		$html = '';

		ob_start();
		wp_simple_iconfonts_field( array(
			'id'    => esc_attr( $field['id'] ),
			'name'  => esc_attr( $field['field_name'] ),
			'value' => $meta,
		) );

		$html .= ob_get_clean();

		return $html;
	}

	/**
	 * Normalize parameters for field.
	 *
	 * @param array $field The field parameters.
	 * @return array
	 */
	public static function normalize( $field ) {
		$field = parent::normalize( $field );

		$field['std'] = wp_parse_args( $field['std'], array(
			'type' => '',
			'icon' => '',
		));

		return $field;
	}
}
