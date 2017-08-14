<?php
namespace WP_Simple_Iconfonts\Extractor;

/**
 * Themify Extractor
 *
 * @link https://themify.me/themify-icons
 */
class Themify_Extractor extends Extractor {
	/**
	 * The relative path of metadata file.
	 *
	 * @var string
	 */
	protected $metadata = 'themify-icons.css';

	/**
	 * The relative path of stylesheet file.
	 *
	 * @var string
	 */
	protected $stylesheet = 'themify-icons.css';

	/**
	 * List of files, directories match with icon pack.
	 *
	 * @var array
	 */
	protected $directory_structure = array( 'SVG/', 'demo-files/', 'index.html', 'themify-icons.css' );

	/**
	 * Doing extract icon pack data.
	 *
	 * @param  Result $result Extractor icon pack instance.
	 * @return void
	 */
	protected function doing_extract( Result $result ) {
		$result->id = 'ti';
		$result->name = 'Themify';
		$result->version = '1.0';

		$handle = @fopen( $result->metadata_path, 'r' );
		while ( ! feof( $handle ) ) {
			$line = trim( fgets( $handle, 1024 ) );

			if ( preg_match( '/^\.ti-([a-z0-9_-]+):before/', $line, $matches ) ) {
				$result->add_icon( 'ti-' . $matches[1], $matches[1] );
			}
		} // End while().
	}
}
