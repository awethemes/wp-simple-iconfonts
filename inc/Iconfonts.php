<?php
namespace WP_Simple_Iconfonts;

final class Iconfonts {
	/* Constants */
	const VERSION = '0.5.1';

	/**
	 * An array registerd icon packs.
	 *
	 * @var array
	 */
	private $iconpacks = array();

	/**
	 * An array extractors supported.
	 *
	 * @var array
	 */
	private $extractors = array();

	/**
	 * An array of upload path, temp path, etc...
	 *
	 * @var array
	 */
	private $paths = array();

	/**
	 * An array icons was imported.
	 *
	 * @var array
	 */
	private $imported;

	/**
	 * Singleton class instance implementation.
	 *
	 * @var static
	 */
	private static $instance;

	/**
	 * Set the globally available instance of the container.
	 *
	 * @return static
	 */
	public static function get_instance() {
		if ( is_null( static::$instance ) ) {
			static::$instance = new static;
		}

		return static::$instance;
	}

	/**
	 * Iconfonst constructor.
	 */
	public function __construct() {
		static::$instance = $this;

		$upload_dir = wp_upload_dir();
		$this->paths['tmp_dir']   = trailingslashit( $upload_dir['basedir'] ) . 'tmp/simple-iconfonts/';
		$this->paths['icons_dir'] = trailingslashit( $upload_dir['basedir'] ) . 'simple-iconfonts/';
		$this->paths['icons_url'] = trailingslashit( $upload_dir['baseurl'] ) . 'simple-iconfonts/';

		// Priority is so important at here!
		$this->extractors = apply_filters( 'wp_simple_iconfonts_extractors', array(
			'WP_Simple_Iconfonts\\Extractor\\Simple_Iconfonts_Extractor',
			'WP_Simple_Iconfonts\\Extractor\\Fontello_Extractor',
			'WP_Simple_Iconfonts\\Extractor\\Icomoon_App_Extractor',
			'WP_Simple_Iconfonts\\Extractor\\Fontawesome_Extractor',
			'WP_Simple_Iconfonts\\Extractor\\Fontawesome5_Extractor',
			'WP_Simple_Iconfonts\\Extractor\\Foundation_Extractor',
			'WP_Simple_Iconfonts\\Extractor\\Ionicons_Extractor',
			'WP_Simple_Iconfonts\\Extractor\\Elusive_Extractor',
			'WP_Simple_Iconfonts\\Extractor\\Paymentfont_Extractor',
			'WP_Simple_Iconfonts\\Extractor\\Pixeden_Extractor',
			'WP_Simple_Iconfonts\\Extractor\\Themify_Extractor',
			'WP_Simple_Iconfonts\\Extractor\\Typicons_Extractor',
			'WP_Simple_Iconfonts\\Extractor\\Mapicons_Extractor',
		));

		$this->init();
	}

	/**
	 * Init the hooks.
	 *
	 * @access private
	 */
	public function init() {
		add_action( 'init', array( $this, 'register_icons' ), 5 );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_styles' ), 5 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 10 );

		add_filter( 'upload_mimes', array( $this, '_svg_mime_support' ) );
		add_action( 'admin_menu', array( $this, '_add_iconfonts_menu' ) );
		add_filter( 'media_view_strings', array( $this, '_media_view_strings' ) );
		add_action( 'print_media_templates', array( $this, '_media_templates' ) );
		add_action( 'admin_enqueue_scripts', array( $this, '_register_admin_scripts' ), 5 );

		add_filter( 'plugin_row_meta', array( $this, '_plugin_links' ), 10, 2 );
	}

	/**
	 * Register icons pack.
	 *
	 * @access private
	 */
	public function register_icons() {
		$this->register( new Icons\Dashicons );

		foreach ( $this->get_imported_icons() as $id => $status ) {
			if ( empty( $id ) ) {
				continue;
			}

			$this->register( new Imported_Iconpack( $id ) );
		}

		do_action( 'wp_simple_iconfonts', $this );
	}

	/**
	 * Get iconpack by ID.
	 *
	 * @param  string $id    Iconpack ID.
	 * @param  bool   $force Force get the icon, even in inactive.
	 * @return Iconpack|null|false
	 */
	public function get( $id, $force = false ) {
		$id = ( $id instanceof Iconpack ) ? $id->id : $id;

		if ( ! isset( $this->iconpacks[ $id ] ) ) {
			return null;
		}

		$iconpack = $this->iconpacks[ $id ];
		if ( $force ) {
			return $iconpack;
		}

		$imported = $this->get_imported_icons();
		if ( $iconpack instanceof Imported_Iconpack && isset( $imported[ $id ] ) && ! $imported[ $id ] ) {
			return false;
		}

		return $iconpack;
	}

	/**
	 * Determine if an iconpack is registerd.
	 *
	 * @param  string $id    Iconpack ID.
	 * @param  bool   $force Force check the icon, even in inactive.
	 * @return boolean
	 */
	public function has( $id, $force = false ) {
		$iconpack = $this->get( $id, $force );

		return $iconpack instanceof Iconpack;
	}

