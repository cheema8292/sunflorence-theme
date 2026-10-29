<?php
/**
 * Template helpers.
 *
 * ACF is optional at runtime: every helper falls back to a default when ACF is not active,
 * so the theme never fatals and still renders sensible content.
 *
 * @package Sunflorence
 */

defined( 'ABSPATH' ) || exit;

/** Global option (ACF Options page → "Sunflorence settings"). */
function sf_opt( $key, $default = '' ) {
	if ( ! function_exists( 'get_field' ) ) {
		return $default;
	}
	$v = get_field( $key, 'option' );
	return ( null === $v || '' === $v || false === $v ) ? $default : $v;
}

/** Field of the current flexible-content row (homepage section). */
function sf_sub( $key, $default = '' ) {
	if ( ! function_exists( 'get_sub_field' ) ) {
		return $default;
	}
	$v = get_sub_field( $key );
	return ( null === $v || '' === $v || false === $v ) ? $default : $v;
}

/** Field of a repeater row passed as array. */
function sf_row( $row, $key, $default = '' ) {
	return ( is_array( $row ) && isset( $row[ $key ] ) && '' !== $row[ $key ] && null !== $row[ $key ] ) ? $row[ $key ] : $default;
}

/**
 * Image tag from an ACF image (array or ID) or attachment ID.
 * Returns '' when there is no image.
 */
function sf_img( $image, $size = 'large', $attr = array() ) {
	$id = is_array( $image ) ? ( $image['ID'] ?? $image['id'] ?? 0 ) : (int) $image;
	if ( ! $id ) {
		return '';
	}
	return wp_get_attachment_image( $id, $size, false, $attr );
}

/** Theme asset URL. */
function sf_asset( $path ) {
	return SF_URI . '/assets/' . ltrim( $path, '/' );
}

/**
 * Allow the small inline formatting used in headings ("Healthy skin, <em>every day</em>").
 */
function sf_inline( $text ) {
	return wp_kses( (string) $text, array( 'em' => array(), 'strong' => array(), 'br' => array(), 'b' => array(), 'i' => array() ) );
}

/** Sanitised hex colour with fallback. */
function sf_color( $value, $fallback ) {
	$c = sanitize_hex_color( (string) $value );
	return $c ? $c : $fallback;
}

/** Product category term (ACF taxonomy field returns ID, object or array). */
function sf_term( $value ) {
	if ( is_array( $value ) ) {
		$value = reset( $value );
	}
	if ( $value instanceof WP_Term ) {
		return $value;
	}
	if ( is_numeric( $value ) ) {
		$t = get_term( (int) $value, 'product_cat' );
		return ( $t && ! is_wp_error( $t ) ) ? $t : null;
	}
	if ( is_string( $value ) && '' !== $value ) {
		$t = get_term_by( 'slug', $value, 'product_cat' );
		return $t ? $t : null;
	}
	return null;
}

