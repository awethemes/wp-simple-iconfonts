<?php
namespace WP_Simple_Iconfonts\Support;

class ACF5_Simple_Iconfonts_Field extends ACF_Simple_Iconfonts_Field {
	/**
	 * Create the HTML interface for this field.
	 *
	 * @param  array $field The field arguments.
	 * @return void
	 */
	public function render_field( $field ) {
		$this->create_field( $field );
	}

	/**
	 * Create extra options for this field.
	 *
	 * @param  array $field An array holding all the field's data.
	 * @return void
	 */
	public function render_field_settings( $field ) {
		acf_render_field_setting( $field, array(
			'label' => esc_html__( 'Default Icon', 'wp_simple_iconfonts' ),
			'type'  => $this->name,
			'name'  => 'default_icon',
		));
	}
}
