<?php
/**
 * ACF fields — the WordPress version of the Shopify customizer.
 *
 *  • Options page "Sunflorence settings": header, announcement bar, footer (= Shopify theme settings / header & footer groups)
 *  • Front page "Homepage sections" flexible content (= Shopify index.json): add / remove / reorder sections.
 *
 * Fields are registered in code, so they ship with the theme (no JSON import needed).
 * Requires ACF Pro (flexible content, repeater, options page).
 *
 * @package Sunflorence
 */

defined( 'ABSPATH' ) || exit;

/* ───────────── small field builders (keep the definitions readable) ───────────── */
function sf_f( $type, $name, $label, $extra = array() ) {
	return array_merge( array( 'key' => 'field_sf_' . $name, 'name' => $name, 'label' => $label, 'type' => $type ), $extra );
}
function sf_tab( $label ) {
	return array( 'key' => 'field_sf_tab_' . sanitize_key( $label ), 'label' => $label, 'type' => 'tab', 'placement' => 'top' );
}
/** Fields inside a layout / repeater need unique keys: prefix them. */
function sf_prefix_keys( array $fields, $prefix ) {
	foreach ( $fields as &$f ) {
		$f['key'] = 'field_sf_' . $prefix . '_' . $f['name'];
		if ( ! empty( $f['sub_fields'] ) ) {
			$f['sub_fields'] = sf_prefix_keys( $f['sub_fields'], $prefix . '_' . $f['name'] );
		}
	}
	return $fields;
}

/* ───────────── admin notice when ACF Pro is missing ───────────── */
add_action( 'admin_notices', function () {
	if ( ! function_exists( 'acf_add_local_field_group' ) || ! function_exists( 'acf_add_options_page' ) ) {
		echo '<div class="notice notice-warning"><p><strong>Sunflorence:</strong> '
			. esc_html__( 'Install and activate ACF Pro to edit the homepage sections and theme settings. The site shows default content until then.', 'sunflorence' )
			. '</p></div>';
	}
} );