/** Inline SVG icons used across the theme. */
function sf_icon( $name ) {
	$icons = array(
		'search'   => '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>',
		'account'  => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
		'cart'     => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><path d="M3 6h18M16 10a4 4 0 01-8 0"/></svg>',
		'chevron'  => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>',
		'arrow'    => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>',
		'prev'     => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 18l-6-6 6-6"/></svg>',
		'next'     => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 6l6 6-6 6"/></svg>',
		'plus'     => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>',
		'close'    => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>',
		'star'     => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>',
		'mail'     => '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg>',
		'phone'    => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.7a2 2 0 0 1-.5 2.1L8 9.8a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.5c.9.3 1.8.6 2.7.7a2 2 0 0 1 1.7 2z"/></svg>',
		'pin'      => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 22s7-6.2 7-12a7 7 0 0 0-14 0c0 5.8 7 12 7 12z"/><circle cx="12" cy="10" r="2.5"/></svg>',
		'clock'    => '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>',
		'truck'    => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M1 3h15v13H1zM16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>',
		'return'   => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5"/></svg>',
		'lock'     => '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>',
		'chat'     => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>',
		'facebook' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 8.5V6.8c0-.8.5-1 1-1h2.5V2H14c-3 0-4 2-4 4.4v2.1H7.5V12H10v10h4V12h3l.5-3.5H14z"/></svg>',
		'instagram'=> '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 7a5 5 0 1 0 0 10 5 5 0 0 0 0-10zm0 8.2a3.2 3.2 0 1 1 0-6.4 3.2 3.2 0 0 1 0 6.4zM17.3 5.5a1.2 1.2 0 1 0 0 2.4 1.2 1.2 0 0 0 0-2.4zM12 2c-2.7 0-3 0-4.1.1C4 2.3 2.3 4 2.1 7.9 2 9 2 9.3 2 12s0 3 .1 4.1c.2 3.9 1.9 5.6 5.8 5.8 1.1.1 1.4.1 4.1.1s3 0 4.1-.1c3.9-.2 5.6-1.9 5.8-5.8.1-1.1.1-1.4.1-4.1s0-3-.1-4.1C21.7 4 20 2.3 16.1 2.1 15 2 14.7 2 12 2z"/></svg>',
		'tiktok'   => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19.6 6.7a4.8 4.8 0 0 1-3.8-4.3V2h-3.4v13.7a2.9 2.9 0 1 1-2-2.8V9.4a6.3 6.3 0 1 0 5.4 6.3v-7a8.2 8.2 0 0 0 4.8 1.5V6.8a4.9 4.9 0 0 1-1-.1z"/></svg>',
		'pinterest'=> '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a10 10 0 0 0-3.6 19.3c-.1-.8-.2-2 0-2.9l1.2-5s-.3-.6-.3-1.5c0-1.4.8-2.5 1.8-2.5.9 0 1.3.7 1.3 1.4 0 .9-.6 2.2-.8 3.4-.2 1 .5 1.9 1.6 1.9 1.9 0 3.3-2 3.3-4.9 0-2.6-1.8-4.4-4.5-4.4a4.6 4.6 0 0 0-4.8 4.6c0 .9.3 1.9.8 2.4l-.3 1.3c0 .2-.2.2-.4.1-1.4-.6-2.2-2.6-2.2-4.2 0-3.4 2.5-6.6 7.2-6.6 3.8 0 6.7 2.7 6.7 6.3 0 3.8-2.4 6.8-5.7 6.8-1.1 0-2.2-.6-2.5-1.3l-.7 2.6c-.2 1-.9 2.2-1.4 2.9A10 10 0 1 0 12 2z"/></svg>',
		'youtube'  => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M23.5 6.2a3 3 0 0 0-2.1-2.1C19.5 3.5 12 3.5 12 3.5s-7.5 0-9.4.6A3 3 0 0 0 .5 6.2 31 31 0 0 0 0 12a31 31 0 0 0 .5 5.8 3 3 0 0 0 2.1 2.1c1.9.6 9.4.6 9.4.6s7.5 0 9.4-.6a3 3 0 0 0 2.1-2.1A31 31 0 0 0 24 12a31 31 0 0 0-.5-5.8zM9.6 15.6V8.4l6.2 3.6-6.2 3.6z"/></svg>',
	);
	return $icons[ $name ] ?? '';
}

/* ═════════════ Section context ═════════════
   Section templates read their settings with sf_s( 'setting_id' ). Where the value comes from depends on
   where the section is rendered:
     'row'          → current homepage flexible-content row (get_sub_field)
     'post:<page>'  → a page using a page template (get_field on the page), e.g. post:about
     'opt:<page>'   → "Sunflorence → Pages" options (e.g. opt:404, opt:search)
     'data'         → a row of assets/data/home-content.json (homepage before the import / without ACF)
   For post/opt, an empty setting falls back to the field's default (= the Shopify value). */
$GLOBALS['sf_ctx'] = 'row';

function sf_ctx( $ctx ) {
	$GLOBALS['sf_ctx'] = $ctx;
}

function sf_s( $key, $default = '' ) {
	$ctx = $GLOBALS['sf_ctx'] ?? 'row';
	$acf = function_exists( 'get_field' );
	if ( 'row' === $ctx ) {
		$v = $acf ? get_sub_field( $key ) : null;
	} elseif ( 'data' === $ctx ) {
		$v = $GLOBALS['sf_data_row'][ $key ] ?? null; // homepage rendered from assets/data/home-content.json
	} else {
		list( $where, $page ) = array_pad( explode( ':', $ctx, 2 ), 2, '' );
		$v = ! $acf ? null : ( 'post' === $where ? get_field( $key, get_queried_object_id() ) : get_field( $page . '__' . $key, 'option' ) );
		if ( '' === $default && $page ) {
			// Shopify value — the page's content (pages) or the setting's default; also when ACF isn't active
			$pd      = 'post' === $where ? sf_page_data( $page ) : array();
			$default = isset( $pd[ $key ] ) && ! is_array( $pd[ $key ] ) ? $pd[ $key ] : sf_field_default( $page, $key );
		}
	}
	return ( null === $v || '' === $v || false === $v ) ? $default : $v;
}

/**
 * Shopify link → WordPress link: /collections/all → shop, /collections/x → category x,
 * /collections/vendors?q=x → product search, /pages/x → /x/, /products/x → /product/x/, /policies/x → /x/.
 * Other site-relative paths get the WordPress home URL in front (site may live in a sub-folder).
 */
