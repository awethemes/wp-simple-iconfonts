<?php
namespace WP_Simple_Iconfonts\Extractor;

/**
 * Foundation Extractor
 *
 * @link http://zurb.com/playground/foundation-icon-fonts-3
 */
class Foundation_Extractor extends Extractor {
	/**
	 * The relative path of metadata file.
	 *
	 * @var string
	 */
	protected $metadata = 'foundation-icons.css';

	/**
	 * The relative path of stylesheet file.
	 *
	 * @var string
	 */
	protected $stylesheet = 'foundation-icons.css';

	/**
	 * The relative path of fonts directory.
	 *
	 * @var string
	 */
	protected $fonts_directory = '';

	/**
	 * List of files, directories match with icon pack.
	 *
	 * @var array
	 */
	protected $directory_structure = array( 'svgs/', 'foundation-icons.css', 'preview.html' );

	/**
	 * Doing extract icon pack data.
	 *
	 * @param  Result $result Extractor icon pack instance.
	 * @return void
	 */
	protected function doing_extract( Result $result ) {
		$result->id = 'fi';
		$result->name = 'Foundation Icon Fonts 3';
		$result->version = '3.0';

		$handle = @fopen( $result->metadata_path, 'r' );
		while ( ! feof( $handle ) ) {
			$line = trim( fgets( $handle, 1024 ) );

			if ( preg_match( '/^\.fi-([a-z0-9_-]+):before\s{.*}$/', $line, $matches ) ) {
				$result->add_icon( 'fi-' . $matches[1], $matches[1] );
			}
		} // End while().
	}
}
