<?php
namespace WP_Simple_Iconfonts\Extractor;

/**
 * Map Icons Extractor
 *
 * @link http://map-icons.com/
 */
class Mapicons_Extractor extends Extractor {
	/**
	 * The relative path of metadata file.
	 *
	 * @var string
	 */
	protected $metadata = 'dist/sass/map-icons-variables.scss';

	/**
	 * The relative path of stylesheet file.
	 *
	 * @var string
	 */
	protected $stylesheet = 'dist/css/map-icons.css';

	/**
	 * The relative path of fonts directory.
	 *
	 * @var string
	 */
	protected $fonts_directory = 'dist/fonts';

	/**
	 * List of files, directories match with icon pack.
	 *
	 * @var array
	 */
	protected $directory_structure = array( 'dist/', 'docs/', 'src/', 'index.html', 'dist/css/map-icons.css' );

	/**
	 * Doing extract icon pack data.
	 *
	 * @param  Result $result Extractor icon pack instance.
	 * @return void
	 */
	protected function doing_extract( Result $result ) {
		$result->id = 'map-icon';
		$result->name = 'Map Icons';
		$result->version = '3.0';

		$handle = @fopen( $result->metadata_path, 'r' );
		while ( ! feof( $handle ) ) {
			$line = trim( fgets( $handle, 1024 ) );

			if ( preg_match( '/^\$map-icon-([a-z0-9_-]+)\:.*\;$/', $line, $matches ) ) {
				$result->add_icon( 'map-icon-' . $matches[1], $matches[1] );
			}
		} // End while().
	}
}
