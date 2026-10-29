<?php
/**
 * Section: Brand logos — endless scrolling strip, no background or heading (from Shopify rr-brand-logos).
 * A brand without an uploaded logo shows its name as a wordmark.
 * Below the strip: a few products of these brands (client request).
 *
 * @package Sunflorence
 */

defined( 'ABSPATH' ) || exit;

$brands = sf_blocks( 'brand' );
if ( ! $brands ) {
	return;
}
$h     = max( 16, (int) sf_s( 'logo_height', 44 ) );
$gap   = max( 20, (int) sf_s( 'gap', 80 ) );
$speed = max( 10, (int) sf_s( 'speed', 40 ) );
$gray  = (bool) sf_s( 'grayscale', false );
$h_m   = max( 16, (int) sf_s( 'logo_height_mobile', 32 ) );
$pad_t = (int) sf_s( 'padding_top', 40 );
$pad_b = (int) sf_s( 'padding_bottom', 40 );
$pause = (bool) sf_s( 'pause_on_hover', true );
$fade  = (bool) sf_s( 'fade_edges', true );

ob_start();
foreach ( $brands as $b ) {
	$name  = sf_row( $b, 'name', 'Brand' );
	$link  = sf_url( sf_row( $b, 'link' ) );
	$inner = sf_row( $b, 'logo' )
		? sf_img( sf_row( $b, 'logo' ), 'medium', array( 'alt' => $name, 'loading' => 'lazy' ) )
		: '<span class="rr-brands__wordmark">' . esc_html( $name ) . '</span>';
	if ( $link ) {
		echo '<a class="rr-brands__item" href="' . esc_url( $link ) . '" aria-label="' . esc_attr( $name ) . '">' . $inner . '</a>'; // phpcs:ignore
	} else {
		echo '<span class="rr-brands__item">' . $inner . '</span>'; // phpcs:ignore
	}
}
$items = ob_get_clean();

$products = array();
if ( sf_s( 'show_products', true ) && function_exists( 'sf_brand_products' ) ) {
	$names    = array_map( function ( $b ) {
		return (string) sf_row( $b, 'name' );
	}, $brands );
	$products = sf_brand_products( $names, max( 2, min( 8, (int) sf_s( 'products_count', 4 ) ) ) );
}
?>
<section class="rr-brands<?php echo $gray ? ' rr-brands--gray' : ''; echo $pause ? '' : ' rr-brands--nopause'; echo $fade ? '' : ' rr-brands--nofade'; ?>" aria-label="<?php echo esc_attr( sf_s( 'aria_label', __( 'Brands we carry', 'sunflorence' ) ) ); ?>"
	style="--rr-brand-h:<?php echo esc_attr( $h ); ?>px; --rr-brand-gap:<?php echo esc_attr( $gap ); ?>px; --rr-brand-speed:<?php echo esc_attr( $speed ); ?>s; --rr-brand-h-m:<?php echo esc_attr( $h_m ); ?>px; padding:<?php echo esc_attr( $pad_t ); ?>px 0 <?php echo esc_attr( $pad_b ); ?>px;">
	<div class="rr-brands__viewport">
		<div class="rr-brands__track">
			<div class="rr-brands__set"><?php echo $items; // phpcs:ignore ?></div>
			<div class="rr-brands__set" aria-hidden="true"><?php echo str_replace( '<a ', '<a tabindex="-1" ', $items ); // phpcs:ignore ?></div>
		</div>
	</div>
	<?php if ( $products ) : ?>
		<div class="rr-brands__products">
			<?php if ( sf_s( 'products_heading', 'Shop By Brand' ) ) : ?>
				<h2 class="rr-brands__products-heading"><?php echo esc_html( sf_s( 'products_heading', 'Shop By Brand' ) ); ?></h2>
			<?php endif; ?>
			<div class="rr-brands__grid" style="--rr-brand-cols:<?php echo esc_attr( count( $products ) ); ?>">
				<?php foreach ( $products as $p ) : ?>
					<?php get_template_part( 'template-parts/product/card-simple', null, array( 'product' => $p ) ); ?>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endif; ?>
</section>
