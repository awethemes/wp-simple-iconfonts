<?php
namespace WP_Simple_Iconfonts\Support;

class JS_Composer_Simple_Iconfonts_Param {
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
		add_action( 'vc_backend_editor_enqueue_js_css', array( $this, 'enqueue_admin_scripts' ) );
		add_action( 'vc_frontend_editor_enqueue_js_css', array( $this, 'enqueue_frontend_scripts' ) );

		vc_add_shortcode_param(
			'simple_iconfonts',
			array( $this, 'display' ),
			wp_simple_iconfonts()->get_plugin_url( 'js/simple-iconfonts-jscomposer.js' )
		);
	}

	/**
	 * Enqueue admin scripts.
	 *
	 * @return void
	 */
	public function enqueue_admin_scripts() {
		wp_enqueue_media();

		foreach ( wp_simple_iconfonts()->all() as $iconpack ) {
			$iconpack->enqueue_styles();
		}

		wp_enqueue_style( 'simple-iconfonts-picker' );
		wp_enqueue_script( 'simple-iconfonts-picker' );
	}

	/**
	 * Enqueue frontend scripts.
	 *
	 * @return void
	 */
	public function enqueue_frontend_scripts() {
		wp_simple_iconfonts()->_register_admin_scripts();
		$this->enqueue_admin_scripts();
	}

	/**
	 * Display param HTML.
	 *
	 * @param  array  $settings Array of param settings.
	 * @param  string $value    Param value.
	 * @return string
	 */
	public function display( $settings, $value ) {
		if ( is_null( $value ) && isset( $settings['default'] ) ) {
			$value = $settings['default'];
		}

		$raw_value = $value;

		$value = wp_parse_args( vc_parse_multi_attribute( $value ), array(
			'type' => '',
			'icon' => '',
		));

		ob_start();
		wp_simple_iconfonts_field( array(
			'id'    => esc_attr( $settings['param_name'] ),
			'name'  => esc_attr( $settings['param_name'] ),
			'value' => $value,
		) );

		?><input type="hidden" value="<?php echo esc_attr( $raw_value ); ?>" name="<?php echo esc_attr( $settings['param_name'] ); ?>"  class="wpb_vc_param_value <?php echo esc_attr( $settings['param_name'] . ' ' . $settings['type'] ); ?>"><?php
		return ob_get_clean();
	}
}
