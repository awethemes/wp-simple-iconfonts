<?php
namespace WP_Simple_Iconfonts\Extractor;

/**
 * Typicons Extractor
 *
 * @link http://typicons.com/
 */
class Typicons_Extractor extends Extractor {
	/**
	 * The relative path of metadata file.
	 *
	 * @var string
	 */
	protected $metadata = 'src/font/typicons.css';

	/**
	 * The relative path of stylesheet file.
	 *
	 * @var string
	 */
	protected $stylesheet = 'src/font/typicons.css';

	/**
	 * The relative path of fonts directory.
	 *
	 * @var string
	 */
	protected $fonts_directory = 'src/font';

	/**
	 * List of files, directories match with icon pack.
	 *
	 * @var array
	 */
	protected $directory_structure = array( 'src/', 'config.yml', 'src/font/typicons.css' );

	/**
	 * Doing extract icon pack data.
	 *
	 * @param  Result $result Extractor icon pack instance.
	 * @return void
	 */
	protected function doing_extract( Result $result ) {
		$result->id = 'typcn';
		$result->name = 'Typicons';
		$result->version = '2.0';

		$handle = @fopen( $result->metadata_path, 'r' );
		while ( ! feof( $handle ) ) {
			$line = trim( fgets( $handle, 1024 ) );

			if ( preg_match( '/^\.typcn-([a-z0-9_-]+):before\s?\{$/', $line, $matches ) ) {
				$result->add_icon( 'typcn typcn-' . $matches[1], $matches[1] );
			}
		} // End while().
	}
}
