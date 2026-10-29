<?php
/**
 * Homepage — renders the sections set in Pages → (front page) → "Homepage sections",
 * in the order the client arranges them (WordPress version of Shopify's index.json).
 *
 * Each layout loads template-parts/sections/{layout}.php.
 *
 * @package Sunflorence
 */

defined( 'ABSPATH' ) || exit;

get_header();

$has_sections = function_exists( 'have_rows' ) && have_rows( 'home_sections' );

if ( $has_sections ) {
	$i = 0;
	while ( have_rows( 'home_sections' ) ) {
		the_row();
		$layout = sanitize_key( get_row_layout() );
		if ( locate_template( 'template-parts/sections/' . $layout . '.php' ) ) {
			get_template_part( 'template-parts/sections/' . $layout, null, array( 'index' => $i++ ) );
		}
	}
} else {
	// Sections not set up yet (ACF missing or not imported): show the Shopify homepage from assets/data/home-content.json.
	foreach ( sf_home_data() as $i => $row ) {
		$layout = sanitize_key( $row['acf_fc_layout'] ?? '' );
		if ( $layout && locate_template( 'template-parts/sections/' . $layout . '.php' ) ) {
			$GLOBALS['sf_data_row'] = $row;
			sf_ctx( 'data' );
			get_template_part( 'template-parts/sections/' . $layout, null, array( 'index' => $i ) );
		}
	}
	sf_ctx( 'row' );
}

get_footer();
