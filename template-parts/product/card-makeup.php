<?php
/**
 * Makeup product card (from Shopify snippets/rr-makeup-card-grid.liquid).
 * Shade count comes from a "Shade" / "Color" / "Colour" attribute; swatch dots appear
 * when the attribute terms have a colour saved in term meta "color" (e.g. by a swatches plugin).
 *
 * $args: product (WC_Product|null), index (int)
 *
 * @package Sunflorence
 */

defined( 'ABSPATH' ) || exit;

$product = $args['product'] ?? null;

if ( ! $product instanceof WC_Product ) : ?>
	<div class="rr-mk-card">
		<span class="rr-mk-card__media"><?php echo function_exists( 'wc_placeholder_img' ) ? wc_placeholder_img( 'sf-card' ) : ''; // phpcs:ignore ?></span>
		<div class="rr-mk-card__body">
			<p class="rr-mk-card__vendor"><?php esc_html_e( 'Brand', 'sunflorence' ); ?></p>
			<h3 class="rr-mk-card__title"><?php esc_html_e( 'Makeup product', 'sunflorence' ); ?></h3>
			<span class="rr-mk-card__atc"><?php esc_html_e( 'Choose a category', 'sunflorence' ); ?></span>
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
$brand   = sf_product_brand( $product );
$off     = sf_sale_percent( $product );

// Shades
$shade_terms = array();
$shade_label = '';
foreach ( $product->get_attributes() as $attr ) {
	$name = strtolower( wc_attribute_label( $attr->get_name() ) );
	if ( preg_match( '/shade|colou?r/', $name ) ) {
		$shade_label = $name;
		$shade_terms = $attr->is_taxonomy() ? wc_get_product_terms( $product->get_id(), $attr->get_name() ) : $attr->get_options();
		break;
	}
}
?>
<div class="rr-mk-card">
	<a href="<?php echo esc_url( $url ); ?>" class="rr-mk-card__media<?php echo $img2 ? ' rr-mk-card__media--has-2' : ''; ?>" tabindex="-1" aria-hidden="true">
		<?php if ( ! $product->is_in_stock() ) : ?>
			<span class="rr-mk-card__badge rr-mk-card__badge--out"><?php esc_html_e( 'Sold out', 'sunflorence' ); ?></span>
		<?php elseif ( $off ) : ?>
			<span class="rr-mk-card__badge">-<?php echo esc_html( $off ); ?>%</span>
		<?php elseif ( has_term( 'new', 'product_tag', $product->get_id() ) ) : ?>
			<span class="rr-mk-card__badge rr-mk-card__badge--new"><?php esc_html_e( 'New', 'sunflorence' ); ?></span>
		<?php elseif ( has_term( 'bestseller', 'product_tag', $product->get_id() ) ) : ?>
			<span class="rr-mk-card__badge rr-mk-card__badge--new"><?php esc_html_e( 'Bestseller', 'sunflorence' ); ?></span>
		<?php endif; ?>
		<?php
		if ( $img1 ) {
			echo wp_get_attachment_image( $img1, 'sf-card', false, array( 'class' => 'rr-mk-card__img-1', 'loading' => 'lazy', 'alt' => $title, 'sizes' => '(max-width: 768px) 50vw, 22vw' ) ); // phpcs:ignore
			if ( $img2 ) {
				echo wp_get_attachment_image( $img2, 'sf-card', false, array( 'class' => 'rr-mk-card__img-2', 'loading' => 'lazy', 'alt' => '', 'sizes' => '(max-width: 768px) 50vw, 22vw' ) ); // phpcs:ignore
			}
		} elseif ( function_exists( 'wc_placeholder_img' ) ) {
			echo wc_placeholder_img( 'sf-card' ); // phpcs:ignore
		}
		?>
	</a>

	<div class="rr-mk-card__body">
		<?php if ( $brand ) : ?><p class="rr-mk-card__vendor"><?php echo esc_html( $brand ); ?></p><?php endif; ?>
		<h3 class="rr-mk-card__title"><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $title ); ?></a></h3>

		<?php if ( count( $shade_terms ) > 1 ) : ?>
			<div class="rr-mk-card__shades">
				<?php
				foreach ( array_slice( $shade_terms, 0, 5 ) as $t ) {
					$hex = ( $t instanceof WP_Term ) ? sanitize_hex_color( (string) get_term_meta( $t->term_id, 'color', true ) ) : '';
					if ( $hex ) {
						echo '<i style="background:' . esc_attr( $hex ) . '"></i>';
					}
				}
				?>
				<span><?php echo esc_html( sprintf( _n( '%d shade', '%d shades', count( $shade_terms ), 'sunflorence' ), count( $shade_terms ) ) ); ?></span>
			</div>
		<?php endif; ?>

		<div class="rr-mk-card__price"><?php echo wp_kses_post( $product->get_price_html() ); ?></div>

		<?php if ( ! $product->is_in_stock() ) : ?>
			<button type="button" class="rr-mk-card__atc" disabled><?php esc_html_e( 'Sold out', 'sunflorence' ); ?></button>
		<?php elseif ( $product->is_type( 'simple' ) && $product->is_purchasable() ) : ?>
			<a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" data-quantity="1" data-product_id="<?php echo esc_attr( $product->get_id() ); ?>"
				class="rr-mk-card__atc add_to_cart_button ajax_add_to_cart" rel="nofollow"><?php esc_html_e( 'Add to bag', 'sunflorence' ); ?></a>
		<?php else : ?>
			<a href="<?php echo esc_url( $url ); ?>" class="rr-mk-card__atc"><?php echo $shade_label ? esc_html__( 'Choose shade', 'sunflorence' ) : esc_html__( 'Choose options', 'sunflorence' ); ?></a>
		<?php endif; ?>
	</div>
</div>
