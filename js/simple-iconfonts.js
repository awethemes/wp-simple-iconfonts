(function($, setting) {
	'use strict';

	/**
	 * Trigger dropdown toggle.
	 */
	var ToggleDropdown = function(el) {
		this.$el = $(el);
		var self = this;

		$(this.$el).on('click', function(e) {
			e.preventDefault();
			self.$el.find('+ .split-button-body').toggleClass('open');
		});

		$(document).on('click', function(e) {
			var $dropdown = self.$el.find('+ .split-button-body')
			if ($.contains(self.$el[0], e.target)) {
				return;
			}

			if ($dropdown.hasClass('open')) {
				$dropdown.removeClass('open');
			}
		});
	};

	$(function() {
		$('[data-toggle="simple-iconfonts-dropdown"]').each(function() {
			new ToggleDropdown(this);
		});

		$('.delete-button').on('click', function() {
			if (! confirm(setting.strings.warning_delete)) {
				return false;
			}
		});

		$('[href="#toggle-upload-form"]').on('click', function(e) {
			e.preventDefault();
			$('#toggle-upload-form').toggleClass('open');
		});
	});

})(jQuery, window._simpleIconfonts);
