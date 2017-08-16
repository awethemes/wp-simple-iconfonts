# WP Simple Iconfonts

A dead simple, icon fonts for WordPress

![screenshot-2](https://user-images.githubusercontent.com/1529454/29360253-4eee5578-82ac-11e7-97b3-65858d152b2b.png)

## Introduction

This plugin born with simple idea, let you create and manager whatever icon fonts
you want in your site. Integration with other option-framework/metabox-framework, etc... 
to pick icon.

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

Official documentation is [located here.](http://docs.awethemes.com/wp-simple-iconfonts/)

## Integration

If you need show icon-picker, just add this in to your form control:

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
