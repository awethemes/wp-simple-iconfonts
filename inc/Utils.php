<?php
namespace WP_Simple_Iconfonts;

use WP_Error;
use DOMDocument;
use FilesystemIterator;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class Utils {
	/**
	 * Get image mime types.
	 *
	 * @return array
	 */
	public static function get_image_mime_types() {
		$mime_types = array_filter( get_allowed_mime_types(), function( $type ) {
			return false !== strpos( $type, 'image/' );
		});

		return apply_filters( 'wp_simple_iconfonts_image_mime_types', $mime_types );
	}

	/**
	 * Minify CSS by remove comments, whitespaces.
	 *
	 * @see https://www.w3.org/TR/CSS2/grammar.html#scanner
	 *
	 * @param  string $styles The original CSS.
	 * @return string
	 */
	public static function minify_css( $styles ) {
		$styles = preg_replace( '#\/\*[^*]*\*+([^/*][^*]*\*+)*\/#', '', $styles ); // Remove comments.
		$styles = preg_replace( '/\s+/', ' ', $styles ); // Remove whitespace.

		return $styles;
	}

	/**
	 * Scan recursive a directory.
	 *
	 * @param  string $directory A real directory location.
	 * @param  mixed  $flags     RecursiveDirectoryIterator flags searching.
	 * @return RecursiveIteratorIterator
	 */
	public static function scandir( $directory, $flags = null ) {
		$flags = ! is_null( $flags ) ? $flags : FilesystemIterator::KEY_AS_PATHNAME | FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::SKIP_DOTS;

		$iterator = new RecursiveDirectoryIterator( $directory, $flags );

		return new RecursiveIteratorIterator( $iterator, RecursiveIteratorIterator::SELF_FIRST );
	}

	/**
	 * Unzip a file and return correctly directory.
	 *
	 * @param  string $zipfile     Zip file path.
	 * @param  string $destination The base directory where extract to.
	 * @return WP_Error|array
	 */
	public static function unzip( $zipfile, $destination ) {
		$filesystem = static::wp_filesystem();

		// We need a working directory first.
		$working_directory = trailingslashit( $destination . '/' . pathinfo( $zipfile, PATHINFO_FILENAME ) );
		$unzip_result = unzip_file( $zipfile, $working_directory );

		if ( is_wp_error( $unzip_result ) ) {
			return $unzip_result; // WP_Error instance.
		}

		// Find the right folder.
		$source_files = array_keys( $filesystem->dirlist( $working_directory ) );

		if ( count( $source_files ) === 0 ) {
			return new WP_Error( 'incompatible_archive', esc_html__( 'Incompatible archive', 'wp_simple_iconfonts' ) );
		}

		if ( 1 === count( $source_files ) && $filesystem->is_dir( $working_directory . $source_files[0] ) ) {
			$directory = $working_directory . trailingslashit( $source_files[0] );
		} else {
			$directory = $working_directory;
		}

		return array( $directory, $working_directory );
	}

	/**
	 * Initialized (if need) and return the Wordpress filesystem.
	 *
	 * @return \WP_Filesystem_Base
	 */
	public static function wp_filesystem() {
		global $wp_filesystem;

		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';
			WP_Filesystem();
		}

		return $wp_filesystem;
	}

	/**
	 * Extract glyph icons from SVG-fonts file.
	 *
	 * @param  string $svg_path The SVG file path.
	 * @param  string $charset  The DOMDocument load charset.
	 * @param  int    $options  The DOMDocument load options.
	 * @return array|null
	 */
	public static function glyph_extract( $svg_path, $charset = 'UTF-8', $options = LIBXML_NONET ) {
		if ( ! is_file( $svg_path ) || ! is_readable( $svg_path ) ) {
			return null;
		}

		$internal_errors = libxml_use_internal_errors( true );
		$disable_entities = libxml_disable_entity_loader( true );

		$dom = new DOMDocument( '1.0', $charset );
		$dom->validateOnParse = true; // @codingStandardsIgnoreLine

		$contents = @file_get_contents( $svg_path );
		if ( '' !== trim( $contents ) ) {
			@$dom->loadXML( $contents, $options );
		}

		libxml_use_internal_errors( $internal_errors );
		libxml_disable_entity_loader( $disable_entities );

		$fontspec = $dom->getElementsByTagName( 'font' )->item( 0 );
		$fontface = $dom->getElementsByTagName( 'font-face' )->item( 0 );

		$svgid = strtolower( $fontspec->getAttribute( 'id' ) );
		$default_char_width  = $fontspec->getAttribute( 'horiz-adv-x' );
		$default_char_height = $fontface->getAttribute( 'units-per-em' );
		$default_char_ascent = $fontface->getAttribute( 'ascent' );

		$glyphs = $dom->getElementsByTagName( 'glyph' );
		$data_on_glyphs = [];

		foreach ( $glyphs as $glyph ) {
			$icon_code  = $glyph->getAttribute( 'unicode' );

			// Some glyphs matched without a unicode value so we should ignore them.
			if ( empty( $icon_code ) ) {
				continue;
			}

			// Get unicode hex representation of a unicode character.
			// @thanks https://github.com/madeyourday/SVG-Icon-Font-Generator/blob/v0.1.3/src/MadeYourDay/SVG/IconFontGenerator.php#L247 .
			if ( ! is_string( $icon_code ) || mb_strlen( $icon_code, 'utf-8' ) === 1 ) {
				$unicode = unpack( 'N', mb_convert_encoding( $icon_code, 'UCS-4BE', 'UTF-8' ) );
				$icon_code = dechex( $unicode[1] );
			}

			// Remove "'&#x" from begin in some case.
			if ( 0 === mb_strpos( $icon_code, '&#x' ) ) {
				$icon_code = mb_substr( $icon_code, 0, 3 );
			}

			// Continue extract data.
			$path_data  = $glyph->getAttribute( 'd' );
			$glyph_name = $glyph->getAttribute( 'glyph-name' );

			$translate_offset = $default_char_ascent;
			$custom_width_match = $glyph->getAttribute( 'horiz-adv-x' );
			$content_width = $custom_width_match ? $custom_width_match : $default_char_width;

			// Skip empty-looking glyphs.
			if ( empty( $icon_code ) || empty( $path_data ) || strlen( $path_data ) < 10 ) {
				continue;
			}

			$data_on_glyphs[] = array(
				'id'    => "svg-{$svgid}-{$glyph_name}",
				'name'  => $glyph_name ? $glyph_name : $icon_code,
				'group' => '',
				'code'  => $icon_code,
				'path'  => $path_data,
				'svg'   => "<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 {$content_width} {$default_char_height}\"><g transform=\"scale(1,-1) translate(0 -{$translate_offset})\"><path d=\"{$path_data}\"/></g></svg>",
				'svg_symbol' => "<symbol id=\"svg-{$svgid}-{$glyph_name}\" viewBox=\"0 0 {$content_width} {$default_char_height}\"><g transform=\"scale(1,-1) translate(0 -{$translate_offset})\"><path d=\"{$path_data}\"/></g></symbol>",
			);
		}

		return $data_on_glyphs;
	}
}
