<?php
namespace WP_Simple_Iconfonts\Extractor;

/**
 * Ionicons Extractor
 *
 * @link http://ionicons.com
 */
class Ionicons_Extractor extends Extractor {
	/**
	 * The relative path of metadata file.
	 *
	 * @var string
	 */
	protected $metadata = 'builder/manifest.json';

	/**
	 * The relative path of stylesheet file.
	 *
	 * @var string
	 */
	protected $stylesheet = 'css/ionicons.css';

	/**
	 * List of files, folders match folder structure.
	 *
	 * @var array
	 */
	protected $directory_structure = array( 'fonts/', 'css/ionicons.css', 'builder/manifest.json' );

	/**
	 * Doing extract icon pack data.
	 *
	 * @param  Result $result Extractor icon pack instance.
	 * @return void
	 */
	protected function doing_extract( Result $result ) {
		$result->id = 'ionicons';
		$result->name = 'Ionicons';

		$json = $result->get_metadata_contents();
		if ( ! is_array( $json ) || empty( $json['build_hash'] ) || empty( $json['icons'] ) ) {
			return;
		}

		foreach ( (array) $json['icons'] as $raw_icon ) {
			if ( ! isset( $raw_icon['name'] ) ) {
				continue;
			}

			$icon_class = $json['prefix'] . $raw_icon['name'];
			$result->add_icon( $icon_class, $raw_icon['name'] );
		}

		$result->version = $json['version'];
	}
}
