(function($) {
	'use strict';

	wp.customize.controlConstructor['simple_iconfonts'] = wp.customize.Control.extend({
		ready: function() {
			this.initControl();
		},

		initControl: function() {
			var control = this;
			var picker  = this.container.find('.simple-iconfonts-picker');

			picker.on('simple-iconfonts-picker:update', function() {
				control.setting.set({
					type: control.container.find('input.simple-iconfonts-picker-type').val(),
					icon: control.container.find('input.simple-iconfonts-picker-icon').val(),
				});
			});
		}
	});

})(jQuery);
