<?php
/**
 * Styles and scripts.
 *
 * assets/
 *   css/global/       base, header, footer, product-card   (every page)
 *   css/sections/     one file per section (from the Shopify sections)
 *   css/pages/        page templates (about, contact, faq …)
 *   css/woocommerce/  product page, category pages, cart drawer
 *   js/global/        header (drawer, sticky)
 *   js/sections/      one file per section that needs JS
 *   js/woocommerce/   product page, cart drawer, filters
 *
 * @package Sunflorence
 */

defined( 'ABSPATH' ) || exit;

/** Enqueue a section's CSS (and JS if it has one). Safe to call more than once. */
function sf_enqueue_section( $slug, $group = 'sections' ) {
	$css = 'css/' . $group . '/' . $slug . '.css';
	$js  = 'js/' . $group . '/' . $slug . '.js';
	if ( file_exists( SF_DIR . '/assets/' . $css ) ) {
		wp_enqueue_style( 'sf-' . $group . '-' . $slug, sf_asset( $css ), array( 'sf-base' ), sf_asset_ver( $css ) );
	}
	if ( file_exists( SF_DIR . '/assets/' . $js ) ) {
		wp_enqueue_script( 'sf-' . $group . '-' . $slug, sf_asset( $js ), array(), sf_asset_ver( $js ), true );
	}
}

/** Cache-busting version = file modification time. */
function sf_asset_ver( $rel ) {
	$f = SF_DIR . '/assets/' . $rel;
	return file_exists( $f ) ? (string) filemtime( $f ) : SF_VERSION;
}

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'sf-fonts', 'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;1,300;1,400;1,500&family=Montserrat:wght@400;500;600;700;800&display=swap', array(), null );

	foreach ( array( 'base', 'header', 'footer', 'product-card' ) as $g ) {
		wp_enqueue_style( 'sf-' . $g, sf_asset( 'css/global/' . $g . '.css' ), 'base' === $g ? array( 'sf-fonts' ) : array( 'sf-base' ), sf_asset_ver( 'css/global/' . $g . '.css' ) );
	}
	wp_enqueue_script( 'sf-header', sf_asset( 'js/global/header.js' ), array(), sf_asset_ver( 'js/global/header.js' ), true );

	// Homepage: only the sections actually used.
	if ( is_front_page() ) {
		$used = array();
		if ( function_exists( 'get_field' ) ) {
			foreach ( (array) get_field( 'home_sections', get_queried_object_id() ) as $row ) {
				if ( ! empty( $row['acf_fc_layout'] ) ) {
					$used[ $row['acf_fc_layout'] ] = true;
				}
			}
		}
		if ( ! $used ) {
			foreach ( sf_home_data() as $row ) { // homepage from assets/data (see front-page.php)
				$used[ $row['acf_fc_layout'] ?? '' ] = true;
			}
			unset( $used[''] );
		}
		foreach ( array_keys( $used ) as $slug ) {
			sf_enqueue_section( sanitize_key( $slug ) );
		}
	}

	// Page templates
	if ( is_page_template() ) {
		$tpl = basename( (string) get_page_template_slug(), '.php' ); // template-about
		sf_enqueue_section( str_replace( 'template-', '', $tpl ), 'pages' );
	} elseif ( is_page() && ! is_front_page() && ! ( function_exists( 'is_woocommerce' ) && ( is_cart() || is_checkout() || is_account_page() ) ) ) {
		sf_enqueue_section( 'policy', 'pages' ); // default page template = Shopify rr-policy layout
	}
	// Wishlist: the page + the "Save to wishlist" button on product pages
	if ( is_page_template( 'page-templates/template-wishlist.php' ) || ( function_exists( 'is_product' ) && is_product() ) ) {
		wp_enqueue_script( 'sf-wishlist', sf_asset( 'js/global/wishlist.js' ), array(), sf_asset_ver( 'js/global/wishlist.js' ), true );
	}
	if ( is_404() ) {
		sf_enqueue_section( '404', 'pages' );
	}
	if ( is_search() ) {
		sf_enqueue_section( 'search', 'pages' );
	}

	// WooCommerce
	if ( class_exists( 'WooCommerce' ) ) {
		wp_enqueue_script( 'wc-cart-fragments' );
		wp_enqueue_script( 'wc-add-to-cart' );
		if ( ! is_cart() && ! is_checkout() ) {
			wp_enqueue_style( 'sf-wc-cart-drawer', sf_asset( 'css/woocommerce/cart-drawer.css' ), array( 'sf-base' ), sf_asset_ver( 'css/woocommerce/cart-drawer.css' ) );
			wp_enqueue_script( 'sf-wc-cart-drawer', sf_asset( 'js/woocommerce/cart-drawer.js' ), array( 'jquery', 'wc-cart-fragments' ), sf_asset_ver( 'js/woocommerce/cart-drawer.js' ), true );
			wp_localize_script( 'sf-wc-cart-drawer', 'sfCart', array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'sf_cart' ),
			) );
		}
		if ( is_product() ) {
			// rendered from the Shopify product page: section CSS, block CSS, reviews carousel, announcement bar
			foreach ( array( 'product', 'product-blocks', 'reviews', 'announcement-bar', 'product-wp' ) as $css ) {
				$rel = 'css/woocommerce/' . $css . '.css';
				wp_enqueue_style( 'sf-wc-' . $css, sf_asset( $rel ), array( 'sf-base' ), sf_asset_ver( $rel ) );
			}
			wp_enqueue_script( 'wc-add-to-cart-variation' );
			wp_enqueue_script( 'sf-wc-product', sf_asset( 'js/woocommerce/product.js' ), array( 'jquery', 'wc-add-to-cart-variation' ), sf_asset_ver( 'js/woocommerce/product.js' ), true );
			wp_localize_script( 'sf-wc-product', 'sfProduct', array(
				'symbol'   => html_entity_decode( get_woocommerce_currency_symbol(), ENT_QUOTES, 'UTF-8' ),
				'format'   => get_woocommerce_price_format(),
				'decimals' => wc_get_price_decimals(),
				'decimal'  => wc_get_price_decimal_separator(),
				'thousand' => wc_get_price_thousand_separator(),
				'cartUrl'  => wc_get_cart_url(),
				'i18n'     => array(
					'soldOut' => __( 'Sold out', 'sunflorence' ),
					'error'   => __( 'Could not add to cart. Please try again.', 'sunflorence' ),
				),
			) );
			if ( comments_open() ) {
				wp_enqueue_script( 'wc-single-product' ); // star rating input in the review form
			}
			sf_enqueue_section( 'faq-accordion' );
			sf_enqueue_section( 'featured-products' );
		}
		if ( is_shop() || is_product_taxonomy() ) {
			sf_enqueue_section( 'collection', 'woocommerce' );
		}
	}
} );

/** Product rows & spacing (like theskinfit.com) — after all section CSS so it wins. */
add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'sf-product-density', sf_asset( 'css/global/product-density.css' ), array( 'sf-base' ), sf_asset_ver( 'css/global/product-density.css' ) );
}, 99 );

/**
 * WooCommerce's default CSS only where the theme doesn't style the page itself
 * (product + category pages are styled like Shopify; cart, checkout, account keep WooCommerce's CSS).
 */
add_filter( 'woocommerce_enqueue_styles', function ( $styles ) {
	if ( is_product() || is_shop() || is_product_taxonomy() ) {
		return array();
	}
	return $styles;
} );

add_filter( 'wp_resource_hints', function ( $urls, $relation ) {
	if ( 'preconnect' === $relation ) {
		$urls[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' );
	}
	return $urls;
}, 10, 2 );
