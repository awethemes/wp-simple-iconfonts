<?php
namespace WP_Simple_Iconfonts;

class Admin_Page {
	/**
	 * Iconfonts object instance.
	 *
	 * @var Iconfonts
	 */
	protected $iconfonts;

	/**
	 * The error messages.
	 *
	 * @var array
	 */
	protected $errors = array();

	/**
	 * The uploader messages.
	 *
	 * @var array
	 */
	protected $messages = array();

	/**
	 * Constructor.
	 *
	 * @param Iconfonts $iconfonts Iconfonts object instance.
	 */
	public function __construct( Iconfonts $iconfonts ) {
		$this->iconfonts = $iconfonts;
	}

	/**
	 * Output icon manager.
	 */
	public function output() {
		// Handler upload a icon pack.
		if ( ! empty( $_POST ) && ! empty( $_FILES['upload_iconpack'] ) ) {
			$this->handler_upload_icon();
		}

		if ( isset( $_REQUEST['delete'] ) && $_REQUEST['delete'] ) {
			$this->handler_delete_icon();
		}

		if ( isset( $_REQUEST['deactive'] ) && $_REQUEST['deactive'] ) {
			$this->handler_deactive_icon();
		}

		if ( isset( $_REQUEST['active'] ) && $_REQUEST['active'] ) {
			$this->handler_active_icon();
		}

		// Enqueue admin scripts.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Load the admin header.
		require_once ABSPATH . 'wp-admin/admin-header.php';

		// Load main template.
		include dirname( __FILE__ ) . '/views/html-main.php';
	}

	/**
	 * Handler install new icon pack.
	 */
	protected function handler_upload_icon() {
		check_admin_referer( 'wp_simple_iconfonts' );

		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		// Handler upload archive file.
		add_filter( 'upload_dir', array( $this, 'set_tmp_upload_dir' ) );
		$upload_result = wp_handle_upload( $_FILES['upload_iconpack'], array( 'test_form' => false ) );

		if ( isset( $upload_result['error'] ) ) {
			return $this->add_error( $upload_result['error'] );
		}

		/**
		 * Hook wp_simple_iconfonts_before_install_iconpack
		 *
		 * @param array           $upload_result  Upload result array.
		 * @param Admin_Page $this                Instance of this class.
		 */
		do_action( 'wp_simple_iconfonts_before_install_iconpack', $upload_result, $this );

		$installer = $this->iconfonts->get_installer();
		$installed_icon = $installer->zip_install( $upload_result['file'], true );
		@unlink( $upload_result['file'] );

		/**
		 * Hook wp_simple_iconfonts_after_install_iconpack
		 *
		 * @param array              $upload_result  Upload result array.
		 * @param Icon_Pack|WP_Error $installed_icon Installed icon result.
		 * @param Admin_Page         $this           Instance of this class.
		 */
		do_action( 'wp_simple_iconfonts_after_install_iconpack', $upload_result, $installed_icon, $this );

		if ( is_wp_error( $installed_icon ) ) {
			return $this->add_error( $installed_icon->get_error_message() );
		} else {
			$this->add_message( esc_html__( 'Install new icon successfully', 'wp_simple_iconfonts' ) );
		}

		/**
		 * Hook wp_simple_iconfonts_installed_iconpack
		 *
		 * @param Icon_Pack  $installed_icon Installed icon model class.
		 * @param Admin_Page $this           Instance of this class.
		 */
		do_action( 'wp_simple_iconfonts_installed_iconpack', $installed_icon, $this );
	}

	/**
	 * Handler delete a icon pack via HTTP request.
	 *
	 * @access private
	 */
	public function handler_delete_icon() {
		check_admin_referer( 'wp_simple_iconfonts_delete_iconpack' );

		if ( empty( $_REQUEST['delete'] ) ) {
			return;
		}

		$iconpack = sanitize_text_field( $_REQUEST['delete'] );
		$iconpack = $this->iconfonts->get( $iconpack );

		if ( is_null( $iconpack ) ) {
			return;
		}

		$this->iconfonts->unregister( $iconpack->id );

		$sc = $this->iconfonts->get_imported_icons();
		unset( $sc[ $iconpack->id ] );
		update_option( '_wp_simple_iconfonts', $sc );

		$fs = Utils::wp_filesystem();
		if ( $fs->is_dir( $this->iconfonts->get_path( 'icons_dir' ) . $iconpack->id ) ) {
			$fs->rmdir( $this->iconfonts->get_path( 'icons_dir' ) . $iconpack->id, true );
		}

		wp_redirect( admin_url( 'tools.php?page=wp-simple-iconfonts' ) );
	}

