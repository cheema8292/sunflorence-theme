<?php
/**
 * Product card (from Shopify rr-cs-card).
 *
 * $args: product (WC_Product|null), index (int).
 * Simple, in-stock products get a one-click AJAX "+" (WooCommerce ajax_add_to_cart);
 * variable products link to the product page to choose options.
 *
 * @package Sunflorence
 */

defined( 'ABSPATH' ) || exit;

$product = $args['product'] ?? null;
$i       = (int) ( $args['index'] ?? 1 );
$num     = str_pad( (string) $i, 2, '0', STR_PAD_LEFT );

if ( ! $product instanceof WC_Product ) : ?>
	<div class="rr-cs-card rr-cs-card--placeholder">
		<span class="rr-cs-card__media"><span class="rr-cs-card__num"><?php echo esc_html( $num ); ?></span></span>
		<div class="rr-cs-card__body">
			<p class="rr-cs-card__vendor"><?php esc_html_e( 'Brand', 'sunflorence' ); ?></p>
			<h3 class="rr-cs-card__title"><?php esc_html_e( 'Choose a category', 'sunflorence' ); ?></h3>
		</div>
	</div>
	<?php
	return;
endif;

$url     = $product->get_permalink();
$title   = $product->get_name();
$img1    = $product->get_image_id();
$gallery = $product->get_gallery_image_ids();
$img2    = $gallery ? $gallery[0] : 0;
$off     = sf_sale_percent( $product );
$brand   = sf_product_brand( $product );
$in      = $product->is_in_stock();
?>
<div class="rr-cs-card">
	<a href="<?php echo esc_url( $url ); ?>" class="rr-cs-card__media<?php echo $img2 ? ' has-2' : ''; ?>" tabindex="-1" aria-hidden="true">
		<?php if ( ! $in ) : ?>
			<span class="rr-cs-card__badge rr-cs-card__badge--out"><?php esc_html_e( 'Sold out', 'sunflorence' ); ?></span>
		<?php elseif ( $off ) : ?>
			<span class="rr-cs-card__badge">-<?php echo esc_html( $off ); ?>%</span>
		<?php elseif ( has_term( 'new', 'product_tag', $product->get_id() ) ) : ?>
			<span class="rr-cs-card__badge rr-cs-card__badge--soft"><?php esc_html_e( 'New', 'sunflorence' ); ?></span>
		<?php endif; ?>
		<span class="rr-cs-card__num"><?php echo esc_html( $num ); ?></span>
		<?php
		if ( $img1 ) {
			echo wp_get_attachment_image( $img1, 'woocommerce_thumbnail', false, array( 'class' => 'rr-cs-card__img-1', 'loading' => 'lazy', 'alt' => $title, 'sizes' => '(max-width: 768px) 50vw, 25vw' ) ); // phpcs:ignore
			if ( $img2 ) {
				echo wp_get_attachment_image( $img2, 'woocommerce_thumbnail', false, array( 'class' => 'rr-cs-card__img-2', 'loading' => 'lazy', 'alt' => '', 'sizes' => '(max-width: 768px) 50vw, 25vw' ) ); // phpcs:ignore
			}
		} elseif ( function_exists( 'wc_placeholder_img' ) ) {
			echo wc_placeholder_img( 'woocommerce_thumbnail', array( 'class' => 'rr-cs-card__img-1' ) ); // phpcs:ignore
		}
		?>
	</a>
	<div class="rr-cs-card__body">
		<?php if ( $brand ) : ?><p class="rr-cs-card__vendor"><?php echo esc_html( $brand ); ?></p><?php endif; ?>
		<h3 class="rr-cs-card__title"><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $title ); ?></a></h3>
		<div class="rr-cs-card__foot">
			<div class="rr-cs-card__price"><?php echo wp_kses_post( $product->get_price_html() ); ?></div>
			<?php if ( ! $in ) : ?>
				<button type="button" class="rr-cs-card__atc" disabled aria-label="<?php esc_attr_e( 'Sold out', 'sunflorence' ); ?>"><?php echo sf_icon( 'close' ); // phpcs:ignore ?></button>
			<?php elseif ( $product->is_type( 'simple' ) && $product->is_purchasable() ) : ?>
				<a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" data-quantity="1" data-product_id="<?php echo esc_attr( $product->get_id() ); ?>" data-product_sku="<?php echo esc_attr( $product->get_sku() ); ?>"
					class="rr-cs-card__atc add_to_cart_button ajax_add_to_cart" rel="nofollow"
					aria-label="<?php echo esc_attr( sprintf( __( 'Add %s to cart', 'sunflorence' ), $title ) ); ?>"><?php echo sf_icon( 'plus' ); // phpcs:ignore ?></a>
			<?php else : ?>
				<a href="<?php echo esc_url( $url ); ?>" class="rr-cs-card__atc" aria-label="<?php echo esc_attr( sprintf( __( 'Choose options for %s', 'sunflorence' ), $title ) ); ?>"><?php echo sf_icon( 'arrow' ); // phpcs:ignore ?></a>
			<?php endif; ?>
		</div>
	</div>
</div>
