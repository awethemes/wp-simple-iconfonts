<?php
namespace WP_Simple_Iconfonts\Extractor;

/**
 * Icomoon extractor provider.
 *
 * @link https://icomoon.io/app
 */
abstract class Icomoon_Extractor extends Extractor {
	/**
	 * The relative path of metadata file.
	 *
	 * @var string
	 */
	protected $metadata = 'selection.json';

	/**
	 * The relative path of stylesheet file.
	 *
	 * @var string
	 */
	protected $stylesheet = 'style.css';

	/**
	 * List of files, directories match with icon pack.
	 *
	 * @var array
	 */
	protected $directory_structure = array( 'demo-files/', 'selection.json', 'style.css', 'demo.html' );

	/**
	 * Doing extract icon pack data.
	 *
	 * @param  Result $result Extractor icon pack instance.
	 * @return void
	 */
	protected function doing_extract( Result $result ) {
		$json = $result->get_metadata_contents();
		if ( ! is_array( $json ) || empty( $json['icons'] ) || empty( $json['preferences']['fontPref'] ) ) {
			return;
		}

		$fontpref = $json['preferences']['fontPref'];

		if ( ! empty( $json['metadata']['name'] ) ) {
			$result->name = sanitize_text_field( $json['metadata']['name'] );
		} elseif ( ! empty( $fontpref['metadata']['fontFamily'] ) ) {
			$result->name = sanitize_text_field( $fontpref['metadata']['fontFamily'] );
		} else {
			return; // We need a font name.
		}

		$result->id = sanitize_key( $result->name );

		/*if ( in_array( $result->id, ['icon', 'icon-font' ] ) ) {
			$result->id = uniqid( 'iconmoon-', false );
		}*/

		$result->version = sprintf( '%s.%s',
			isset( $fontpref['metadata']['majorVersion'] ) ? $fontpref['metadata']['majorVersion'] : 1,
			isset( $fontpref['metadata']['minorVersion'] ) ? $fontpref['metadata']['minorVersion'] : 0
		);

		$this->doing_extract_icons( $result, $json );
	}

	/**
	 * Doing extract icons from metatata.
	 *
	 * TODO: Maybe have trouble in some cases in this extractor.
	 *
	 * @param  Result $result Extract result object.
	 * @param  array  $json   Json metatata.
	 */
	protected function doing_extract_icons( Result $result, array $json ) {
		$fontconfig = $json['preferences']['fontPref'];

		foreach ( $json['icons'] as $raw_icon ) {
			if ( ! isset( $raw_icon['properties']['name'] ) ) {
				continue;
			}

			$postfix = isset( $fontconfig['postfix'] ) ? $fontconfig['postfix'] : '';
			$prefix = $fontconfig['prefix'];

			if ( $result->id && 0 === strpos( $prefix, 'icon' ) ) {
				$prefix = $result->id . '-';
			}

			$icon_name = $raw_icon['properties']['name'];
			$icon_class = $prefix . $icon_name . $postfix;

			if ( isset( $fontconfig['selector'] ) && 'class' === $fontconfig['selector'] && $fontconfig['classSelector'] ) {
				$selector = str_replace( '.', '', $fontconfig['classSelector'] );

				if ( $result->id && 0 === strpos( $selector, 'icon' ) ) {
					$selector = $result->id;
				}

				$icon_class = $selector . ' ' . $icon_class;
			}

			$result->add_icon( $icon_class, $icon_name );
		}
	}
}
