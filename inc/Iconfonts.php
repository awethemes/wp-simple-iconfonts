<?php
namespace WP_Simple_Iconfonts;

final class Iconfonts {
	/* Constants */
	const VERSION = '0.1.0-dev';

	/**
	 * The icon fonts installer.
	 *
	 * @var \WP_Simple_Iconfonts\Installer
	 */
	protected $installer;

	/**
	 * An array registerd icon packs.
	 *
	 * @var array
	 */
	protected $iconpacks = array();

	/**
	 * An array extractors supported.
	 *
	 * @var array
	 */
	protected $extractors = array();

	/**
	 * An array of upload path, temp path, etc...
	 *
	 * @var array
	 */
	protected $paths = array();

	/**
	 * Singleton class instance implementation.
	 *
	 * @var static
	 */
	protected static $instance;

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

		$this->setup();
		$this->installer = new Installer( $this );

		$this->init();
	}

	/**
	 * Init the hooks.
	 */
	public function init() {
		add_action( 'init', array( $this, 'register_icons' ), 5 );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_styles' ), 5 );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_styles' ), 5 );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ) );

		add_action( 'admin_menu', array( $this, '_add_iconfonts_menu' ) );
		add_filter( 'upload_mimes', array( $this, '_svg_mime_support' ) );

		add_filter( 'media_view_strings', array( $this, '_media_view_strings' ) );
		add_action( 'print_media_templates', array( $this, '_media_templates' ) );
	}

	/**
	 * Get iconpack by ID.
	 *
	 * @param  string $id Iconpack ID.
	 * @return mixed
	 */
	public function get( $id ) {
		if ( isset( $this->iconpacks[ $id ] ) ) {
			return $this->iconpacks[ $id ];
		}
	}

	/**
	 * Determine if an iconpack is registerd.
	 *
	 * @param  string $id Iconpack ID.
	 * @return boolean
	 */
	public function has( $id ) {
		return ! is_null( $this->get( $id ) );
	}

	/**
	 * Get all iconpacks.
	 *
	 * @return array
	 */
	public function all() {
		return $this->iconpacks;
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

		$this->iconpacks[ $icon->id ] = $icon;
	}

	/**
	 * Unregister a icon pack.
	 *
	 * @param  string $id Icon pack ID.
	 * @return void
	 */
	public function unregister( $id ) {
		unset( $this->iconpacks[ $id ] );
	}

	/**
	 * Get icon fonts installer.
	 *
	 * @return \WP_Simple_Iconfonts\Installer
	 */
	public function get_installer() {
		return $this->installer;
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
		static $imported;

		if ( is_null( $imported ) ) {
			$imported = get_option( '_wp_simple_iconfonts', array() );
		}

		return is_array( $imported ) ? $imported : array();
	}

	/**
	 * Get all icons for JS.
	 *
	 * @return array
	 */
	public function get_for_js() {
		$types = array();
		$names = array();

		$imported = $this->get_imported_icons();

		foreach ( $this->all() as $icon ) {
			if ( $icon instanceof Upload_Iconpack && isset( $imported[ $icon->id ] ) && ! $imported[ $icon->id ] ) {
				continue;
			}

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
	 * Check if icon pack is valid.
	 *
	 * @param  Iconpack $icon Icon pack.
	 * @return bool
	 */
	protected function is_valid_iconpack( Iconpack $icon ) {
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
	 * Setup upload, tmp directory and the extractors.
	 *
	 * @return void
	 */
	protected function setup() {
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
			'WP_Simple_Iconfonts\\Extractor\\Foundation_Extractor',
			'WP_Simple_Iconfonts\\Extractor\\Ionicons_Extractor',
			'WP_Simple_Iconfonts\\Extractor\\Elusive_Extractor',
			'WP_Simple_Iconfonts\\Extractor\\Paymentfont_Extractor',
			'WP_Simple_Iconfonts\\Extractor\\Pixeden_Extractor',
			'WP_Simple_Iconfonts\\Extractor\\Themify_Extractor',
			'WP_Simple_Iconfonts\\Extractor\\Typicons_Extractor',
			'WP_Simple_Iconfonts\\Extractor\\Mapicons_Extractor',
		));
	}

	/**
	 * Register icons pack.
	 *
	 * @access private
	 */
	public function register_icons() {
		$this->register( new Icons\Dashicons );

		foreach ( $this->get_imported_icons() as $id => $status ) {
			$this->register( new Upload_Iconpack( $id ) );
		}

		do_action( 'wp_simple_iconfonts', $this );
	}

	/**
	 * Register icons pack.
	 *
	 * @access private
	 */
	public function register_styles() {
		foreach ( $this->all() as $iconpack ) {
			$iconpack->register_styles();
		}
	}

	/**
	 * Register admin scripts.
	 *
	 * @access private
	 */
	public function register_admin_scripts() {
		wp_register_script( 'icon-picker', $this->get_plugin_url( 'js/icon-picker.js' ), array( 'media-views' ), '0.5.0', true );

		wp_register_style( 'simple-iconfonts-picker', $this->get_plugin_url( 'css/simple-iconfonts-picker.css' ), array(), static::VERSION );
		wp_register_script( 'simple-iconfonts-picker', $this->get_plugin_url( 'js/simple-iconfonts-picker.js' ), array( 'icon-picker' ), static::VERSION );

		wp_localize_script( 'simple-iconfonts-picker', '_simpleIconFontsPicker', array(
			'types' => $this->get_for_iconpicker_js(),
		) );
	}

	/**
	 * Enqueue front-end icons style.
	 *
	 * @access private
	 */
	public function enqueue_styles() {
		$imported = $this->get_imported_icons();

		foreach ( $this->all() as $iconpack ) {
			if ( $iconpack instanceof Upload_Iconpack && isset( $imported[ $iconpack->id ] ) && ! $imported[ $iconpack->id ] ) {
				continue;
			}

			$iconpack->enqueue_styles();
		}
	}

	/**
	 * Media templates.
	 *
	 * @access private
	 *
	 * @return void
	 */
	public function _media_templates() {
		include trailingslashit( __DIR__ ) . 'views/media_templates.php';
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
			array( new Admin_Page( $this ), 'output' )
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
}
