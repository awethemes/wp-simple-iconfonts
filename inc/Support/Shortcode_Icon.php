<?php
namespace WP_Simple_Iconfonts\Support;

class Shortcode_Icon {
	/**
	 * Singleton class instance implementation.
	 *
	 * @var static
	 */
	protected static $instance;

	/**
	 * Set the globally available instance of the container.
	 *
	 * @return static
	 */
	public static function instance() {
		if ( is_null( static::$instance ) ) {
			static::$instance = new static;
		}

		return static::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		add_shortcode( 'wp_simple_iconfonts', array( $this, 'output_shortcode' ) );

		add_action( 'wp_enqueue_editor', array( $this, 'wp_enqueue_editor' ) );
		add_filter( 'mce_external_plugins', array( $this, 'enqueue_editor_scripts' ) );
		add_filter( 'mce_buttons', array( $this, 'register_buttons_editor' ) );
		add_action( 'print_media_templates', array( $this, 'print_media_templates' ) );
	}

	/**
	 * Enqueue scripts.
	 *
	 * @access private
	 */
	public function wp_enqueue_editor() {
		wp_enqueue_media();

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_style( 'simple-iconfonts-picker' );
		wp_enqueue_script( 'simple-iconfonts-picker' );

		foreach ( wp_simple_iconfonts()->all() as $iconpack ) {
			$iconpack->enqueue_styles();
		}
	}

	/**
	 * Enqueue editor scripts.
	 *
	 * @param  array $plugin_array The plugin array.
	 * @return array
	 *
	 * @access private
	 */
	public function enqueue_editor_scripts( $plugin_array ) {
		// Enqueue TinyMCE plugin script with its ID.
		$plugin_array['wp_simple_icons_button'] = wp_simple_iconfonts()->get_plugin_url( 'js/simple-iconfonts-editor.js' );

		return $plugin_array;
	}

	/**
	 * Register button editor.
	 *
	 * @param  array $buttons buttons.
	 * @return array
	 *
	 * @access private
	 */
	public function register_buttons_editor( $buttons ) {
		// Register buttons with their id.
		array_push( $buttons, 'wp_simple_icons_button' );

		return $buttons;
	}

	/**
	 * Get and print media templates from all types
	 *
	 * @access private
	 */
	public function print_media_templates() {
		include trailingslashit( __DIR__ ) . '/../views/shortcode-media-template.php';
	}

	/**
	 * Add shortcode: wp_simple_iconfonts_editor_shortcode
	 *
	 * @param array $atts atts.
	 */
	public function output_shortcode( $atts ) {
		$atts = shortcode_atts( array(
			'type'        => '',
			'icon'        => '',
			'url'         => '',
			'font_size'   => '',
			'font_weight' => '',
			'color'       => '',
		), $atts, 'wp_simple_iconfonts_editor_shortcode' );

		if ( ! $atts['type'] || ! $atts['icon'] ) {
			return;
		}

		if ( 'svg' === $atts['type'] || 'image' === $atts['type'] ) {
			echo wp_get_attachment_image( $atts['icon'] );
		} else {
			$css = '';
			$css .= $atts['font_size'] ? 'font-size:' . $atts['font_size'] . 'px;' : '';
			$css .= $atts['font_weight'] ? 'font-weight:' . $atts['font_weight'] . ';' : '';
			$css .= $atts['color'] ? 'color:' . $atts['color'] . ';' : '';

			$css = $css ? 'style="' . $css . '"' : '';

			printf( '<i class="%1$s %2$s" %3$s></i>', esc_attr( $atts['type'] ), esc_attr( $atts['icon'] ), $css ); // WPCS: xss ok.
		}
	}
}
