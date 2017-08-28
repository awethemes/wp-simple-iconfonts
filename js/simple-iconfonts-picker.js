(function($) {
	'use strict';

	var BaseIconPicker = wp.media.view.MediaFrame.IconPicker;
	var Select = wp.media.view.MediaFrame.Select;

	wp.media.controller.IconPickerSimpleImage = wp.media.controller.IconPickerImg.extend();
	wp.media.controller.IconPickerSimpleIconpack = wp.media.controller.IconPickerFont.extend();

	wp.media.controller.SimpleIconPicker = BaseIconPicker.extend({
		initialize: function() {
			_.defaults( this.options, {
				title:       '',
				multiple:    false,
				ipTypes:     _simpleIconFontsPicker.types,
				target:      null,
				SidebarView: null
			});

			if ( this.options.target instanceof wp.media.model.IconPickerTarget ) {
				this.target = this.options.target;
			} else {
				this.target = new wp.media.model.IconPickerTarget();
			}

			Select.prototype.initialize.apply( this, arguments );
		},
	});

	$(function() {
		var l10n = wp.media.view.l10n.iconPicker,
			IconPicker = wp.media.controller.SimpleIconPicker,
			templates = {},
			frame, selectIcon, removeIcon, getFrame, updateField, updatePreview, $field;

		getFrame = function() {
			if ( ! frame ) {
				frame = new IconPicker;
				frame.target.on( 'change', updateField );
			}

			return frame;
		};

		updateField = function( model ) {
			_.each( model.get( 'inputs' ), function( $input, key ) {
				$input.val( model.get( key ) );
			});

			model.clear({ silent: true });
			$field.trigger( 'simple-iconfonts-picker:update' );
		};

		updatePreview = function( e ) {
			var $el     = $( e.currentTarget ),
			    $select = $el.find( 'a.simple-iconfonts-picker-select' ),
			    $remove = $el.find( 'a.simple-iconfonts-picker-remove' ),
			    type    = $el.find( 'input.simple-iconfonts-picker-type' ).val(),
			    icon    = $el.find( 'input.simple-iconfonts-picker-icon' ).val(),
			    url     = $el.find( 'input.url' ).val(),
			    template;

			if ( type === '' || icon === '' || ! _.has( _simpleIconFontsPicker.types, type ) ) {
				$remove.addClass( 'hidden' );
				$select.parent().removeClass('has-icon');

				$select
					.text( '' )
					.attr( 'title', '' );

				return;
			}

			if ( templates[ type ]) {
				template = templates[ type ];
			} else {
				template = templates[ type ] = wp.template( 'iconpicker-' + _simpleIconFontsPicker.types[ type ].templateId + '-icon' );
			}

			$remove.removeClass( 'hidden' );
			$select.parent().addClass('has-icon');

			$select
				.attr( 'title', '' )
				.html( template({
					type: type,
					icon: icon,
					url:  url
				}) );
		};

		selectIcon = function( e ) {
			var frame = getFrame(),
				model = { inputs: {} };

			e.preventDefault();

			$field   = $( e.currentTarget ).closest( '.simple-iconfonts-picker' );
			model.id = $field.attr( 'id' );

			// Collect input fields and use them as the model's attributes.
			$field.find( 'input' ).each( function() {
				var $input = $( this ),
				    key    = $input.attr( 'class' ).replace( 'simple-iconfonts-picker-', '' ),
				    value  = $input.val();

				model[ key ]        = value;
				model.inputs[ key ] = $input;
			});

			frame.target.set( model, { silent: true });
			frame.open();
		};

		removeIcon = function( e ) {
			var $el = $( e.currentTarget ).closest( 'div.simple-iconfonts-picker' );

			$el.find( 'input' ).val( '' );
			$el.trigger( 'simple-iconfonts-picker:update' );
		};

		$( document )
			.on( 'click', 'a.simple-iconfonts-picker-select', selectIcon )
			.on( 'click', 'a.simple-iconfonts-picker-remove', removeIcon )
			.on( 'simple-iconfonts-picker:update', 'div.simple-iconfonts-picker', updatePreview );

		$( 'div.simple-iconfonts-picker' ).trigger( 'simple-iconfonts-picker:update' );
	});

})(jQuery);
