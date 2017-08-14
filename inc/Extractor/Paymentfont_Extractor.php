<?php
namespace WP_Simple_Iconfonts\Extractor;

/**
 * PaymentFont ExtractorF
 *
 * @link http://paymentfont.io
 */
class Paymentfont_Extractor extends Fontawesome_Extractor {
	/**
	 * The relative path of metadata file.
	 *
	 * @var string
	 */
	protected $metadata = 'sass/_icons.scss';

	/**
	 * The relative path of stylesheet file.
	 *
	 * @var string
	 */
	protected $stylesheet = 'css/paymentfont.css';

	/**
	 * List of files, directories match with iconpack.
	 *
	 * @var array
	 */
	protected $directory_structure = array( 'fonts/', 'sass/', 'css/paymentfont.css' );

	/**
	 * Icon prefix name.
	 *
	 * @var string
	 */
	protected $prefix = 'pf-';

	/**
	 * Src icon prefix name.
	 *
	 * @var string
	 */
	protected $src_prefix = '.#{$pf-css-prefix}';

	/**
	 * Doing extract icon pack data.
	 *
	 * @param  Result $result Extractor icon pack instance.
	 * @return void
	 */
	protected function doing_extract( Result $result ) {
		$result->id = 'pf';
		$result->name = 'PaymentFont';

		$this->doing_extract_icons( $result );

		// Extract version.
		$variable_contents = file_get_contents( $this->directory . 'sass/_variables.scss' );
		if ( preg_match( '/\$pf-version:\s+"([0-9.]+)"/', $variable_contents, $matches ) ) {
			$result->version = trim( $matches[1] );
		}
	}
}
