<?php
namespace WP_Simple_Iconfonts\Support;

class Nav_Menu_Icon {
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
	public static function instance() {
		if ( is_null( static::$instance ) ) {
			static::$instance = new static;
		}

		return static::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		add_filter( 'wp_edit_nav_menu_walker', array( $this, 'nav_menu_edit_walker' ), 101 );
		add_filter( 'manage_nav-menus_columns', array( $this, 'add_iconfonts_columns' ), 99 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		add_action( 'wp_nav_menu_item_custom_fields', array( $this, 'print_fields' ), 10, 4 );
		add_action( 'wp_update_nav_menu_item', array( $this, 'doing_save' ), 10, 3 );

		add_filter( 'wp_nav_menu_args', array( $this, '_add_menu_item_title_filter' ) );
		add_filter( 'wp_nav_menu', array( $this, '_remove_menu_item_title_filter' ) );
	}

	public function _add_menu_item_title_filter( $args ) {
		add_filter( 'the_title', array( $this, '_add_icon' ), 999, 2 );
		return $args;
	}

	public function _remove_menu_item_title_filter( $nav_menu ) {
		remove_filter( 'the_title', array( $this, '_add_icon' ), 999, 2 );

		return $nav_menu;
	}

	public function _add_icon( $title, $id ) {
		$icon = get_post_meta( $id, '_simple_iconfonts_menu_icon', true );
		$meta = is_array( $icon ) ? $icon : array();

		$icon = $this->get_icon( $icon );
		if ( empty( $icon ) ) {
			return $title;
		}

		return apply_filters( 'wp_simple_iconfonts_menu_title', "{$icon}{$title}", $icon, $title, $id );
	}

	/**
	 * Get icon
	 *
	 * @param  array $meta Menu item meta value.
	 * @return string
	 */
	public function get_icon( $meta ) {
		// Icon or icon type is not set.
		if ( empty( $meta['type'] ) || empty( $meta['icon'] ) ) {
			return '';
		}

		if ( in_array( $meta['type'], array( 'image', 'svg' ) ) && is_numeric( $meta['icon'] ) ) {
			return sprintf(
				'<img src="%s" aria-hidden="true">',
				esc_url( wp_get_attachment_url( $meta['icon'] ) )
			);
		}

		return sprintf( '<i class="%s" aria-hidden="true"></i>', $meta['type'] . ' ' . $meta['icon'] );
	}

	/**
	 * Filter the walker being used for the menu edit screen.
	 *
	 * @return string
	 */
	public function nav_menu_edit_walker() {
		return 'WP_Simple_Iconfonts\\Support\\Nav_Menu_Edit_Walker';
	}

	/**
	 * Add our field to the screen options toggle.
	 *
	 * @see http://codex.wordpress.org/Plugin_API/Filter_Reference/manage_posts_columns
	 *
	 * @param  array $columns Menu item columns.
	 * @return array
	 */
	public function add_iconfonts_columns( $columns ) {
		$columns['simple-iconfonts'] = esc_html__( 'Iconfonts', 'wp_simple_iconfonts' );

		return $columns;
	}

	/**
	 * //
	 *
	 * @return void
	 */
	public function admin_enqueue_scripts() {
		$screen = get_current_screen();

		if ( 'nav-menus' === $screen->id ) {
			wp_enqueue_media();
			wp_enqueue_style( 'simple-iconfonts-picker' );
			wp_enqueue_script( 'simple-iconfonts-picker' );

			foreach ( wp_simple_iconfonts()->all() as $iconpack ) {
				$iconpack->enqueue_styles();
			}
		}
	}

	/**
	 * Save menu item's icons metadata
	 *
	 * @see https://developer.wordpress.org/reference/functions/wp_update_nav_menu_item/
	 *
	 * @param int   $menu_id         Nav menu ID.
	 * @param int   $menu_item_db_id Menu item ID.
	 * @param array $menu_item_args  Menu item data.
	 */
	public function doing_save( $menu_id, $menu_item_db_id, $menu_item_args ) {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		$screen = get_current_screen();
		if ( is_null( $screen ) || 'nav-menus' !== $screen->id ) {
			return;
		}

		check_admin_referer( 'update-nav_menu', 'update-nav-menu-nonce' );

		// Sanitize input value.
		if ( ! empty( $_POST['_simple_iconfonts'][ $menu_item_db_id ] ) ) {
			$value = array_map(
				'sanitize_text_field',
				wp_unslash( (array) $_POST['_simple_iconfonts'][ $menu_item_db_id ] )
			);
		} else {
			$value = array();
		}

		if ( ! empty( $value ) ) {
			update_post_meta( $menu_item_db_id, '_simple_iconfonts_menu_icon', $value );
		} else {
			delete_post_meta( $menu_item_db_id, '_simple_iconfonts_menu_icon' );
		}
	}

	/**
	 * Print fields
	 *
	 * @param int    $id    Nav menu ID.
	 * @param object $item  Menu item data object.
	 * @param int    $depth Nav menu depth.
	 * @param array  $args  Menu item args.
	 *
	 * @return void
	 */
	public function print_fields( $id, $item, $depth, $args ) {
		$db_icon = get_post_meta( $item->ID, '_simple_iconfonts_menu_icon', true );
		$db_icon = is_array( $db_icon ) ? $db_icon : array();

		?><div class="field-simple-iconfonts description-wide">
			<?php wp_simple_iconfonts_field( array(
				'id'   => "_simple_iconfonts_{$item->ID}",
				'name' => "_simple_iconfonts[{$item->ID}]",
				'value' => $db_icon,
			) ); ?>
		</div><?php
	}
}
