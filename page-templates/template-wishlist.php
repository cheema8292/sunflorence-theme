<?php
/**
 * Template Name: Wishlist
 *
 * Shopify sections/rr-wishlist.liquid — saved products are kept in the browser (localStorage "sf_wishlist"),
 * added with the "Save to wishlist" button on product pages (assets/js/global/wishlist.js).
 *
 * @package Sunflorence
 */

defined( 'ABSPATH' ) || exit;

get_header();
sf_ctx( 'post:wishlist' );
$shop = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
?>
<div class="rr-wishlist">
	<div class="rr-wishlist__hero">
		<div class="rr-wishlist__eyebrow"><?php echo esc_html( sf_s( 'eyebrow' ) ); ?></div>
		<h1 class="rr-wishlist__title"><?php echo esc_html( sf_s( 'heading' ) ); ?></h1>
		<p class="rr-wishlist__sub"><?php echo esc_html( sf_s( 'subtext' ) ); ?></p>
		<div class="rr-wishlist__count-badge" id="rr-wl-count-badge" data-one="<?php esc_attr_e( 'saved item', 'sunflorence' ); ?>" data-many="<?php esc_attr_e( 'saved items', 'sunflorence' ); ?>">0 <?php esc_html_e( 'saved items', 'sunflorence' ); ?></div>
	</div>
	<div class="rr-wishlist__body">
		<div class="rr-wishlist__body-inner">
			<div class="rr-wishlist__grid" id="rr-wl-grid" data-view="<?php esc_attr_e( 'View Product', 'sunflorence' ); ?>" data-remove="<?php esc_attr_e( 'Remove', 'sunflorence' ); ?>"></div>
			<div class="rr-wishlist__empty" id="rr-wl-empty" style="display:none">
				<div class="rr-wishlist__empty-icon"><svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg></div>
				<div class="rr-wishlist__empty-title"><?php esc_html_e( 'Your wishlist is empty', 'sunflorence' ); ?></div>
				<p class="rr-wishlist__empty-sub"><?php esc_html_e( 'Save your favourite products by clicking the heart icon on any product page.', 'sunflorence' ); ?></p>
				<a href="<?php echo esc_url( $shop ); ?>" class="rr-wishlist__empty-btn"><?php esc_html_e( 'Explore Products', 'sunflorence' ); ?></a>
			</div>
		</div>
	</div>
</div>
<?php
sf_ctx( 'row' );
get_footer();
