<?php
/**
 * "Import Shopify content" — fills the WordPress homepage and pages with the content
 * from the Sunflorence Shopify theme (assets/data/*.json, made by tools/convert-shopify.js).
 *
 * - Texts, headings, links, colours, FAQs, steps … are copied as they are.
 * - Shopify categories ("skin-care", "cleansers" …) are matched to WooCommerce product
 *   categories with the same slug. Missing ones are left empty (pick them later in the admin).
 * - Images/videos hosted on Shopify cannot be moved automatically → re-select them in the admin.
 *
 * @package Sunflorence
 */

defined( 'ABSPATH' ) || exit;

add_action( 'admin_menu', function () {
	add_submenu_page(
		function_exists( 'acf_add_options_page' ) ? 'sunflorence-settings' : 'themes.php',
		__( 'Import Shopify content', 'sunflorence' ),
		__( 'Import Shopify content', 'sunflorence' ),
		'manage_options',
		'sf-import',
		'sf_import_page'
	);
}, 30 );

function sf_import_page() {
	$done = array();
	if ( isset( $_POST['sf_import'] ) && check_admin_referer( 'sf_import' ) && current_user_can( 'manage_options' ) ) {
		if ( ! empty( $_POST['sf_cats'] ) ) {
			$done[] = sf_import_categories();
		}
		if ( ! empty( $_POST['sf_home'] ) ) {
			$done[] = sf_import_home();
		}
		if ( ! empty( $_POST['sf_pages'] ) ) {
			foreach ( sf_import_pages() as $r ) {
				$done[] = $r;
			}
		}
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Import Shopify content', 'sunflorence' ); ?></h1>
		<?php foreach ( $done as $d ) : ?>
			<div class="notice notice-<?php echo esc_attr( 'ok' === $d[0] ? 'success' : ( 'warn' === $d[0] ? 'warning' : 'error' ) ); ?>"><p><?php echo esc_html( $d[1] ); ?></p></div>
		<?php endforeach; ?>
		<p><?php esc_html_e( 'Copies the homepage sections and page texts from the Sunflorence Shopify theme into this site.', 'sunflorence' ); ?></p>
		<ul style="list-style:disc;padding-left:20px">
			<li><?php esc_html_e( 'Product categories are created with the slugs the homepage sections use (skin-care, cleansers, makeup, lips …). Existing categories are kept.', 'sunflorence' ); ?></li>
			<li><?php esc_html_e( 'Without ACF the pages are still created; their texts then come from the theme files.', 'sunflorence' ); ?></li>
			<li><?php esc_html_e( 'Images and videos are not copied (they are hosted on Shopify) — add them in each section afterwards.', 'sunflorence' ); ?></li>
			<li><?php esc_html_e( 'Importing again replaces the homepage sections.', 'sunflorence' ); ?></li>
		</ul>
		<form method="post">
			<?php wp_nonce_field( 'sf_import' ); ?>
			<p><label><input type="checkbox" name="sf_home" value="1" checked> <?php esc_html_e( 'Homepage sections (creates a "Home" page and sets it as the front page if needed)', 'sunflorence' ); ?></label></p>
			<p><label><input type="checkbox" name="sf_pages" value="1" checked> <?php esc_html_e( 'Pages: About, Contact, FAQ, Track order, Wishlist, All Categories', 'sunflorence' ); ?></label></p>
			<p><button class="button button-primary" name="sf_import" value="1"><?php esc_html_e( 'Run', 'sunflorence' ); ?></button></p>
		</form>
	</div>
	<?php
}

/** Read a JSON file from assets/data. */
function sf_import_data( $file ) {
	$path = SF_DIR . '/assets/data/' . $file;
	return file_exists( $path ) ? sf_fix_links( (array) json_decode( (string) file_get_contents( $path ), true ) ) : array(); // phpcs:ignore — Shopify links → WordPress links
}

/**
 * Turn Shopify values into ACF values using the field definitions:
 * category handles → term IDs, checkboxes → 0/1, unknown fields dropped.
 */
function sf_import_map_values( array $values, array $fields, array &$missing ) {
	$out = array();
	foreach ( $fields as $f ) {
		if ( empty( $f['name'] ) || ! array_key_exists( $f['name'], $values ) ) {
			continue;
		}
		$v = $values[ $f['name'] ];
		switch ( $f['type'] ) {
			case 'taxonomy':
				$ids = array();
				foreach ( (array) $v as $handle ) {
					$t = $handle ? get_term_by( 'slug', sanitize_title( $handle ), 'product_cat' ) : null;
					if ( $t ) {
						$ids[] = (int) $t->term_id;
					} elseif ( $handle ) {
						$missing[ $handle ] = true;
					}
				}
				$v = 'multi_select' === ( $f['field_type'] ?? '' ) ? $ids : ( $ids ? $ids[0] : '' );
				break;
			case 'true_false':
				$v = $v ? 1 : 0;
				break;
			case 'image':
			case 'file':
			case 'post_object':
				continue 2; // Shopify-hosted media / products can't be mapped automatically
			case 'repeater':
				$rows = array();
				foreach ( (array) $v as $row ) {
					$rows[] = sf_import_map_values( (array) $row, $f['sub_fields'], $missing );
				}
				$v = $rows;
				break;
		}
		$out[ $f['name'] ] = $v;
	}
	return $out;
}

function sf_import_home() {
	$rows = sf_import_data( 'home-content.json' );
	if ( ! $rows ) {
		return array( 'error', 'assets/data/home-content.json is missing.' );
	}
	$defs = sf_section_defs( false );

	// Front page
	$front = (int) get_option( 'page_on_front' );
	if ( ! $front || 'page' !== get_option( 'show_on_front' ) ) {
		$front = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'Home' ) );
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $front );
	}
	if ( ! function_exists( 'update_field' ) ) {
		return array( 'ok', 'Home page set as front page. Without ACF it shows the sections from assets/data/home-content.json.' );
	}

	$missing = array();
	$value   = array();
	foreach ( $rows as $row ) {
		$layout = $row['acf_fc_layout'];
		if ( empty( $defs[ $layout ] ) ) {
			continue;
		}
		if ( 'see-in-action' === $layout ) {
			// Sample reviewer names / texts from Shopify are placeholders → don't copy them; add real customer videos + reviews.
			unset( $row['blocks_video_card'], $row['blocks_testimonial'] );
		}
		$value[] = array_merge( array( 'acf_fc_layout' => $layout ), sf_import_map_values( $row, $defs[ $layout ]['fields'], $missing ) );
	}
	update_field( 'field_sf_home_sections', $value, $front );

	$msg = sprintf( 'Homepage: %d sections imported.', count( $value ) );
	if ( $missing ) {
		$msg .= ' Categories not found (create them, then re-import or pick them in the section): ' . implode( ', ', array_keys( $missing ) ) . '.';
	}
	return array( $missing ? 'warn' : 'ok', $msg );
}

