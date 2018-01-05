(function($) {
	'use strict';

	var IconPicker = wp.media.controller.SimpleIconPicker;

	var WP_SimpleIconFonts_Sidebar = wp.media.view.IconPickerSidebar.extend({
		initialize: function() {
			wp.media.view.IconPickerSidebar.prototype.initialize.apply(this, arguments);

			this.$el.html($('#wp_simple_iconfonts_shortcode-template').html());

			// Setup some plugins.
			$('#wp_simple_iconfonts_color', this.$el).wpColorPicker();
		},

		createSingle: function() {
		},

		disposeSingle: function() {
		},
	});

	tinymce.create("tinymce.plugins.wp_simple_icons_button", {
		frame: null,

		init: function(editor, url) {
			this.frame = new IconPicker({
				SidebarView: WP_SimpleIconFonts_Sidebar
			});

			this.frame.target.on('change', this.handleInsertShortcode.bind(this, editor));

			editor.addButton('wp_simple_icons_button', {
				title : 'WP Simple Iconfont',
				icon: 'icon dashicons-star-empty',
				onclick: this.handleOnClick.bind(this),
			});
		},

		handleOnClick: function(e) {
			this.getFrame().open();
		},

		handleInsertShortcode: function(editor, model) {
			var icon_atts = model.attributes;
			if ( ! icon_atts ) {
				return;
			}

			var type = icon_atts.type,
				icon = icon_atts.icon,
				url  = icon_atts.url;

			// Build shortcode attributes.
			var $attributes = {
				type: type,
				icon: icon,
				url: url,
				color: $('#wp_simple_iconfonts_color').val(),
				font_size: $('#wp_simple_iconfonts_font_size').val(),
				font_weight: $('#wp_simple_iconfonts_font_weight').val(),
			};

			var $html_attributes = '';
			$.each( $attributes, function(key, value) {
				$html_attributes += key + '="' + value + '" ';
			});

			editor.insertContent('[wp_simple_iconfonts ' + $html_attributes +']');
		},

		getFrame: function() {
			return this.frame;
		},
	});

	tinymce.PluginManager.add('wp_simple_icons_button', tinymce.plugins.wp_simple_icons_button);

})(jQuery);
