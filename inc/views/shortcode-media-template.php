<script type="text/template" id="wp_simple_iconfonts_shortcode-template">
	<h3><?php esc_html_e( 'Settings', 'wp_simple_iconfonts' ); ?></h3>

	<div class="wp-simple-iconfonts-settings attachment-info">
		<label class="setting">
			<span style="margin-right: 5px;"><?php esc_html_e( 'Font size', 'wp_simple_iconfonts' ); ?></span>
			<input id="wp_simple_iconfonts_font_size" type="number" min="1" step="1" value="14">
			<em>px </em>
		</label>

		<label class="setting">
			<span style="margin-right: 5px;"><?php esc_html_e( 'Font weight', 'wp_simple_iconfonts' ); ?></span>
			<input id="wp_simple_iconfonts_font_weight" type="number" min="400" step="100" value="400">
		</label>

		<label class="setting">
			<span style="margin-right: 5px;"><?php esc_html_e( 'Color', 'wp_simple_iconfonts' ); ?></span>
			<input id="wp_simple_iconfonts_color" type="text" value="">
		</label>
	</div>
</script>
