<?php
/**
 * Icon manager upload form partial template.
 *
 * @package WP_Simple_Iconfonts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="welcome-panel">
	<a class="welcome-panel-close" href="#toggle-upload-form"  aria-label="<?php esc_html_e( 'Dismiss the upload form', 'wp_simple_iconfonts' ); ?>"><?php esc_html_e( 'Dismiss', 'wp_simple_iconfonts' ); ?></a>

	<div class="welcome-panel-content">

		<h2><?php echo esc_html__( 'Select Your Files', 'wp_simple_iconfonts' ); ?></h2>
		<p><?php echo esc_html__( 'Let’s get started importing your custom icon pack', 'wp_simple_iconfonts' ); ?></p>
		<p><?php echo wp_kses_post( __( 'To get started, simply upload a zip file downloaded from <a href="http://fontello.com" target="_blank">Fontello</a> or <a href="https://icomoon.io/app" target="_blank">IcoMoon App</a>.', 'wp_simple_iconfonts' ) ); ?></p>

		<form method="post" enctype="multipart/form-data" action="<?php echo esc_url( admin_url( 'tools.php?page=wp-simple-iconfonts' ) ); ?>" class="wp-upload-form">
			<?php wp_nonce_field( 'wp_simple_iconfonts' ) ?>

			<p>
				<input type="file" id="upload_iconpack" name="upload_iconpack">
				<input type="submit" name="submit_iconpack" class="button" value="<?php esc_html_e( 'Install Now', 'wp_simple_iconfonts' ); ?>">
			</p>
		</form>

	</div><!-- /.welcome-panel-content -->
</div><!-- /.welcome-panel -->
