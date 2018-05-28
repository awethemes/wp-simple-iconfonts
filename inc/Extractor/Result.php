<?php
namespace WP_Simple_Iconfonts\Extractor;

class Result {
	/**
	 * The Extractor instance.
	 *
	 * @var Extractor
	 */
	protected $extractor;

	/**
	 * Iconpack unique ID.
	 *
	 * @var string
	 */
	public $id = '';

	/**
	 * Iconpack display name.
	 *
	 * @var string
	 */
	public $name = '';

	/**
	 * Iconpack version.
	 *
	 * @var string
	 */
	public $version = '';

	/**
	 * Iconpack icons.
	 *
	 * @var array
	 */
	public $icons = array();

	/**
	 * Iconpack groups.
	 *
	 * @var array
	 */
	public $groups = array();

	/**
	 * An array store icon-font paths.
	 *
	 * @var array
	 */
	public $font_paths = array();

	/**
	 * The metadata file path.
	 *
	 * @var string
	 */
	public $metadata_path = '';

	/**
	 * The stylesheet file path.
	 *
	 * @var string
	 */
	public $stylesheet_path = '';

	/**
	 * Supplied $args override class property defaults.
	 *
	 * @param Extractor $extractor The extractor instance.
	 * @param array     $args      Optional. Arguments to override class property defaults.
	 */
	public function __construct( Extractor $extractor, array $args = array() ) {
		$this->extractor  = $extractor;

		$keys = array_keys( get_object_vars( $this ) );
		foreach ( $keys as $key ) {
			if ( isset( $args[ $key ] ) ) {
				$this->{$key} = $args[ $key ];
			}
		}
	}

	/**
	 * Add a icon to result.
	 *
	 * @param string $id    The icon ID.
	 * @param string $name  The icon name.
	 * @param string $group The icon group.
	 */
	public function add_icon( $id, $name, $group = '' ) {
		if ( empty( $id ) ) {
			return;
		}

		// Format icon name.
		$name = ucfirst( str_replace( array( '-', ' o' ), array( ' ', '' ), $name ) );

		$this->icons[] = array(
			'id'    => $id,
			'name'  => $name,
			'group' => $group,
		);
	}

	/**
	 * Copy result to a destination path.
	 *
	 * @param  string $base Base path.
	 * @return void
	 */
	public function destination( $base ) {
		$destination = trailingslashit( $base ) . $this->id . '/';

		// Ensure destination exits.
		if ( ! is_dir( $destination ) ) {
			@mkdir( $destination, 0755, true );
		}

		if ( ! is_dir( $destination . 'fonts/' ) ) {
			@mkdir( $destination . 'fonts/', 0755, true );
		}

		// Move fonts to new folder.
		foreach ( $this->font_paths as $font_path ) {
			$this->perform_copy_font( $font_path, $destination );
		}

		// Write style.css, icons.json.
		file_put_contents( $destination . 'style.css', $this->get_rewrite_stylesheet() );
		file_put_contents( $destination . 'metadata.json', json_encode( $this->to_array() ) . "\n" );
		file_put_contents( $destination . 'index.php', "<?php\n// Silence is golden.\n" );
	}

	/**
	 * Perform copy font from source to the destination.
	 *
	 * @param  string|array $source      The font source path.
	 * @param  string       $destination The base destination.
	 * @return void
	 */
	protected function perform_copy_font( $source, $destination ) {
		if ( is_array( $source ) ) {
			foreach ( $source as $font_path ) {
				$this->perform_copy_font( $font_path, $destination );
			}

			return;
		}

		if ( $this->extractor instanceof Editable ) {
			copy( $source, $destination . 'fonts/' . $this->id . '.' . pathinfo( $source, PATHINFO_EXTENSION ) );
		} else {
			copy( $source, $destination . 'fonts/' . basename( $source ) );
		}
	}

	/**
	 * Return metadata data contents.
	 *
	 * If see a json file, decode json as array and return it.
	 *
	 * @return string
	 */
	public function get_metadata_contents() {
		$contents = file_get_contents( $this->metadata_path );

		if ( 'json' === pathinfo( $this->metadata_path, PATHINFO_EXTENSION ) ) {
			$contents = json_decode( $contents, true );
		}

		return $contents;
	}

	/**
	 * Return stylesheet data contents.
	 *
	 * @return string
	 */
	public function get_stylesheet_contents() {
		return file_get_contents( $this->stylesheet_path );
	}

	/**
	 * Utils: Rewrite and minify CSS.
	 *
	 * @return string
	 */
	protected function get_rewrite_stylesheet() {
		$stylesheet = $this->get_stylesheet_contents();

		// Change fonts paths.
		$stylesheet = preg_replace( '/url\(([\'"])?/', 'url($1::', $stylesheet ); // ...
		$stylesheet = preg_replace( '/\.\.?\/|fonts?\//', '::', $stylesheet ); // Replace ..|fonts with temp string ::.
		$stylesheet = preg_replace( '/[\:\:]{2,}+/', 'fonts/', $stylesheet ); // Replace `::` temp string with 'fonts/'.

		// Call rewrite_stylesheet from extractor.
		if ( method_exists( $this->extractor, 'rewrite_stylesheet' ) ) {
			$stylesheet = $this->extractor->rewrite_stylesheet( $stylesheet );
		}

		if ( $this->extractor instanceof Editable ) {
			// $stylesheet = Utils::minify_css( $stylesheet );

			// Change fonts name.
			foreach ( $this->font_paths as $path ) {
				$newname = $this->id . '.' . pathinfo( $path, PATHINFO_EXTENSION );
				$stylesheet = str_replace( basename( $path ), $newname, $stylesheet );
			}

			// Never use prefix .icon for icon name.
			$stylesheet = str_replace( array( '.icon {', 'i {' ), '[class^="' . $this->id . '-"], [class*=" ' . $this->id . '-"] {', $stylesheet );
			$stylesheet = str_replace( '" icon-"', " \"{$this->id}-\"", $stylesheet );
			$stylesheet = str_replace( '"icon-"', "\"{$this->id}-\"", $stylesheet );
			$stylesheet = str_replace( '.icon-', ".{$this->id}-", $stylesheet );
		}

		// Return parser CSS with blank line at end.
		return trim( $stylesheet ) . "\n";
	}

	/**
	 * Get an array of this result.
	 *
	 * @return array
	 */
	public function to_array() {
		return array(
			'id'      => $this->id,
			'name'    => $this->name,
			'version' => $this->version,
			'icons'   => $this->icons,
			'groups'  => $this->groups,
		);
	}
}
