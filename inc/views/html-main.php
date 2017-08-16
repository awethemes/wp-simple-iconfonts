<?php
/**
 * Icon manager main template.
 *
 * @package WP_Simple_Iconfonts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?><div id="wp-simple-iconfonts" class="wrap theme-install-php wp-simple-iconfonts-wrap">
	<h2>
		<?php esc_html_e( 'Font Icon Manager', 'wp_simple_iconfonts' ); ?>
		<a href="#toggle-upload-form" data-toggle="collapse" class="add-new-h2"><?php esc_html_e( 'Upload Icon Pack', 'wp_simple_iconfonts' ); ?></a>
	</h2>

	<div id="toggle-upload-form" class="">
		<?php $this->output_upload_form(); ?>
	</div><!-- /.welcome-panel -->

	<?php $this->show_messages(); ?>

	<div class="metabox-holder simple-iconfonts">
		<?php foreach ( $this->iconfonts->all( true ) as $icon ) :
			$this->add_icons_box( $icon );
		endforeach; ?>
	</div>
</div>
