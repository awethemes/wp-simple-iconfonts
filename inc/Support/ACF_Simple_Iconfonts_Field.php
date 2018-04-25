<?php
namespace WP_Simple_Iconfonts\Support;

class ACF_Simple_Iconfonts_Field extends \acf_field {
	/**
	 * Constructor the field.
	 */
	public function __construct() {
		$this->name = 'simple_iconfonts';

		$this->label = esc_html__( 'Icon Picker', 'wp_simple_iconfonts' );

		$this->defaults = array( 'default_icon' => '' );

		parent::__construct();
	}

	/**
	 * Create the field.
	 *
	 * @param  array $field The field arguments.
	 * @return void
	 */
	public function create_field( $field ) {
		$default = isset( $field['default_icon'] ) ? $field['default_icon'] : array();

		$values = wp_parse_args( $field['value'], array(
			'type' => '',
			'icon' => '',
		));

		if ( empty( $values['type'] ) && empty( $values['icon'] ) &&
			! empty( $default['type'] ) && ! empty( $default['icon'] ) ) {
			$values = $default;
		}

		echo '<div class="acf-input-wrap">';

		wp_simple_iconfonts_field( array(
			'id'    => esc_attr( $field['id'] ),
			'name'  => esc_attr( $field['name'] ),
			'value' => $values,
		) );

		echo '</div>';
	}

	/**
	 * Create extra options for this field.
	 *
	 * @param  array $field An array holding all the field's data.
	 * @return void
	 */
	public function create_options( $field ) {
		?><tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
			<td class="label">
				<label><?php esc_html_e( 'Default Icon', 'wp_simple_iconfonts' ); ?></label>
			</td>

			<td>
			<?php
			/**
			 * Do action `acf/create_field`.
			 */
			do_action('acf/create_field', array(
				'type'  => $this->name,
				'name'  => 'fields[' . esc_attr( $field['name'] ) . '][default_icon]',
				'value' => $field['default_icon'],
			));
			?>
			</td>
		</tr><?php
	}

	/**
	 * Format value.
	 *
	 * @param  mixed $value   The value which was loaded from the database.
	 * @param  int   $post_id The post ID from which the value was loaded.
	 * @param  array $field   The field array holding all the field options.
	 * @return array
	 */
	public function format_value( $value, $post_id, $field ) {
		return wp_parse_args( $value, array(
			'type' => '',
			'icon' => '',
		));
	}

	/**
	 * Format value for api.
	 *
	 * @param  mixed $value   The value which was loaded from the database.
	 * @param  int   $post_id The post ID from which the value was loaded.
	 * @param  array $field   The field array holding all the field options.
	 * @return array
	 */
	public function format_value_for_api( $value, $post_id, $field ) {
		return $this->format_value( $value, $post_id, $field );
	}

	/**
	 * Enqueue scripts.
	 *
	 * @return void
	 */
	public function field_group_admin_enqueue_scripts() {
		wp_enqueue_media();

		foreach ( wp_simple_iconfonts()->all() as $iconpack ) {
			$iconpack->enqueue_styles();
		}

		wp_enqueue_style( 'simple-iconfonts-picker' );
		wp_enqueue_script( 'simple-iconfonts-picker' );
	}
}