function sf_url( $url ) {
	$url = trim( (string) $url );
	if ( '' === $url || 0 === strpos( $url, 'shopify://' ) ) {
		return '';
	}
	if ( '/' !== $url[0] || 0 === strpos( $url, '//' ) ) {
		return $url; // absolute, #anchor, mailto: …
	}
	$shop = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
	if ( preg_match( '#^/collections/vendors\?q=(.+)$#', $url, $m ) ) {
		return add_query_arg( array( 's' => rawurldecode( $m[1] ), 'post_type' => 'product' ), home_url( '/' ) );
	}
	if ( preg_match( '#^/collections/?(all)?/?$#', $url ) ) {
		return $shop;
	}
	if ( preg_match( '#^/collections/([^/?]+)#', $url, $m ) ) {
		$t = taxonomy_exists( 'product_cat' ) ? get_term_by( 'slug', $m[1], 'product_cat' ) : null;
		return $t ? get_term_link( $t ) : $shop;
	}
	if ( preg_match( '#^/(pages|policies)/([^/?]+)#', $url, $m ) ) {
		return home_url( '/' . $m[2] . '/' );
	}
	if ( preg_match( '#^/products/([^/?]+)#', $url, $m ) ) {
		return home_url( '/product/' . $m[1] . '/' );
	}
	return home_url( $url );
}

/** Apply sf_url() to every "…url" / "…link" value of Shopify content (recursively). */
function sf_fix_links( $data ) {
	if ( is_array( $data ) ) {
		foreach ( $data as $k => $v ) {
			$data[ $k ] = ( is_string( $v ) && is_string( $k ) && preg_match( '/(url|link)$/', $k ) ) ? sf_url( $v ) : sf_fix_links( $v );
		}
	}
	return $data;
}

/** Homepage sections from the Shopify theme (assets/data/home-content.json), links made WordPress links. */
function sf_home_data() {
	static $rows = null;
	if ( null === $rows ) {
		$file = SF_DIR . '/assets/data/home-content.json';
		$rows = file_exists( $file ) ? json_decode( (string) file_get_contents( $file ), true ) : array(); // phpcs:ignore
		$rows = is_array( $rows ) ? sf_fix_links( $rows ) : array();
	}
	return $rows;
}

/** Content of a page in the Shopify theme (assets/data/pages-content.json), e.g. sf_page_data( 'about' ). */
function sf_page_data( $page ) {
	static $data = null;
	if ( null === $data ) {
		$file = SF_DIR . '/assets/data/pages-content.json';
		$data = file_exists( $file ) ? sf_fix_links( (array) json_decode( (string) file_get_contents( $file ), true ) ) : array(); // phpcs:ignore
	}
	return isset( $data[ $page ] ) && is_array( $data[ $page ] ) ? $data[ $page ] : array();
}

/** Default value of a page field (inc/fields/<page>.json) — the value the Shopify theme used. */
function sf_field_default( $page, $key ) {
	$defs = function_exists( 'sf_section_defs' ) ? sf_section_defs( true ) : array();
	foreach ( $defs[ $page ]['fields'] ?? array() as $f ) {
		if ( ( $f['name'] ?? '' ) === $key ) {
			return $f['default_value'] ?? '';
		}
	}
	return '';
}

/** Repeater rows of a section ("blocks_<type>" — the Shopify block type). */
function sf_blocks( $type ) {
	$rows = sf_s( 'blocks_' . $type, array() );
	if ( ! $rows && 0 === strpos( $GLOBALS['sf_ctx'] ?? 'row', 'post:' ) ) {
		// page never saved with its fields (or ACF not active) → the Shopify content (assets/data/pages-content.json)
		$page = substr( $GLOBALS['sf_ctx'], 5 );
		if ( ! metadata_exists( 'post', get_queried_object_id(), 'blocks_' . $type ) ) {
			$rows = sf_page_data( $page )[ 'blocks_' . $type ] ?? array();
		}
	}
	return is_array( $rows ) ? $rows : array();
}

/**
 * CSS custom properties for a section: --s-<id>: value, for each setting that has a value.
 * The converted Shopify CSS uses var(--s-<id>, <shopify default>), so empty settings keep the Shopify default.
 */
function sf_vars( array $keys ) {
	$css = '';
	foreach ( $keys as $k ) {
		$v = sf_s( $k, '' );
		if ( '' === $v || is_array( $v ) || is_bool( $v ) ) {
			continue;
		}
		$v = (string) $v;
		if ( preg_match( '/^#[0-9a-f]{3,8}$|^-?\d+(\.\d+)?$|^rgba?\([\d\s.,%]+\)$|^[a-z-]+$/i', $v ) ) {
			$css .= '--s-' . $k . ':' . $v . ';';
		}
	}
	return $css;
}

/** Money for plain price text that came from Shopify settings (e.g. "$19.99"). */
function sf_text_price( $v ) {
	return esc_html( (string) $v );
}
