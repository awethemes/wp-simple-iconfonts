<?php
namespace WP_Simple_Iconfonts\Support;

class WP_Simple_Iconfonts_Control extends \WP_Customize_Control {
	/**
	 * The control type.
	 *
	 * @access public
	 * @var string
	 */
	public $type = 'simple_iconfonts';

	/**
	 * Enqueue control related scripts/styles.
	 *
	 * @access public
	 */
	public function enqueue() {
		wp_enqueue_media();

		foreach ( wp_simple_iconfonts()->all() as $iconpack ) {
			$iconpack->enqueue_styles();
		}

		wp_enqueue_style( 'simple-iconfonts-picker' );
		wp_enqueue_script( 'simple-iconfonts-picker' );
		wp_enqueue_script( 'simple-iconfonts-customize' );
	}

	/**
	 * Render the control's content.
	 *
	 * Control content can alternately be rendered in JS. See WP_Customize_Control::print_template().
	 */
	public function render_content() {
		?><label>
			<?php if ( ! empty( $this->label ) ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif; ?>

			<?php if ( ! empty( $this->description ) ) : ?>
				<span class="description customize-control-description"><?php echo $this->description; ?></span>
			<?php endif; ?>

			<?php
			wp_simple_iconfonts_field( array(
				'id'    => esc_attr( $this->id ),
				'name'  => esc_attr( $this->id ),
				'value' => $this->value(),
			) );
			?>
		</label><?php
	}
}
