<?php
namespace WP_Simple_Iconfonts\Extractor;

/**
 * Pixeden (free version) Extractor
 *
 * @link http://www.pixeden.com/icon-fonts
 */
class Pixeden_Extractor extends Icomoon_Extractor {
	/**
	 * The relative path of metadata file.
	 *
	 * @var string
	 */
	protected $metadata = 'icomoon/selection.json';

	/**
	 * List of files, directories match with icon pack.
	 *
	 * @var array
	 */
	protected $directory_structure = array( 'icomoon/', 'assets/', 'reference.html', 'documentation.html' );

	/**
	 * Before extractor, return an array metadata, stylesheet, fonts path.
	 *
	 * @param  array $args Reference $args variable.
	 * @return void
	 */
	protected function before_extract( array &$args ) {
		if ( is_file( $this->directory . $this->metadata ) ) {
			$json = json_decode( file_get_contents( $this->directory . $this->metadata ), true );

			if ( ! empty( $json['metadata']['name'] ) ) {
				$font_name = strtolower( $json['metadata']['name'] );

				$this->stylesheet = sprintf( '%1$s/css/%1$s.css', $font_name );
				$this->fonts_directory = $font_name . '/fonts';
			}

			parent::before_extract( $args );
		}
	}
}
