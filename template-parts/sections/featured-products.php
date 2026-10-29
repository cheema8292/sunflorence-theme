<?php
/**
 * Section: Featured products — "Deals of the Day" slider (from Shopify rr-products).
 * Category empty = newest products from the whole shop.
 *
 * Also used on the product page as "You May Also Like" ($args: heading, products).
 *
 * @package Sunflorence
 */

defined( 'ABSPATH' ) || exit;

if ( isset( $args['products'] ) ) {
	$products  = $args['products'];
	$heading   = $args['heading'] ?? __( 'You May Also Like', 'sunflorence' );
	$link_text = $args['link_text'] ?? '';
	$link_url  = $args['link_url'] ?? '';
	$style     = '';
} else {
	$count     = max( 1, (int) sf_s( 'products_count', 8 ) );
	$term      = sf_term( sf_s( 'collection' ) );
	$products  = $term ? sf_products_in_cat( $term, $count ) : ( function_exists( 'wc_get_products' ) ? wc_get_products( array( 'status' => 'publish', 'visibility' => 'catalog', 'limit' => $count, 'orderby' => 'date', 'order' => 'DESC' ) ) : array() );
	$heading   = sf_s( 'heading', 'Our Most Loved Products' );
	$link_text = sf_s( 'link_text', 'To All Products' );
	$link_url  = sf_s( 'link_url', $term ? sf_cat_link( $term ) : sf_cat_link( null ) );
	$style     = sf_vars( array( 'bg_color', 'padding_vertical', 'padding_horizontal' ) );
}
if ( ! $products ) {
	return;
}
?>
<section class="rr-products" style="<?php echo esc_attr( $style ); ?>">
	<div class="rr-products__inner">
		<div class="rr-products__header">
			<h2 class="rr-products__heading"><?php echo esc_html( $heading ); ?></h2>
			<?php if ( $link_text ) : ?>
				<a href="<?php echo esc_url( $link_url ); ?>" class="rr-products__view-all"><?php echo esc_html( $link_text ); ?> &rsaquo;</a>
			<?php endif; ?>
		</div>

		<div class="rr-products__slider">
			<?php foreach ( $products as $p ) : ?>
				<?php get_template_part( 'template-parts/product/card-simple', null, array( 'product' => $p ) ); ?>
			<?php endforeach; ?>
		</div>

		<div class="rr-products__footer">
			<div class="rr-products__progress-wrap"><div class="rr-products__progress-fill"></div></div>
			<div class="rr-products__nav">
				<button class="rr-products__nav-btn" data-dir="-1" aria-label="<?php esc_attr_e( 'Previous', 'sunflorence' ); ?>"><svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg></button>
				<button class="rr-products__nav-btn" data-dir="1" aria-label="<?php esc_attr_e( 'Next', 'sunflorence' ); ?>"><svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></button>
			</div>
		</div>
	</div>
</section>