	/**
	 * Handler deactive a icon pack.
	 *
	 * @access private
	 */
	public function handler_deactive_icon() {
		if ( empty( $_REQUEST['deactive'] ) ) {
			return;
		}

		$iconpack = $this->iconfonts->get(
			sanitize_text_field( $_REQUEST['deactive'] )
		);

		if ( is_null( $iconpack ) ) {
			return;
		}

		$sc = $this->iconfonts->get_imported_icons();
		$sc[ $iconpack->id ] = false;
		update_option( '_wp_simple_iconfonts', $sc );

		wp_redirect( admin_url( 'tools.php?page=wp-simple-iconfonts' ) );
	}

	/**
	 * Handler active a icon pack.
	 *
	 * @access private
	 */
	public function handler_active_icon() {
		if ( empty( $_REQUEST['active'] ) ) {
			return;
		}

		$iconpack = $this->iconfonts->get(
			sanitize_text_field( $_REQUEST['active'] )
		);

		if ( is_null( $iconpack ) ) {
			return;
		}

		$sc = $this->iconfonts->get_imported_icons();
		$sc[ $iconpack->id ] = true;
		update_option( '_wp_simple_iconfonts', $sc );

		wp_redirect( admin_url( 'tools.php?page=wp-simple-iconfonts' ) );
	}

	/**
	 * Set custom upload directory.
	 *
	 * @access private
	 *
	 * @param  array $upload Upload directory information.
	 * @return array
	 */
	public function set_tmp_upload_dir( $upload ) {
		$upload['url']  = ''; // We don't need this.
		$upload['path'] = untrailingslashit( $this->iconfonts->get_path( 'tmp_dir' ) );

		return $upload;
	}

	/**
	 * Output partial upload form.
	 */
	protected function output_upload_form() {
		$upload_dir = wp_upload_dir();

		if ( ! empty( $upload_dir['error'] ) ) {
			$error  = sprintf( '<p>%s</p>', esc_html__( 'Before you can upload your icon, you will need to fix the following error:', 'wp_simple_iconfonts' ) );
			$error .= sprintf( '<p><strong>%s</strong></p>', $upload_dir['error'] );

			printf( '<div class="error inline">%s</div>', $error ); // WPCS: XSS OK.
			return;
		}

		include dirname( __FILE__ ) . '/views/html-upload-form.php';
	}

	/**
	 * Add an error.
	 *
	 * @param string $text Message text.
	 */
	protected function add_error( $text ) {
		$this->errors[] = $text;
	}

	/**
	 * Add a message.
	 *
	 * @param string $text Error message.
	 */
	protected function add_message( $text ) {
		$this->messages[] = $text;
	}

	/**
	 * Output messages and errors.
	 */
	protected function show_messages() {
		if ( count( $this->errors ) > 0 ) {
			foreach ( $this->errors as $error ) {
				echo '<div class="error notice notice-error is-dismissible"><p>' . esc_html( $error ) . '</p></div>'; // WPCS: XSS OK.
			}
		} elseif ( count( $this->messages ) > 0 ) {
			foreach ( $this->messages as $message ) {
				echo '<div class="updated notice notice-success is-dismissible"><p>' . esc_html( $message ) . '</p></div>';  // WPCS: XSS OK.
			}
		}
	}

	/**
	 * Add a icon box.
	 *
	 * @param AC_Icon_Model $icon The icon model.
	 */
	protected function add_icons_box( $icon ) {
		$nonce = wp_create_nonce( 'wp_simple_iconfonts_delete_iconpack' );
		include dirname( __FILE__ ) . '/views/html-icon-box.php';
	}

	/**
	 * Enqueue scripts.
	 *
	 * @access private
	 */
	public function enqueue_scripts() {
		wp_enqueue_media();

		wp_enqueue_style( 'wp-simple-iconfonts', $this->iconfonts->get_plugin_url( 'css/simple-iconfonts.css' ), array(), Iconfonts::VERSION );
		wp_enqueue_script( 'wp-simple-iconfonts', $this->iconfonts->get_plugin_url( 'js/simple-iconfonts.js' ), array( 'jquery' ), Iconfonts::VERSION );

		wp_localize_script( 'wp-simple-iconfonts', '_simpleIconfonts', array(
			'strings' => array(
				'warning_delete' => esc_html__( 'This icon pack will be lost in your system. Are you sure want to do this?', 'wp_simple_iconfonts' ),
			),
		));

		foreach ( $this->iconfonts->all() as $iconpack ) {
			$iconpack->enqueue_styles();
		}
	}
}
