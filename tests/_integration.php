<?php

use WP_Simple_Iconfonts\Support\WP_Simple_Iconfonts_Control;

function __customize_register( $wp_customize ) {
	$wp_customize->add_setting( 'wp_simple_iconfonts', array(
		'default' => [],
	) );

	$wp_customize->add_control( new WP_Simple_Iconfonts_Control( $wp_customize, 'wp_simple_iconfonts', array(
		'label'   => 'Icon',
		'section' => 'colors',
	) ) );
}
add_action( 'customize_register', '__customize_register' );

if ( class_exists( 'Kirki' ) ) {
	Kirki::add_field( 'my_config', array(
		'settings' => 'my_setting',
		'label'    => __( 'My custom control', 'translation_domain' ),
		'section'  => 'colors',
		'type'     => 'simple_iconfonts',
		'priority' => 10,
		'default'  => 'some-default-value',
	) );
}

add_filter( 'rwmb_meta_boxes', function ( $meta_boxes ) {
	$meta_boxes[] = [
		'title'  => 'Metabox.io',
		'fields' => [
			[
				'name' => 'Icon',
				'id'   => '__icon__',
				'type' => 'simple_iconfonts',
			],
		],
	];

	return $meta_boxes;
} );

function cmb2_text_email_metabox() {
	$cmb = new_cmb2_box( array(
		'id'           => 'cmb2_text_email_metabox',
		'title'        => 'CMB2 Metabox',
		'object_types' => array( 'post' ),
	) );

	$cmb->add_field( array(
		'name' => 'Email',
		'id'   => '_cmb2_person_email',
		'type' => 'simple_iconfonts',
		'desc' => 'Invalid email addresses will be wiped out.',
	));
}
add_action( 'cmb2_admin_init', 'cmb2_text_email_metabox' );

if(function_exists("register_field_group"))
{
	register_field_group(array (
		'id' => 'acf_acf-field',
		'title' => 'ACF FIeld',
		'fields' => array (
			array (
				'key' => 'field_5994372001acc',
				'label' => 'Icon',
				'name' => 'icon',
				'type' => 'simple_iconfonts',
				'default_value' => '',
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'post',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'normal',
			'layout' => 'default',
			'hide_on_screen' => array (
			),
		),
		'menu_order' => 0,
	));
}

/**
 * Define the metabox and field configurations.
 *
 * @param  array $meta_boxes Existing metaboxes.
 * @return array
 */
function cmb_sample_metaboxes( array $meta_boxes ) {

	/**
	 * Example of all available fields.
	 */
	$fields = array(
		array(
			'id'   => 's-icon-pciker',
			'name' => 'Icon',
			'type' => 'simple_iconfonts',
		),
		array(
			'id'   => 's-fdsfdsicon-pciker',
			'name' => 'Icon',
			'type' => 'simple_iconfonts',
			'repeatable' => true,
			'sortable' => true,
		),
	);

	/**
	 * Metabox instantiation.
	 */
	$meta_boxes[] = array(
		'title' => 'CMB Test Icon Picker',
		'pages' => 'post',
		'fields' => $fields,
	);

	return $meta_boxes;

}
add_filter( 'cmb_meta_boxes', 'cmb_sample_metaboxes' );

if ( class_exists( 'Redux' ) ) {
	Redux::setArgs( 'sdasd_ddd' );

    Redux::setSection( 'sdasd_ddd', array(
        'title'            => __( 'Icon', 'redux-framework-demo' ),
        'id'               => 'basic-icon',
        'fields'           => array(
            array(
                'id'       => 'opt-icon',
                'type'     => 'simple_iconfonts',
                'title'    => __( 'Icon', 'redux-framework-demo' ),
                'default'  => [
                    'type' => 'dashicons',
                    'icon' => 'dashicons-portfolio',
                ]
            ),
        )
    ) );
}

if ( class_exists('TitanFramework') ) {
	add_action( 'tf_create_options', function() {
		$titan = TitanFramework::getInstance( 'mytheme' );

		$ss = $titan->createMetaBox( array(
			'name' => __( 'TitanFramework Metabox', 'mytheme' ),
			'post_type' => 'post',
		) );
		$ss->createOption( array(
			'name' => __( 'Icon', 'mytheme' ),
			'id' => '___dasdicon',
			'type' => 'simple_iconfonts',
		) );

		$as = $titan->createAdminPage( array(
			'name' => __( 'TitanFramework Admin', 'mytheme' ),
			'post_type' => 'post',
		) );
		$as->createOption( array(
			'name' => __( 'Icon', 'mytheme' ),
			'id' => '___dassddicon',
			'type' => 'simple_iconfonts',
		) );
		$as->createOption( array(
			'type' => 'save'
		) );

		$a = $titan->createCustomizer( array(
			'name' => __( 'TitanFramework Customizer', 'mytheme' ),
			'post_type' => 'post',
		) );
		$a->createOption( array(
			'name' => __( 'Icon', 'mytheme' ),
			'id' => '___dassdsdicon',
			'type' => 'simple_iconfonts',
		) );
	});
}
