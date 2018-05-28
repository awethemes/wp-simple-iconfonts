<?php
namespace WP_Simple_Iconfonts;

use WP_Error;
use WP_Simple_Iconfonts\Extractor\Extractor;

class Installer {
	/**
	 * Fonticons instance.
	 *
	 * @var Iconfonts
	 */
	protected $iconfonts;

	/**
	 * Constructor Manager.
	 *
	 * @param Iconfonts $iconfonts Fonticons instance.
	 */
	public function __construct( Iconfonts $iconfonts ) {
		$this->iconfonts = $iconfonts;
	}

	/**
	 * Install icon pack form a directory.
	 *
	 * @param  string  $directory         Source directory.
	 * @param  string  $working_directory The directory we working on it.
	 * @param  boolean $delete_after      Delete directory or working_directory after that.
	 * @return Iconpack|WP_Error
	 */
	public function install( $directory, $working_directory = null, $delete_after = false ) {
		$extractor = $this->guest_extractor( $directory );

		if ( ! $extractor ) {
			$this->delete_directory( $delete_after, $directory, $working_directory );
			return new WP_Error( 'not_support', esc_html__( 'The icon pack is not supported', 'wp_simple_iconfonts' ) );
		}

		// Extract icon pack data.
		$result = $extractor->extract();
		if ( is_wp_error( $result ) ) {
			$this->delete_directory( $delete_after, $directory, $working_directory );
			return $result; // WP_Error instance.
		}

		// TODO: We need check version of icon.
		if ( $this->iconfonts->has( $result->id, true ) ) {
			$this->delete_directory( $delete_after, $directory, $working_directory );
			return new WP_Error( 'error', sprintf( esc_html__( 'The icon pack "%s" has been exists.', 'wp_simple_iconfonts' ),  $result->name ) );
		}

		$result->destination( $this->iconfonts->get_path( 'icons_dir' ) );

		$opt = get_option( '_wp_simple_iconfonts', array() );
		$opt = is_array( $opt ) ? $opt : array();
		$opt[ $result->id ] = true;

		$this->iconfonts->register( $iconpack = new Imported_Iconpack( $result->id ) );
		update_option( '_wp_simple_iconfonts', $opt );

		$this->delete_directory( $delete_after, $directory, $working_directory );

		return $iconpack;
	}

	/**
	 * Install icon form zip file format.
	 *
	 * @param  string $zipfile Zipfile path.
	 * @return Iconpack|WP_Error
	 */
	public function zip_install( $zipfile ) {
		$unzip_result = Utils::unzip( $zipfile, $this->iconfonts->get_path( 'tmp_dir' ) );

		if ( is_wp_error( $unzip_result ) ) {
			return $unzip_result;
		}

		list( $directory, $working_directory ) = $unzip_result;

		return $this->install( $directory, $working_directory, true );
	}

	/**
	 * Guest extractor by directory.
	 *
	 * @param  string $directory Icon font directory.
	 * @return Extractor|null
	 */
	public function guest_extractor( $directory ) {
		foreach ( $this->iconfonts->get_extractors() as $class ) {
			if ( ! class_exists( $class ) ) {
				continue;
			}

			$extractor = new $class( $directory );
			if ( ! $extractor instanceof Extractor ) {
				continue;
			}

			if ( $extractor->check() ) {
				return $extractor;
			}
		}
	}

	/**
	 * Utils: Delete directory and working directory.
	 *
	 * @param  bool   $check            Check delete directory.
	 * @param  string $directory         Source directory.
	 * @param  string $working_directory The directory we working on it.
	 */
	protected function delete_directory( $check, $directory, $working_directory = null ) {
		if ( ! $check ) {
			return;
		}

		$filesystem = Utils::wp_filesystem();

		if ( $filesystem->is_dir( $directory ) ) {
			$filesystem->rmdir( $directory, true );
		}

		if ( $working_directory && $filesystem->is_dir( $working_directory ) ) {
			$filesystem->rmdir( $working_directory, true );
		}
	}
}
