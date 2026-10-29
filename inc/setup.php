<?php
/**
 * Theme setup: supports, menus, image sizes.
 *
 * @package Sunflorence
 */

defined( 'ABSPATH' ) || exit;

add_action( 'after_setup_theme', function () {
	load_theme_textdomain( 'sunflorence', SF_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo', array( 'height' => 120, 'width' => 320, 'flex-height' => true, 'flex-width' => true ) );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_theme_support( 'responsive-embeds' );

	// WooCommerce
	add_theme_support( 'woocommerce', array(
		'thumbnail_image_width' => 600,
		'single_image_width'    => 1000,
		'product_grid'          => array( 'default_columns' => 4, 'min_columns' => 2, 'max_columns' => 5 ),
	) );
	// no WooCommerce gallery scripts: the theme has its own gallery (Shopify layout)

	register_nav_menus( array(
		'main-menu'   => __( 'Sidebar menu (with sub-menus)', 'sunflorence' ),
		'footer-menu' => __( 'Footer — Customer Care', 'sunflorence' ),
	) );

	add_image_size( 'sf-card', 700, 700, false );
	add_image_size( 'sf-hero', 2000, 1200, false );
} );

/** Body class so the header knows whether a hero sits under it. */
add_filter( 'body_class', function ( $classes ) {
	if ( is_front_page() ) {
		$classes[] = 'sf-has-hero';
	}
	return $classes;
} );