add_action( 'acf/init', function () {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	/* ═════════════ Options page ═════════════ */
	if ( function_exists( 'acf_add_options_page' ) ) {
		acf_add_options_page( array(
			'page_title' => __( 'Sunflorence settings', 'sunflorence' ),
			'menu_title' => __( 'Sunflorence', 'sunflorence' ),
			'menu_slug'  => 'sunflorence-settings',
			'capability' => 'edit_theme_options',
			'icon_url'   => 'dashicons-store',
			'position'   => 59,
			'redirect'   => false,
		) );
	}

	$trust_icons = array( 'truck' => 'Truck (shipping)', 'return' => 'Return arrow', 'lock' => 'Lock (secure)', 'chat' => 'Chat (support)', 'star' => 'Star', 'clock' => 'Clock' );

	acf_add_local_field_group( array(
		'key'      => 'group_sf_settings',
		'title'    => __( 'Sunflorence settings', 'sunflorence' ),
		'location' => array( array( array( 'param' => 'options_page', 'operator' => '==', 'value' => 'sunflorence-settings' ) ) ),
		'fields'   => array(
			sf_tab( 'Header' ),
			sf_f( 'image', 'logo_color', 'Logo — colour', array( 'return_format' => 'id', 'instructions' => 'Shown once the page scrolls and on pages without a hero. Leave empty for the default Sun Florence logo.' ) ),
			sf_f( 'image', 'logo_white', 'Logo — white', array( 'return_format' => 'id', 'instructions' => 'Shown over the homepage hero.' ) ),
			sf_f( 'number', 'logo_height', 'Logo height (px)', array( 'default_value' => 84, 'min' => 20, 'max' => 140 ) ),
			sf_f( 'number', 'logo_height_mobile', 'Logo height on mobile (px)', array( 'default_value' => 62, 'min' => 16, 'max' => 100 ) ),
			sf_f( 'true_false', 'announce_show', 'Show announcement bar', array( 'default_value' => 1, 'ui' => 1 ) ),
			sf_f( 'text', 'announce_text', 'Announcement text', array( 'default_value' => 'Secure Checkout  |  30-Day Returns  |  24/7 Support' ) ),
			sf_f( 'color_picker', 'announce_bg', 'Announcement background', array( 'default_value' => '#22344a' ) ),
			sf_f( 'color_picker', 'announce_color', 'Announcement text colour', array( 'default_value' => '#D9B99B' ) ),

			sf_tab( 'Footer' ),
			sf_f( 'true_false', 'news_show', 'Show newsletter band', array( 'default_value' => 1, 'ui' => 1 ) ),
			sf_f( 'text', 'news_eyebrow', 'Newsletter eyebrow', array( 'default_value' => 'Join the Sunflorence circle' ) ),
			sf_f( 'text', 'news_title', 'Newsletter title', array( 'default_value' => 'Beauty news, offers & <em>new arrivals</em>', 'instructions' => 'Wrap words in &lt;em&gt; for the italic accent.' ) ),
			sf_f( 'text', 'news_text', 'Newsletter text', array( 'default_value' => 'Sign up to get first dibs on new arrivals, sales, exclusive content, events and more!' ) ),
			sf_f( 'text', 'news_shortcode', 'Newsletter form shortcode (optional)', array( 'instructions' => 'e.g. a Mailchimp / Klaviyo / MailPoet form shortcode. Empty = the built-in form (emails are saved under Sunflorence → Subscribers).' ) ),
			sf_f( 'image', 'footer_logo', 'Footer logo (light version)', array( 'return_format' => 'id' ) ),
			sf_f( 'textarea', 'footer_tagline', 'Tagline', array( 'rows' => 2, 'default_value' => 'Skin care, makeup, hair care, personal care and K-Beauty favourites, handpicked for your everyday routine.' ) ),
			sf_f( 'email', 'contact_email', 'Contact email', array( 'default_value' => 'sunflorencellc@gmail.com' ) ),
			sf_f( 'text', 'contact_phone', 'Phone', array( 'default_value' => '+1 321 357 4656' ) ),
			sf_f( 'textarea', 'contact_address', 'Address', array( 'rows' => 2, 'default_value' => '7901 4th St N, Saint Petersburg, Florida 33702, United States' ) ),
			sf_f( 'text', 'contact_hours', 'Support hours', array( 'default_value' => '24/7 customer support' ) ),
			sf_f( 'url', 'social_facebook', 'Facebook URL' ),
			sf_f( 'url', 'social_instagram', 'Instagram URL' ),
			sf_f( 'url', 'social_tiktok', 'TikTok URL' ),
			sf_f( 'url', 'social_pinterest', 'Pinterest URL' ),
			sf_f( 'url', 'social_youtube', 'YouTube URL' ),
			sf_f( 'text', 'footer_cat_title', 'Categories column title', array( 'default_value' => 'Categories' ) ),
			sf_f( 'taxonomy', 'footer_categories', 'Categories column — product categories', array( 'taxonomy' => 'product_cat', 'field_type' => 'multi_select', 'return_format' => 'id', 'add_term' => 0, 'instructions' => 'Empty = the first 8 product categories.' ) ),
			sf_f( 'text', 'footer_menu_title', 'Menu column title', array( 'default_value' => 'Customer Care', 'instructions' => 'Links come from Appearance → Menus → "Footer — Customer Care".' ) ),
			sf_f( 'text', 'trust_title', 'Trust column title', array( 'default_value' => 'Shop with confidence' ) ),
			sf_f( 'repeater', 'trust_items', 'Trust items', array( 'layout' => 'table', 'max' => 6, 'button_label' => 'Add item', 'sub_fields' => array(
				sf_f( 'select', 'trust_icon', 'Icon', array( 'key' => 'field_sf_trust_icon', 'choices' => $trust_icons, 'default_value' => 'truck' ) ),
				sf_f( 'text', 'trust_item_title', 'Title', array( 'key' => 'field_sf_trust_item_title' ) ),
				sf_f( 'text', 'trust_item_text', 'Text', array( 'key' => 'field_sf_trust_item_text' ) ),
			) ) ),
			sf_f( 'text', 'copyright', 'Copyright', array( 'default_value' => '© 2026 SUNFLORENCE LLC. All rights reserved.' ) ),
			sf_f( 'true_false', 'show_payment_icons', 'Show payment card icons', array( 'default_value' => 1, 'ui' => 1 ) ),
			sf_f( 'color_picker', 'footer_bg', 'Footer background (top)', array( 'default_value' => '#22344A' ) ),
			sf_f( 'color_picker', 'footer_bg_2', 'Footer background (bottom)', array( 'default_value' => '#17243A' ) ),
			sf_f( 'color_picker', 'footer_text', 'Footer text', array( 'default_value' => '#F3EDE6' ) ),
			sf_f( 'color_picker', 'footer_accent', 'Footer accent', array( 'default_value' => '#D9B99B' ) ),
			sf_f( 'color_picker', 'footer_button', 'Footer button', array( 'default_value' => '#6E1F2F' ) ),
		),
	) );

	/* ═════════════ Homepage sections (flexible content) ═════════════ */
	$layouts = array();
	foreach ( sf_section_defs( false ) as $slug => $def ) {
		$layouts[ 'layout_sf_' . $slug ] = array(
			'key'        => 'layout_sf_' . $slug,
			'name'       => $slug,
			'label'      => $def['label'],
			'display'    => 'block',
			'sub_fields' => $def['fields'],
		);
	}

	acf_add_local_field_group( array(
		'key'            => 'group_sf_home',
		'title'          => __( 'Homepage sections', 'sunflorence' ),
		'location'       => array( array( array( 'param' => 'page_type', 'operator' => '==', 'value' => 'front_page' ) ) ),
		'hide_on_screen' => array( 'the_content' ),
		'fields'         => array(
			array(
				'key'          => 'field_sf_home_sections',
				'name'         => 'home_sections',
				'label'        => __( 'Sections', 'sunflorence' ),
				'type'         => 'flexible_content',
				'button_label' => __( 'Add section', 'sunflorence' ),
				'layouts'      => $layouts,
				'instructions' => __( 'Add, remove and drag sections to reorder the homepage — like the Shopify customizer.', 'sunflorence' ),
			),
		),
	) );

	/* ═════════════ Page sections ═════════════
	   Real pages (About, Contact, FAQ, Track order, Wishlist, policies) get their fields on the page,
	   by page template. 404 / search / category archives have no page → "Sunflorence → Pages" screen. */
	$page_templates = array(
		'about'       => 'page-templates/template-about.php',
		'contact'     => 'page-templates/template-contact.php',
		'faq'         => 'page-templates/template-faq.php',
		'track-order' => 'page-templates/template-track-order.php',
		'wishlist'    => 'page-templates/template-wishlist.php',
		'policy'      => 'default',
	);
	$defs = sf_section_defs( true );
	foreach ( $page_templates as $slug => $tpl ) {
		if ( empty( $defs[ $slug ] ) ) {
			continue;
		}
		acf_add_local_field_group( array(
			'key'            => 'group_sf_page_' . $slug,
			'title'          => $defs[ $slug ]['label'],
			'location'       => array( array( array( 'param' => 'page_template', 'operator' => '==', 'value' => $tpl ) ) ),
			'hide_on_screen' => 'policy' === $slug ? array() : array( 'the_content' ),
			'fields'         => $defs[ $slug ]['fields'],
		) );
	}

	if ( function_exists( 'acf_add_options_sub_page' ) ) {
		acf_add_options_sub_page( array(
			'page_title'  => __( 'Shop & system pages', 'sunflorence' ),
			'menu_title'  => __( 'Pages', 'sunflorence' ),
			'menu_slug'   => 'sunflorence-pages',
			'parent_slug' => 'sunflorence-settings',
		) );
		$fields = array();
		foreach ( array( 'product' => 'Product page', 'collection' => 'Category pages', 'list-collections' => 'All categories page', 'search' => 'Search page', '404' => '404 page' ) as $slug => $title ) {
			if ( empty( $defs[ $slug ] ) ) {
				continue;
			}
			$fields[] = array( 'key' => 'field_sf_pg_tab_' . $slug, 'label' => $title, 'type' => 'tab' );
			// options share one namespace → prefix the names: "404__heading"
			foreach ( sf_prefix_names( $defs[ $slug ]['fields'], $slug . '__' ) as $f ) {
				$fields[] = $f;
			}
		}
		acf_add_local_field_group( array(
			'key'      => 'group_sf_pages',
			'title'    => __( 'Shop & system pages', 'sunflorence' ),
			'location' => array( array( array( 'param' => 'options_page', 'operator' => '==', 'value' => 'sunflorence-pages' ) ) ),
			'fields'   => $fields,
		) );
	}
} );

