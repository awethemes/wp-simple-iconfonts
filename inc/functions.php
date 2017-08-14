<?php

if ( ! function_exists( 'wp_simple_iconfonts_field' ) ) :
	function wp_simple_iconfonts_get_icon_url( $type, $id, $size = 'thumbnail' ) {
		$url = '';

		if ( ! in_array( $type, array( 'image', 'svg' ), true ) ) {
			return $url;
		}

		if ( empty( $id ) ) {
			return $url;
		}

		return wp_get_attachment_image_url( $id, $size, false );
	}
endif;

if ( ! function_exists( 'wp_simple_iconfonts_field' ) ) :
	function wp_simple_iconfonts_field( $args, $echo = true ) {
		$defaults = array(
			'id'    => '',
			'name'  => '',
			'value' => array(
				'type' => '',
				'icon' => '',
			),
			'select' => sprintf( '<a class="simple-iconfonts-picker-select">%s</a>', esc_html__( 'Select Icon', 'icon-picker-field' ) ),
			'remove' => sprintf( '<a class="simple-iconfonts-picker-remove button hidden">%s</a>', esc_html__( 'Remove', 'icon-picker-field' ) ),
		);

		$args          = wp_parse_args( $args, $defaults );
		$args['value'] = wp_parse_args( $args['value'], $defaults['value'] );

		$field  = sprintf( '<div id="%s" class="simple-iconfonts-picker">', $args['id'] );
		$field .= $args['select'];
		$field .= $args['remove'];

		foreach ( $args['value'] as $key => $value ) {
			$field .= sprintf(
				'<input type="hidden" id="%s" name="%s" class="%s" value="%s" />',
				esc_attr( "{$args['id']}-{$key}" ),
				esc_attr( "{$args['name']}[{$key}]" ),
				esc_attr( "simple-iconfonts-picker-{$key}" ),
				esc_attr( $value )
			);
		}

		// This won't be saved. It's here for the preview.
		$field .= sprintf(
			'<input type="hidden" class="url" value="%s" />',
			esc_attr( wp_simple_iconfonts_get_icon_url( $args['value']['type'], $args['value']['icon'] ) )
		);
		$field .= '</div>';

		wp_enqueue_style( 'simple-iconfonts-picker' );
		wp_enqueue_script( 'simple-iconfonts-picker' );

		foreach ( wp_simple_iconfonts()->all() as $iconpack ) {
			$iconpack->enqueue_styles();
		}

		if ( $echo ) {
			echo $field; // XSS OK.
		} else {
			return $field;
		}
	}
endif;
