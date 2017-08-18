# WP Simple Iconfonts

An icon fonts manager and picker for WordPress

![screenshot-2](https://user-images.githubusercontent.com/1529454/29360253-4eee5578-82ac-11e7-97b3-65858d152b2b.png)

## Introduction

If you looking for an icon picker, this's last choice your need.

### Features

- Allow you import/register **unlimit icon fonts** in to your site and manager them.
- Provider a picker-icon, integrated with almost popular WordPress plugins, frameworks, etc...
- Fluent API, easy use for other.

### Links

- [WordPress Plugin](https://wordpress.org/plugins/wp-simple-iconfonts/)
- [Official documentation](http://docs.awethemes.com/wp-simple-iconfonts/)

## Integration

### Icon-picker

WP Simple Iconfonts come with large framework, plugins, supports, in list below:

- [x] WP Customizer
- [x] [CMB2](https://wordpress.org/plugins/cmb2/)
- [x] [ACF](https://wordpress.org/plugins/advanced-custom-fields/)
- [x] [Kirki](https://wordpress.org/plugins/kirki/)
- [x] [Redux Framework](https://wordpress.org/plugins/redux-framework/)
- [x] [Titan Framework](https://wordpress.org/plugins/titan-framework/)
- [x] [CMB by humanmade](https://github.com/humanmade/Custom-Meta-Boxes)
- [x] [Metabox](https://wordpress.org/plugins/meta-box/)
- [ ] Visual Composer
- [ ] https://github.com/valendesigns/option-tree
- [ ] https://github.com/devinsays/options-framework-plugin
- [ ] https://github.com/Codestar/codestar-framework

See the [example](https://github.com/awethemes/wp-simple-iconfonts/blob/master/tests/_integration.php) to see how it works.

We also have a function to display picker field, checkout this example:

```php
<?php

// Get value from database...
$values = array(
	'type' => '',
	'icon' => '',
);

wp_simple_iconfonts_field( array(
	'id'    => '_simple_icon',
	'name'  => '_simple_icon',
	'value' => $values
) );
```

## How I add my icon pack in PHP?

Checkout this very simple example:

> NOTE: An icon pack registerd via PHP will be set "lock" status, you can't deactive or remove it from dashboard.

```php
<?php

function register_new_icons( $iconfonts ) {
    $iconpack = new \WP_Simple_Iconfonts\Iconpack(array(
        'id'             => 'dashicons',
        'name'           => 'Display name',
        'version'        => '0.1.1',
        'stylesheet_uri' => 'maybe_same_your_id',
        'stylesheet_uri' => 'http://your-site.com/path-to-styles.css',
        $groups = array(
            array(
                'id'   => 'admin',
                'name' => 'Admin',
            ),
            array(
                'id'   => 'post-formats',
                'name' => 'Post Formats',
            ),
        ),
        'icons'          => array(
            array(
                'id'    => 'dashicons-admin-appearance',
                'name'  => 'Appearance',
                'group' => 'admin',
            ),
            array(
                'id'    => 'dashicons-admin-collapse',
                'name'  => 'Collapse',
                'group' => 'admin',
            ),
            array(
                'id'    => 'dashicons-format-standard',
                'name'  => 'Standard',
                'group' => 'post-formats',
            ),
        ),

    ));

    $iconfonts->register( $iconpack );
}
add_action( 'wp_simple_iconfonts', 'register_new_icons' );
```

## Supports

- Fontello (http://fontello.com)
- Icomoon (https://icomoon.io/app)
- Font Awesome (http://fontawesome.io)
- Foundation Icon Fonts 3 (http://zurb.com/playground/foundation-icon-fonts-3)
- Ionicons (http://ionicons.com)
- Elusive (http://elusiveicons.com)
- PaymentFont (http://paymentfont.io)
- Pixeden (http://www.pixeden.com/icon-fonts)
- Themify Icons (https://themify.me/themify-icons)
- Typicons (http://typicons.com)
- Map Icons (http://map-icons.com)
