(function($) {
	'use strict';

	var buildVCAttributes = function(data) {
		var _data = [];

		$.map(data, function(n, i) {
			_data.push(i+':'+encodeURIComponent(n));
		});

		return _data.join('|');
	};

	$('.wpb_el_type_simple_iconfonts').each(function() {
		var $el = $(this);
		var $picker = $el.find('.simple-iconfonts-picker');

		$picker.on('simple-iconfonts-picker:update', function() {
			var $input = $el.find('input.wpb_vc_param_value');

			var data = {
				type: $el.find('input.simple-iconfonts-picker-type').val(),
				icon: $el.find('input.simple-iconfonts-picker-icon').val(),
			};

			if (data.type && data.icon) {
				$input.val(buildVCAttributes(data));
			} else {
				$input.val('');
			}
		});

		$picker.trigger('simple-iconfonts-picker:update');
	});

})(jQuery);
