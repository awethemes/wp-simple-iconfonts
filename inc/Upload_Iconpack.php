<?php
namespace WP_Simple_Iconfonts;

class Upload_Iconpack extends Iconpack {
	/**
	 * //
	 *
	 * @param string $id Upload iconpack ID.
	 */
	public function __construct( $id ) {
		if ( empty( $id ) ) {
			return;
		}

		$metadata = file_get_contents( wp_simple_iconfonts()->get_path( 'icons_dir' ) . $id . '/metadata.json' );
		$args = json_decode( $metadata, true );

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
