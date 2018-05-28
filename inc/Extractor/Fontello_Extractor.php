<?php
namespace WP_Simple_Iconfonts\Extractor;

/**
 * Fontello Extractor
 *
 * @link http://fontello.com
 */
class Fontello_Extractor extends Extractor {
	/**
	 * The relative path of metadata file.
	 *
	 * @var string
	 */
	protected $metadata = 'config.json';

	/**
	 * The relative path of stylesheet file.
	 *
	 * @var string
	 */
	protected $stylesheet = 'css/fontello.css';

	/**
	 * The relative path of fonts directory.
	 *
	 * @var string
	 */
	protected $fonts_directory = 'font';

	/**
	 * List of files, directories match with icon pack.
	 *
	 * @var array
	 */
	protected $directory_structure = array( 'css/', 'font/', 'config.json', 'demo.html' );

	/**
	 * Before extractor, return an array metadata, stylesheet, fonts path.
	 *
	 * @param  array $args Reference $args variable.
	 * @return void
	 */
	protected function before_extract( array &$args ) {
		parent::before_extract( $args );

		// Try find stylesheet path, because fontello use dynamic stylesheet file name.
		if ( empty( $args['stylesheet_path'] ) && is_file( $args['metadata_path'] ) ) {
			$json = json_decode( file_get_contents( $args['metadata_path'] ), true );

			if ( empty( $json['name'] ) ) {
				return;
			}

			$name = $json['name'];
			if ( is_file( "{$this->directory}/css/{$name}.css" ) ) {
				$args['stylesheet_path'] = "{$this->directory}/css/{$name}.css";
			}
		}
	}

	/**
	 * Doing extract icon pack data.
	 *
	 * @param  Result $result Extractor icon pack instance.
	 * @return void
	 */
	protected function doing_extract( Result $result ) {
		$json = $result->get_metadata_contents();

		// Checking valid metadata.
		if ( ! is_array( $json ) || ! isset( $json['name'] ) || empty( $json['glyphs'] ) ) {
			return;
		}

		$result->name = empty( $json['name'] ) ? 'fontello' : sanitize_text_field( $json['name'] );
		$result->id = sanitize_key( $result->name );

		$this->doing_extract_icons( $result, $json );
	}

	/**
	 * Doing extract icons from metatata.
	 *
	 * @param  Result $result Extract result object.
	 * @param  array  $json   Json metatata.
	 */
	protected function doing_extract_icons( Result $result, array $json ) {
		if ( ! is_array( $json['glyphs'] ) || empty( $json['css_prefix_text'] ) ) {
			return;
		}

		foreach ( $json['glyphs'] as $raw_icon ) {
			if ( ! isset( $raw_icon['css'] ) ) {
				continue;
			}

			if ( isset( $json['css_use_suffix'] ) && true == $json['css_use_suffix'] ) {
				$icon_class = $raw_icon['css'] . $json['css_prefix_text'];
			} else {
				$icon_class = $json['css_prefix_text'] . $raw_icon['css'];
			}

			$result->add_icon( $icon_class, $raw_icon['css'] );
		}
	}
}