function sf_import_pages() {
	$data   = sf_import_data( 'pages-content.json' );
	$defs   = sf_section_defs( true );
	$titles = array( 'about' => 'About', 'contact' => 'Contact', 'faq' => 'FAQ', 'track-order' => 'Track Order', 'wishlist' => 'Wishlist' );
	$out    = array();
	foreach ( $titles as $slug => $title ) {
		if ( empty( $data[ $slug ] ) || empty( $defs[ $slug ] ) ) {
			continue;
		}
		$page = get_page_by_path( $slug );
		$id   = $page ? $page->ID : wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => $title, 'post_name' => $slug ) );
		update_post_meta( $id, '_wp_page_template', 'page-templates/template-' . $slug . '.php' );
		if ( ! function_exists( 'update_field' ) ) {
			$out[] = array( 'ok', sprintf( '%s page ready (%s) — texts from the theme files.', $title, get_permalink( $id ) ) );
			continue;
		}
		$missing = array();
		foreach ( sf_import_map_values( $data[ $slug ], $defs[ $slug ]['fields'], $missing ) as $name => $v ) {
			$key = 'field_sf_' . $slug . '_' . $name;
			if ( 0 === strpos( $name, 'blocks_' ) ) {
				$key = 'field_sf_' . $slug . '_' . $name;
			}
			update_field( $key, $v, $id );
		}
		$out[] = array( 'ok', sprintf( '%s page imported (%s).', $title, get_permalink( $id ) ) );
	}
	// "All categories" page (texts come from Sunflorence → Pages, so only the page + template)
	if ( ! get_page_by_path( 'collections' ) ) {
		$id = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'All Categories', 'post_name' => 'collections' ) );
		update_post_meta( $id, '_wp_page_template', 'page-templates/template-list-collections.php' );
		$out[] = array( 'ok', sprintf( 'All Categories page created (%s).', get_permalink( $id ) ) );
	}
	return $out ? $out : array( array( 'warn', 'No page content found.' ) );
}

/**
 * Product categories with the slugs the theme uses (assets/data/categories.json): main categories
 * and their subcategories. Existing categories (same slug) are kept; a missing parent link is fixed.
 */
function sf_import_categories() {
	if ( ! taxonomy_exists( 'product_cat' ) ) {
		return array( 'error', 'WooCommerce is not active.' );
	}
	$tree    = sf_import_data( 'categories.json' );
	$created = 0;
	$kept    = 0;
	$add     = function ( $cat, $parent ) use ( &$created, &$kept ) {
		$t = get_term_by( 'slug', $cat['slug'], 'product_cat' );
		if ( $t ) {
			$kept++;
			if ( (int) $t->parent !== (int) $parent ) {
				wp_update_term( $t->term_id, 'product_cat', array( 'parent' => (int) $parent ) );
			}
			return (int) $t->term_id;
		}
		$r = wp_insert_term( $cat['name'], 'product_cat', array( 'slug' => $cat['slug'], 'parent' => (int) $parent ) );
		if ( is_wp_error( $r ) ) {
			return 0;
		}
		$created++;
		return (int) $r['term_id'];
	};
	foreach ( $tree as $main ) {
		$id = $add( $main, 0 );
		foreach ( $main['children'] ?? array() as $child ) {
			$add( $child, $id );
		}
	}
	return array( 'ok', sprintf( 'Product categories: %d created, %d already there.', $created, $kept ) );
}
