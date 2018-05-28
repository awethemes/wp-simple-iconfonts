<?php

use WP_Simple_Iconfonts\Support\WP_Simple_Iconfonts_Control;

/**
 * Important: Class must have no namespace.
 *
 * @package WP_Simple_Iconfonts
 */
class TitanFrameworkOptionSimple_Iconfonts extends TitanFrameworkOption {
	/**
	 * Display for options and meta.
	 */
	public function display() {
		$this->echoOptionHeader();

		wp_simple_iconfonts_field( array(
			'id'    => esc_attr( $this->getID() ),
			'name'  => esc_attr( $this->getID() ),
			'value' => $this->getValue(),
		) );

		$this->echoOptionFooter();
	}

	/**
	 * Cleans up the value before saving.
	 *
	 * @param  array|mixed $value The value.
	 * @return array
	 */
	public function cleanValueForSaving( $value ) {
		return wp_parse_args( $value, array(
			'type' => '',
			'icon' => '',
		));
	}

	/**
	 * Display for theme customizer.
	 *
	 * @param \WP_Customize_Manager     $wp_customize The customizer object.
	 * @param \TitanFrameworkCustomizer $section      The customizer section.
	 * @param int                       $priority     The display priority of the control.
	 */
	public function registerCustomizerControl( $wp_customize, $section, $priority = 1 ) {
		$wp_customize->add_control( new WP_Simple_Iconfonts_Control( $wp_customize, $this->getID(), array(
			'label'       => $this->settings['name'],
			'description' => $this->settings['desc'],
			'section'     => $section->settings['id'],
			'settings'    => $this->getID(),
			'priority'    => $priority,
		) ) );
	}
}
