<?php
/**
 * Sunflorence theme bootstrap.
 *
 * Every feature lives in its own file in /inc so this file stays a simple loader.
 *
 * @package Sunflorence
 */

defined( 'ABSPATH' ) || exit;

define( 'SF_VERSION', '1.0.0' );
define( 'SF_DIR', get_template_directory() );
define( 'SF_URI', get_template_directory_uri() );

$sf_includes = array(
	'inc/setup.php',        // theme supports, menus, image sizes
	'inc/helpers.php',      // small template helpers (options, images, icons)
	'inc/enqueue.php',      // CSS / JS loading
	'inc/nav-walker.php',   // sidebar menu with sub-menus (accordion)
	'inc/acf-fields.php',   // homepage sections + global options (ACF)
	'inc/woocommerce.php',  // WooCommerce support, cart count, product queries
	'inc/newsletter.php',   // footer newsletter form handler
	'inc/forms.php',        // contact form + order lookup (Track Order page)
	'inc/importer.php',     // one-click import of the Shopify homepage + page content
);

foreach ( $sf_includes as $file ) {
	require_once SF_DIR . '/' . $file;
}
