<?php
namespace WP_Simple_Iconfonts\Support;

class CMB_Simple_Iconfonts_Field extends \CMB_Field {
	/**
	 * Display the field.
	 *
	 * @see CMB_Field::html()
	 */
	public function html() {
		wp_simple_iconfonts_field( array(
			'id'    => $this->id,
			'name'  => $this->get_the_name_attr(),
			'value' => $this->get_value(),
		) );
	}

	/**
	 * Get multiple values for a field.
	 *
	 * @return array
	 */
	public function &get_values() {
		$values = ( ! $this->args['repeatable'] ) ? array( $this->values ) : $this->values;

		return $values;
	}

	/**
	 * Parse save values.
	 *
	 * When used as a sub-field of a `group` field, wrap the values with array.
	 *
	 * @return void
	 */
	public function parse_save_values() {
		if ( ! empty( $this->parent ) ) {
			$this->values = array( $this->values );
		}
	}
}
