<?php
namespace WP_Simple_Iconfonts;

/**
 * Groups and icons must return below structure:
 *
 * Groups (optional):
 *
 * array(
 *      array(
 *          'id'   => 'admin',
 *          'name' => 'Admin',
 *      ),
 *      ...
 * )
 *
 * Icons (required):
 *
 * array(
 *      array(
 *          'id'    => 'dashicons-admin-comments',
 *          'name'  => 'Comments',
 *          'group' => 'admin', // Optional, if isset.
 *      ),
 *      ...
 * )
 */
class Iconpack {
	/**
	 * Iconpack unique ID.
	 *
	 * @var string
	 */
	public $id;

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
	 * Stylesheet ID.
	 *
	 * @var string
	 */
	public $stylesheet_id = '';

	/**
	 * Stylesheet URI.
	 *
	 * @var string
	 */
	public $stylesheet_uri = '';

	/**
	 * Iconpack icons.
	 *
	 * @var string
	 */
	public $icons = array();

	/**
	 * Iconpack groups.
	 *
	 * @var string
	 */
	public $groups = array();

	/**
	 * Supplied $args override class property defaults.
	 *
	 * @param array $args Optional. Arguments to override class property defaults.
	 */
	public function __construct( array $args = array() ) {
		$keys = array_keys( get_object_vars( $this ) );

		foreach ( $keys as $key ) {
			if ( isset( $args[ $key ] ) ) {
				$this->{$key} = $args[ $key ];
			}
		}
	}

	/**
	 * Register the wp stylesheet.
	 *
	 * @return void
	 */
	public function register_styles() {
		if ( $this->stylesheet_uri ) {
			wp_register_style( $this->stylesheet_id, $this->stylesheet_uri, array(), $this->version );
		}
	}

	/**
	 * Enqueue the wp stylesheet.
	 *
	 * @return void
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->stylesheet_id );
	}

	/**
	 * Return an array icon groups.
	 *
	 * @return array
	 */
	public function groups() {
		return apply_filters( "wp_simple_iconfonts.{$this->id}_groups", $this->groups );
	}

	/**
	 * Return an array of icons.
	 *
	 * @return array
	 */
	public function icons() {
		return apply_filters( "wp_simple_iconfonts.{$this->id}_icons", $this->icons );
	}

	/**
	 * Get properties
	 *
	 * @since  0.1.0
	 * @return array
	 */
	final public function get_props() {
		$props = array(
			'id'         => $this->id,
			'name'       => $this->name,
			'controller' => 'SimpleIconpack', // Never change this.
			'templateId' => 'font',
			'data'       => array(
				'groups' => $this->groups(),
				'items'  => $this->icons(),
			),
		);

		/**
		 * Filter icon pack properties.
		 *
		 * @param array    $props    Icon pack properties.
		 * @param string   $id       Icon pack ID.
		 * @param Iconpack $iconpack Iconpack object.
		 */
		$props = apply_filters( 'wp_simple_iconfonts_props', $props, $this->id, $this );

		/**
		 * Filter icon pack properties.
		 *
		 * @param array    $props Icon pack properties.
		 * @param Iconpack $pack  Iconpack object.
		 */
		$props = apply_filters( "wp_simple_iconfonts_props_{$this->id}", $props, $this );

		return $props;
	}
}