	/**
	 * Get all iconpacks.
	 *
	 * @param  bool $force Force get all icons, even in inactive.
	 * @return Iconpack[]
	 */
	public function all( $force = false ) {
		if ( $force ) {
			return $this->iconpacks;
		}

		// We support PHP 5.3, because $this invisible in Closures,
		// so let assign $this with $self variable.
		// See: https://stackoverflow.com/questions/5734011/php-5-4-closure-this-support .
		$self = $this;

		return array_filter( $this->iconpacks, function( $iconpack ) use ( $self, $force ) {
			return $self->get( $iconpack, $force );
		});
	}

	/**
	 * Register a icon pack.
	 *
	 * @param  Iconpack $icon The icon pack object.
	 * @return void
	 */
	public function register( Iconpack $icon ) {
		if ( ! $this->is_valid_iconpack( $icon ) ) {
			return;
		}

		if ( $this->has( $icon->id, true ) ) {
			return;
		}

		$this->iconpacks[ $icon->id ] = $icon;
	}

	/**
	 * Unregister a icon pack.
	 *
	 * @param  string $id Icon pack ID.
	 * @return void
	 */
	public function unregister( $id ) {
		$id = ( $id instanceof Iconpack ) ? $id->id : $id;

		unset( $this->iconpacks[ $id ] );
	}

	/**
	 * Check if icon pack is valid.
	 *
	 * @param  Iconpack $icon Icon pack.
	 * @return bool
	 */
	private function is_valid_iconpack( Iconpack $icon ) {
		if ( empty( $icon->id ) ) {
			trigger_error( 'WP Simple Iconfonts: "ID" cannot be empty.' );
			return false;
		}

		if ( isset( $this->iconpacks[ $icon->id ] ) ) {
			trigger_error( sprintf( 'WP Simple Iconfonts: Icon pack %s is already registered. Please use a different ID.', $icon->id ) );
			return false;
		}

		return true;
	}

	/**
	 * Get the plugin url.
	 *
	 * @param string|null $path Optional, extra path.
	 * @return string
	 */
	public function get_plugin_url( $path = null ) {
		return plugin_dir_url( WP_SIMPLE_ICONFONTS_PATH ) . $path;
	}

	/**
	 * Get the plugin path.
	 *
	 * @param string|null $path Optional, extra path.
	 * @return string
	 */
	public function get_plugin_path( $path = null ) {
		return plugin_dir_path( WP_SIMPLE_ICONFONTS_PATH ) . $path;
	}

	/**
	 * Get the plugin slug (basename).
	 *
	 * @return string
	 */
	public function get_plugin_basename() {
		return plugin_basename( $this->get_plugin_path() );
	}

	/**
	 * Get supported extractors.
	 *
	 * @return array
	 */
	public function get_extractors() {
		return $this->extractors;
	}

	/**
	 * Get the path.
	 *
	 * @param  string $path Path name.
	 * @return string|null
	 */
	public function get_path( $path ) {
		return isset( $this->paths[ $path ] ) ? $this->paths[ $path ] : null;
	}

	/**
	 * Returns imported icons.
	 *
	 * @return array
	 */
	public function get_imported_icons() {
		if ( is_null( $this->imported ) ) {
			$imported = get_option( '_wp_simple_iconfonts', array() );
			$this->imported = is_array( $imported ) ? $imported : array();
		}

		return $this->imported;
	}

	/**
	 * Get all icons for JS.
	 *
	 * @return array
	 */
	public function get_for_js() {
		$types = array();
		$names = array();

		foreach ( $this->all() as $icon ) {
			$types[ $icon->id ] = $icon->get_props();
			$names[ $icon->id ] = $icon->name;
		}

		array_multisort( $names, SORT_ASC, $types );

		return $types;
	}

	/**
	 * Get all icons for JS Iconpicker.
	 *
	 * We use wp.media.view.MediaFrame.IconPicker from
	 * kucrut/wp-icon-picker (GPLv2 license) for picker icon. So
	 * this return same format of kucrut/wp-icon-picker.
	 *
	 * @link https://github.com/kucrut/wp-icon-picker
	 *
	 * @return array
	 */
	public function get_for_iconpicker_js() {
		$types = $this->get_for_js();

		$types['image'] = array(
			'id'         => 'image',
			'name'       => 'Image',
			'controller' => 'SimpleImage',
			'templateId' => 'image',
			'data'       => array( 'mimeTypes' => Utils::get_image_mime_types() ),
		);

		$types['svg'] = array(
			'id'         => 'svg',
			'name'       => 'SVG',
			'controller' => 'SimpleImage',
			'templateId' => 'svg',
			'data'       => array( 'mimeTypes' => 'image/svg+xml' ),
		);

		return $types;
	}