/**
 * Section field definitions generated from the Shopify schemas (tools/convert-shopify.js → inc/fields/*.json).
 * Field names are the Shopify setting ids, so Shopify content imports 1:1.
 *
 * @param bool $pages true = page sections, false = homepage sections.
 */
function sf_section_defs( $pages = false ) {
	static $all = null;
	if ( null === $all ) {
		$all = array();
		foreach ( glob( SF_DIR . '/inc/fields/*.json' ) as $file ) {
			$def = json_decode( (string) file_get_contents( $file ), true ); // phpcs:ignore
			if ( is_array( $def ) ) {
				$all[ basename( $file, '.json' ) ] = $def;
			}
		}
		// hand-made additions (not touched by the converter): inc/fields/extra/<slug>.json → fields appended
		foreach ( glob( SF_DIR . '/inc/fields/extra/*.json' ) as $file ) {
			$slug  = basename( $file, '.json' );
			$extra = json_decode( (string) file_get_contents( $file ), true ); // phpcs:ignore
			if ( isset( $all[ $slug ] ) && ! empty( $extra['fields'] ) ) {
				$all[ $slug ]['fields'] = array_merge( $all[ $slug ]['fields'], $extra['fields'] );
			}
		}
	}
	$order = array( 'hero', 'brand-logos', 'banner-grid', 'featured-products', 'category-showcase', 'makeup-edit', 'image-banner', 'bundles', 'routine-steps', 'see-in-action', 'tested-proven', 'faq-accordion', 'rewards' );
	$out   = array();
	foreach ( $all as $slug => $def ) {
		if ( (bool) $def['page'] === $pages ) {
			$out[ $slug ] = $def;
		}
	}
	if ( ! $pages ) {
		uksort( $out, function ( $a, $b ) use ( $order ) {
			return (int) array_search( $a, $order, true ) - (int) array_search( $b, $order, true );
		} );
	}
	return $out;
}

/** Prefix field names (and keys) for fields that live in the shared options namespace. */
function sf_prefix_names( array $fields, $prefix ) {
	foreach ( $fields as &$f ) {
		if ( ! empty( $f['name'] ) ) {
			$f['name'] = $prefix . $f['name'];
		}
		$f['key'] = $f['key'] . '_' . sanitize_key( $prefix );
	}
	return $fields;
}

/** Value of a "Shop & system pages" option, e.g. sf_page_opt( '404', 'heading' ). */
function sf_page_opt( $page, $key, $default = '' ) {
	if ( '' === $default ) {
		$default = sf_field_default( $page, $key ); // the Shopify value, also before the options are saved
	}
	return sf_opt( $page . '__' . $key, $default );
}
