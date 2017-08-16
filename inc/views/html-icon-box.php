<?php
/**
 * Icon manager icon-box partial template.
 *
 * @package WP_Simple_Iconfonts
 */

use WP_Simple_Iconfonts\Imported_Iconpack;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$is_deactive = false;
$all_icons = $this->iconfonts->get_imported_icons();

if ( $icon instanceof Imported_Iconpack ) {
	$is_deactive = isset( $all_icons[ $icon->id ] ) && ! $all_icons[ $icon->id ];
}

?><div id="<?php echo esc_attr( sprintf( 'ac-icon-%s', $icon->id ) ); ?>" class="simple-iconfonts__box <?php echo ( $is_deactive ? 'deactive' : '' ); ?>">
	<div class="postbox">

		<?php if ( $icon instanceof Imported_Iconpack ) : ?>
			<button type="button" data-toggle="simple-iconfonts-dropdown" class="handlediv" title="<?php echo esc_html__( 'Actions', 'wp_simple_iconfonts' ); ?>">
				<span class="screen-reader-text"><?php echo esc_html__( 'Actions', 'wp_simple_iconfonts' ); ?></span>
				<span class="toggle-indicator" aria-hidden="true"></span>
			</button>

			<ul class="split-button-body">
				<?php if ( $is_deactive ) : ?>
					<li><a href="<?php echo esc_url( admin_url( 'tools.php?page=wp-simple-iconfonts&active=' . $icon->id ) ); ?>" class="button-link split-button-option"><?php echo esc_html__( 'Active', 'wp_simple_iconfonts' ); ?></a></li>
				<?php else : ?>
					<li><a href="<?php echo esc_url( admin_url( 'tools.php?page=wp-simple-iconfonts&deactive=' . $icon->id ) ); ?>" class="button-link split-button-option"><?php echo esc_html__( 'Deactive', 'wp_simple_iconfonts' ); ?></a></li>
				<?php endif ?>

				<li><a href="<?php echo esc_url( admin_url( 'tools.php?page=wp-simple-iconfonts&delete=' . $icon->id . '&_wpnonce=' . $nonce ) ); ?>" class="button-link delete-button split-button-option"><?php echo esc_html__( 'Delete', 'wp_simple_iconfonts' ); ?></a></li>
			</ul>
		<?php endif; ?>

		<!-- <span class="postbox-search-icons">
			<label class="screen-reader-text" for="wp-filter-search-input"><?php echo esc_html__( 'Search icons...', 'wp_simple_iconfonts' ); ?></label>
			<input placeholder="<?php echo esc_attr( esc_html__( 'Search icons...', 'wp_simple_iconfonts' ) ); ?>" type="search" id="wp-filter-search-input" class="wp-filter-search fuzzy-search">
		</span> -->

		<h2 class="hndle">
			<?php if ( ! $icon instanceof Imported_Iconpack ) : ?>
				<span class="dashicons dashicons-lock simple-iconfonts-lock"></span>
			<?php endif; ?>

			<span class="icon_pack_id screen-reader-text"><?php echo esc_html( $icon->id ); ?></span>
			<span class="icon_pack_name"><?php echo esc_html( $icon->name ? $icon->name : $icon->id ); ?></span>
			<small class="count">(<?php echo esc_html( count( $icon->icons() ) ); ?>)</small>
		</h2>

		<div class="inside">
			<div class="inside-icons">
				<!-- <span class="spinner" style="visibility: visible;"></span> -->

				<ul class="simple-iconfonts__icons">
					<?php foreach ( $icon->icons() as $i ) : ?>
						<li>
							<i class="<?php echo esc_attr( $icon->id ); ?> <?php echo esc_attr( $i['id'] ); ?>" aria-hidden="true"></i>
							<div class="zoom-icon">
								<i class="<?php echo esc_attr( $icon->id ); ?> <?php echo esc_attr( $i['id'] ); ?>" aria-hidden="true"></i>
								<span class="icon-label name"><?php echo esc_html( $i['name'] ); ?></span>
								<span class="screen-reader-text classes"><?php echo esc_attr( $i['id'] ); ?></span>
							</div>
						</li>
					<?php endforeach ?>
				</ul>

			</div>
		</div>

	</div><!-- /.postbox -->
</div><!-- /.simple-iconfonts__box -->
