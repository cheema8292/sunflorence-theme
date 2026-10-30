<?php
/**
 * Product card (featured products, You May Also Like, brand strip, shop / category pages).
 * Photo (2nd photo on hover) · discount / sold-out badge · quick "Add to cart" · category · title · rating · price.
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

$link     = $product->get_permalink();
$name     = $product->get_name();
$variable = $product->is_type( 'variable' );

// category label: the product's sub-category if it has one, else its main category
$cat_name = '';
$cats     = get_the_terms( $product->get_id(), 'product_cat' );
if ( $cats && ! is_wp_error( $cats ) ) {
	usort( $cats, function ( $a, $b ) {
		return (int) ( $b->parent > 0 ) - (int) ( $a->parent > 0 ); // children first
	} );
	foreach ( $cats as $c ) {
		if ( (int) $c->term_id !== (int) get_option( 'default_product_cat' ) ) {
			$cat_name = $c->name;
			break;
		}
	}
}

// prices — variable products pushed by DSers sometimes have no stored price: read it from the variations
if ( $variable ) {
	$min = $product->get_variation_price( 'min', true );
	$max = $product->get_variation_price( 'max', true );
	$reg = $product->get_variation_regular_price( 'min', true );
	if ( '' === $product->get_price() && '' !== $min ) {
		WC_Product_Variable::sync( $product->get_id() ); // repair once, so sorting / filtering by price works too
	}
} else {
	$min = wc_get_price_to_display( $product );
	$max = $min;
	$reg = wc_get_price_to_display( $product, array( 'price' => $product->get_regular_price() ) );
}
$min     = '' === $min ? 0 : (float) $min;
$max     = '' === $max ? 0 : (float) $max;
$reg     = '' === $reg ? 0 : (float) $reg;
$on_sale = $min > 0 && $reg > $min;
$pct     = $on_sale ? (int) round( ( $reg - $min ) / $reg * 100 ) : 0;
$in      = $product->is_in_stock();

// images: main + first gallery photo (shown on hover)
$img_id  = $product->get_image_id();
$alt_ids = $product->get_gallery_image_ids();
$alt_id  = $alt_ids ? (int) $alt_ids[0] : 0;

$rating = (float) $product->get_average_rating();
$count  = (int) $product->get_review_count();
?>
<article class="rr-prod-card sf-pcard<?php echo $in ? '' : ' is-soldout'; ?>">
	<div class="sf-pcard__media">
		<a class="sf-pcard__img-link" href="<?php echo esc_url( $link ); ?>" tabindex="-1" aria-hidden="true">
			<?php
			echo $img_id // phpcs:ignore
				? wp_get_attachment_image( $img_id, 'woocommerce_thumbnail', false, array( 'class' => 'sf-pcard__img', 'loading' => 'lazy', 'alt' => $name, 'sizes' => '(max-width: 640px) 48vw, 24vw' ) )
				: wc_placeholder_img( 'woocommerce_thumbnail', array( 'class' => 'sf-pcard__img' ) );
			if ( $alt_id ) {
				echo wp_get_attachment_image( $alt_id, 'woocommerce_thumbnail', false, array( 'class' => 'sf-pcard__img sf-pcard__img--alt', 'loading' => 'lazy', 'alt' => '', 'sizes' => '(max-width: 640px) 48vw, 24vw' ) ); // phpcs:ignore
			}
			?>
		</a>
		<?php if ( ! $in ) : ?>
			<span class="sf-pcard__badge sf-pcard__badge--soldout"><?php esc_html_e( 'Sold out', 'sunflorence' ); ?></span>
		<?php elseif ( $pct ) : ?>
			<span class="sf-pcard__badge">-<?php echo esc_html( $pct ); ?>%</span>
		<?php elseif ( has_term( 'new', 'product_tag', $product->get_id() ) ) : ?>
			<span class="sf-pcard__badge sf-pcard__badge--new"><?php esc_html_e( 'New', 'sunflorence' ); ?></span>
		<?php endif; ?>

		<?php if ( $in ) : ?>
			<?php if ( $product->is_type( 'simple' ) && $product->is_purchasable() ) : ?>
				<a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" data-quantity="1" data-product_id="<?php echo esc_attr( $product->get_id() ); ?>" data-product_sku="<?php echo esc_attr( $product->get_sku() ); ?>"
					class="sf-pcard__atc add_to_cart_button ajax_add_to_cart" rel="nofollow" aria-label="<?php echo esc_attr( sprintf( __( 'Add %s to your cart', 'sunflorence' ), $name ) ); ?>">
					<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><path d="M3 6h18M16 10a4 4 0 01-8 0"/></svg>
					<span><?php esc_html_e( 'Add to cart', 'sunflorence' ); ?></span>
				</a>
			<?php else : ?>
				<a href="<?php echo esc_url( $link ); ?>" class="sf-pcard__atc" aria-label="<?php echo esc_attr( sprintf( __( 'Choose options for %s', 'sunflorence' ), $name ) ); ?>">
					<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6h16M4 12h10M4 18h6"/></svg>
					<span><?php esc_html_e( 'Choose options', 'sunflorence' ); ?></span>
				</a>
			<?php endif; ?>
		<?php endif; ?>
	</div>

	<div class="sf-pcard__body">
		<?php if ( $cat_name ) : ?><span class="sf-pcard__cat"><?php echo esc_html( $cat_name ); ?></span><?php endif; ?>
		<h3 class="sf-pcard__title"><a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $name ); ?></a></h3>
		<?php if ( $count ) : ?>
			<div class="sf-pcard__rating" aria-label="<?php echo esc_attr( sprintf( __( 'Rated %1$s out of 5 (%2$d reviews)', 'sunflorence' ), number_format_i18n( $rating, 1 ), $count ) ); ?>">
				<span class="sf-pcard__stars" style="--sf-r: <?php echo esc_attr( $rating / 5 * 100 ); ?>%"></span>
				<span class="sf-pcard__count">(<?php echo esc_html( $count ); ?>)</span>
			</div>
		<?php endif; ?>
		<?php if ( $min > 0 ) : ?>
			<div class="sf-pcard__price">
				<?php if ( $variable && $max > $min ) : ?><span class="sf-pcard__from"><?php esc_html_e( 'From', 'sunflorence' ); ?></span><?php endif; ?>
				<span class="sf-pcard__now<?php echo $on_sale ? ' is-sale' : ''; ?>"><?php echo wc_price( $min ); // phpcs:ignore ?></span>
				<?php if ( $on_sale ) : ?><del class="sf-pcard__was"><?php echo wc_price( $reg ); // phpcs:ignore ?></del><?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</article>
