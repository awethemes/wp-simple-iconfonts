<?php
namespace WP_Simple_Iconfonts;

class Imported_Iconpack extends Iconpack {
	/**
	 * TODO: ...
	 *
	 * @param string $id Upload iconpack ID.
	 */
	public function __construct( $id ) {
		if ( empty( $id ) ) {
			return;
		}

		$metadata = wp_simple_iconfonts()->get_path( 'icons_dir' ) . $id . '/metadata.json';
		if ( ! file_exists( $metadata ) ) {
			return;
		}

		$args = json_decode( file_get_contents( $metadata ), true );
		if ( json_last_error() ) {
			return;
		}

		parent::__construct( $args );
	}

	/**
	 * Register the wp stylesheet.
	 *
	 * @return void
	 */
	public function register_styles() {
		wp_register_style( $this->id, wp_simple_iconfonts()->get_path( 'icons_url' ) . $this->id . '/style.css' );
	}

	/**
	 * Enqueue the wp stylesheet.
	 *
	 * @return void
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->id );
	}
}
