<?php
namespace WP_Simple_Iconfonts\Extractor;

/**
 * Extract Simple Iconfonts own format.
 */
class Simple_Iconfonts_Extractor extends Extractor {
	/**
	 * List of files, directories match with icon pack.
	 *
	 * @var array
	 */
	protected $directory_structure = array( 'fonts/', 'index.php', 'metadata.json', 'style.css' );

	/**
	 * Webbfont extensions.
	 *
	 * @var array
	 */
	protected $webfont_extensions = array( 'svg', 'otf', 'eot', 'ttf', 'woff', 'woff2', 'png' );

	/**
	 * Doing extract icon pack data.
	 *
	 * @param  Result $result Extractor icon pack instance.
	 * @return void
	 */
	protected function doing_extract( Result $result ) {
		$json = $result->get_metadata_contents();

		if ( ! is_array( $json ) || empty( $json['id'] ) || empty( $json['icons'] ) ) {
			return;
		}

		// TODO: Write validate metadata.json contents format.
		$result->id = sanitize_text_field( $json['id'] );
		$result->name = empty( $json['name'] ) ? $result->id : sanitize_text_field( $json['name'] );
		$result->version = isset( $json['version'] ) ? sanitize_text_field( $json['version'] ) : null;

		if ( ! empty( $json['groups'] ) && is_array( $json['groups'] ) ) {
			foreach ( (array) $json['groups'] as $raw_group ) {
				if ( isset( $raw_group['id'], $raw_group['name'] ) ) {
					$result->groups[] = $raw_group;
				}
			}
		}

		foreach ( (array) $json['icons'] as $raw_icon ) {
			if ( isset( $raw_icon['id'], $raw_icon['name'] ) ) {
				$result->icons[] = $raw_icon;
			}
		}
	}
}
