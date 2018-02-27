<?php
namespace WP_Simple_Iconfonts\Extractor;

use WP_Simple_Iconfonts\Utils;

/**
 * FontAwesome 5 extractor provider.
 *
 * @link http://fontawesome.io
 */
class Fontawesome5_Extractor extends Extractor {
	/**
	 * The relative path of metadata file.
	 *
	 * @var string
	 */
	protected $metadata = 'web-fonts-with-css/webfonts/fa-solid-900.svg';

	/**
	 * The relative path of stylesheet file.
	 *
	 * @var string
	 */
	protected $stylesheet = 'web-fonts-with-css/css/fontawesome-all.css';

	/**
	 * The relative path of fonts directory.
	 *
	 * @var string
	 */
	protected $fonts_directory = 'web-fonts-with-css/webfonts';

	/**
	 * List of files, directories match with iconpack.
	 *
	 * @var array
	 */
	protected $directory_structure = array( 'svg-with-js/', 'web-fonts-with-css/', 'web-fonts-with-css/css/fontawesome-all.css' );

	/**
	 * {@inheritdoc}
	 */
	protected function before_extract( array &$args ) {
		parent::before_extract( $args );

		// Reset the font_paths.
		$args['font_paths'] = [];

		// Scan fonts directory and get font paths.
		foreach ( Utils::scandir( $this->directory . $this->fonts_directory ) as $fontpath => $fileinfo ) {
			$extension = $fileinfo->getExtension();

			if ( ! in_array( $extension, $this->webfont_extensions ) ) {
				continue;
			}

			if ( isset( $args['font_paths'][ $extension ] ) ) {
				$args['font_paths'][ $extension ][] = $fontpath;
			} else {
				$args['font_paths'][ $extension ] = array( $fontpath );
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
		$result->id = 'fa5';
		$result->name = 'Font Awesome 5';

		$svginfo = Utils::glyph_extract( $result->metadata_path );
		foreach ( $svginfo as $meta ) {
			$result->add_icon( 'fas fa-' . $meta['name'], $meta['name'] );
		}

		$svginfo_brands = Utils::glyph_extract( dirname( $result->metadata_path ) . '/fa-brands-400.svg' );
		foreach ( $svginfo_brands as $meta ) {
			$result->add_icon( 'fab fa-' . $meta['name'], $meta['name'] );
		}

		// Extract version.
		$variable_contents = file_get_contents( $this->directory . 'web-fonts-with-css/scss/_variables.scss' );
		if ( preg_match( '/\$fa-version:\s+"([0-9.]+)"/', $variable_contents, $matches ) ) {
			$result->version = trim( $matches[1] );
		}
	}

	/**
	 * Rewrite the stylesheet content.
	 *
	 * @param  string $stylesheet The content.
	 * @return string
	 */
	public function rewrite_stylesheet( $stylesheet ) {
		return str_replace( 'webfonts/', '', $stylesheet );
	}
}
