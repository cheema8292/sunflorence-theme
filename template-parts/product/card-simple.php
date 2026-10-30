<?php
/**
 * Simple product card (from Shopify snippets/rr-product-card.liquid).
 * Small category label above the name; badge = discount % when on sale, else "New" (product tag).
 *
 * $args: product (WC_Product)
 *
 * @package Sunflorence
 */

defined( 'ABSPATH' ) || exit;

$product = $args['product'] ?? null;
if ( ! $product instanceof WC_Product ) {
	return;
}

// category label (small, above the name) + badge: discount % when on sale, else "New" (product tag "new")
$cat_name = '';
$cats     = get_the_terms( $product->get_id(), 'product_cat' );
if ( $cats && ! is_wp_error( $cats ) ) {
	foreach ( $cats as $c ) {
		if ( (int) $c->term_id !== (int) get_option( 'default_product_cat' ) ) {
			$cat_name = $c->name;
			break;
		}
	}
}
$pct   = sf_sale_percent( $product );
$badge = $pct ? '-' . $pct . '%' : ( has_term( 'new', 'product_tag', $product->get_id() ) ? __( 'New', 'sunflorence' ) : '' );
?>
<a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="rr-prod-card">
	<div class="rr-prod-card__img-wrap">
		<?php if ( $badge ) : ?><span class="rr-prod-badge<?php echo $pct ? '' : ' rr-prod-badge--new'; ?>"><?php echo esc_html( $badge ); ?></span><?php endif; ?>
		<?php echo $product->get_image( 'woocommerce_thumbnail', array( 'loading' => 'lazy', 'alt' => $product->get_name() ) ); // phpcs:ignore ?>
	</div>
	<div class="rr-prod-card__body">
		<?php if ( $cat_name ) : ?><span class="rr-prod-card__cat"><?php echo esc_html( $cat_name ); ?></span><?php endif; ?>
		<h3 class="rr-prod-card__name"><?php echo esc_html( $product->get_name() ); ?></h3>
		<div class="rr-prod-card__price"><?php echo wp_kses_post( $product->get_price_html() ); ?></div>
	</div>
</a>
