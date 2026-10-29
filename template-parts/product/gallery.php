<?php
/**
 * Product gallery — main image + thumbnails below (Shopify snippets/product-gallary.liquid).
 * Images: featured image, gallery images, then variation images (so a selected variation can show its photo).
 *
 * @package Sunflorence
 */

defined( 'ABSPATH' ) || exit;

$product = $args['product'];
$ids     = array_filter( array_merge( array( $product->get_image_id() ), $product->get_gallery_image_ids() ) );
if ( $product->is_type( 'variable' ) ) {
	foreach ( $product->get_children() as $vid ) {
		$v = wc_get_product( $vid );
		if ( $v && $v->get_image_id() ) {
			$ids[] = $v->get_image_id();
		}
	}
}
$ids  = array_values( array_unique( array_map( 'intval', $ids ) ) );
$name = $product->get_name();
?>
<div class="shop-carousel-container page-fade-in" style="animation-delay: 0.1s;">
	<div class="shop-product-carousel product-gallery" data-sf-gallery>
		<div class="shop-main-image-wrapper">
			<div class="shop-image-container shop-square-container">
				<?php if ( ! $ids ) : ?>
					<div class="shop-image-zoom-wrapper active">
						<?php echo wc_placeholder_img( 'woocommerce_single', array( 'class' => 'shop-main-image active' ) ); // phpcs:ignore ?>
					</div>
				<?php endif; ?>
				<?php foreach ( $ids as $i => $id ) : ?>
					<div class="shop-image-zoom-wrapper<?php echo 0 === $i ? ' active' : ''; ?>" data-media-id="<?php echo esc_attr( $id ); ?>">
						<?php
						echo wp_get_attachment_image( $id, 'woocommerce_single', false, array( // phpcs:ignore
							'class'         => 'shop-main-image' . ( 0 === $i ? ' active' : '' ),
							'alt'           => trim( (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) ) ?: $name,
							'loading'       => 0 === $i ? 'eager' : 'lazy',
							'fetchpriority' => 0 === $i ? 'high' : 'auto',
							'data-media-id' => $id,
						) );
						?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<?php if ( count( $ids ) > 1 ) : ?>
			<div class="shop-thumbnails-outer">
				<div class="shop-thumbnails-wrapper shop-match-image-width">
					<div class="shop-thumbnails" id="thumbnail-container">
						<?php foreach ( $ids as $i => $id ) : ?>
							<div class="shop-thumbnail page-fade-in<?php echo 0 === $i ? ' active' : ''; ?>" style="animation-delay: <?php echo esc_attr( 0.3 + $i * 0.1 ); ?>s;" data-index="<?php echo esc_attr( $i ); ?>" data-media-id="<?php echo esc_attr( $id ); ?>" role="button" tabindex="0" aria-label="<?php echo esc_attr( sprintf( __( 'View image %d', 'sunflorence' ), $i + 1 ) ); ?>">
								<div class="thumb-square">
									<?php echo wp_get_attachment_image( $id, 'woocommerce_gallery_thumbnail', false, array( 'loading' => 'lazy', 'alt' => '' ) ); // phpcs:ignore ?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
				<div class="shop-thumbnail-indicators" id="thumbnail-indicators"></div>
			</div>
		<?php endif; ?>
	</div>
</div>