	/**
	 * Register icons pack.
	 *
	 * @access private
	 */
	public function register_styles() {
		foreach ( $this->all( true ) as $iconpack ) {
			$iconpack->register_styles();
		}
	}

	/**
	 * Enqueue front-end icons style.
	 *
	 * @access private
	 */
	public function enqueue_styles() {
		foreach ( $this->all() as $iconpack ) {
			$iconpack->enqueue_styles();
		}
	}

	/**
	 * Register admin scripts.
	 *
	 * @access private
	 */
	public function _register_admin_scripts() {
		$this->register_styles();

		wp_register_style( 'wp-simple-iconfonts', $this->get_plugin_url( 'css/simple-iconfonts.css' ), array(), static::VERSION );
		wp_register_style( 'simple-iconfonts-picker', $this->get_plugin_url( 'css/simple-iconfonts-picker.css' ), array(), static::VERSION );

		wp_register_script( 'wp-simple-iconfonts', $this->get_plugin_url( 'js/simple-iconfonts.js' ), array( 'jquery' ), static::VERSION, true );
		wp_register_script( 'wp-simple-iconfonts', '_simpleIconfonts', array(
			'strings' => array(
				'warning_delete' => esc_html__( 'This icon pack will be lost in your system. Are you sure want to do this?', 'wp_simple_iconfonts' ),
			),
		));

		wp_register_script( 'icon-picker', $this->get_plugin_url( 'js/icon-picker.js' ), array( 'media-views' ), '0.5.0', true );
		wp_register_script( 'simple-iconfonts-picker', $this->get_plugin_url( 'js/simple-iconfonts-picker.js' ), array( 'icon-picker' ), static::VERSION, true );
		wp_localize_script( 'simple-iconfonts-picker', '_simpleIconFontsPicker', array(
			'types' => $this->get_for_iconpicker_js(),
		) );

		wp_register_script( 'simple-iconfonts-customize', $this->get_plugin_url( 'js/simple-iconfonts-customize.js' ), array( 'jquery', 'simple-iconfonts-picker' ), static::VERSION, true );
	}

	/**
	 * Media templates.
	 *
	 * @access private
	 *
	 * @return void
	 */
	public function _media_templates() {
		include trailingslashit( __DIR__ ) . 'views/media-templates.php';
	}

	/**
	 * Filter media view strings.
	 *
	 * TODO: ...
	 *
	 * @access private
	 *
	 * @param  array $strings Media view strings.
	 * @return array
	 */
	public function _media_view_strings( $strings ) {
		$strings['iconPicker'] = array(
			'frameTitle' => esc_html__( 'Icon Picker', 'wp_simple_iconfonts' ),
			'allFilter'  => esc_html__( 'All', 'wp_simple_iconfonts' ),
			'selectIcon' => esc_html__( 'Select Icon', 'wp_simple_iconfonts' ),
		);

		return $strings;
	}

	/**
	 * Add manager icon fonts menu.
	 *
	 * @access private
	 */
	public function _add_iconfonts_menu() {
		$hook_suffix = add_management_page(
			esc_html__( 'Icon Fonts', 'wp_simple_iconfonts' ),
			esc_html__( 'Icon Fonts', 'wp_simple_iconfonts' ),
			'manage_options',
			'wp-simple-iconfonts',
			array( new Iconfonts_Manager( $this ), 'output' )
		);

		add_action( 'load-' . $hook_suffix, function() {
			$_GET['noheader'] = true;
		});
	}

	/**
	 * Add SVG support.
	 *
	 * @access private
	 *
	 * @param array $mimes Array mimes type.
	 * @return array
	 */
	public function _svg_mime_support( array $mimes ) {
		if ( ! isset( $mimes['svg'] ) ) {
			$mimes['svg'] = 'image/svg+xml';
		}

		return $mimes;
	}

	/**
	 * Adds links to the docs and GitHub.
	 *
	 * @param  array  $plugin_meta The current array of links.
	 * @param  string $plugin_file The plugin file.
	 * @return array
	 */
	public function _plugin_links( $plugin_meta, $plugin_file ) {
		if ( $this->get_plugin_basename() . '/wp-simple-iconfonts.php' === $plugin_file ) {
			$plugin_meta[] = sprintf( '<a href="%s" target="_blank">%s</a>',
				esc_url( 'http://docs.awethemes.com/wp-simple-iconfonts' ),
				esc_html__( 'Documentation', 'wp_simple_iconfonts' )
			);

			$plugin_meta[] = sprintf( '<a href="%s" target="_blank">%s</a>',
				esc_url( 'https://github.com/awethemes/wp-simple-iconfonts' ),
				esc_html__( 'GitHub Repo', 'wp_simple_iconfonts' )
			);

			$plugin_meta[] = sprintf( '<a href="%s" target="_blank">%s</a>',
				esc_url( 'https://github.com/awethemes/wp-simple-iconfonts/issues' ),
				esc_html__( 'Issue Tracker', 'wp_simple_iconfonts' )
			);
		}

		return $plugin_meta;
	}
}
