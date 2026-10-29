<?php
/**
 * Site header.
 *
 * @package Sunflorence
 */

defined( 'ABSPATH' ) || exit;
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="sf-skip" href="#sf-main"><?php esc_html_e( 'Skip to content', 'sunflorence' ); ?></a>

<?php get_template_part( 'template-parts/header/nav' ); ?>
<?php get_template_part( 'template-parts/header/drawer-menu' ); ?>

<main id="sf-main" class="sf-main">
