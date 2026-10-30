<?php
/**
 * Section: Sets & Save — bundle cards (from Shopify rr-bundles + snippets/rr-bundle-card).
 * Ratings only show when filled in for a bundle (use real review data).
 *
 * @package Sunflorence
 */

defined( 'ABSPATH' ) || exit;

$bundles = sf_blocks( 'bundle' );
if ( ! $bundles ) {
	return;
}
$star  = '<svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
$style = sf_vars( array( 'heading_color', 'btn_bg', 'btn_color', 'columns', 'bg_color', 'padding_vertical', 'padding_horizontal' ) );
?>
<section class="rr-bundles" style="<?php echo esc_attr( $style ); ?>">
	<div class="rr-bundles__inner">
		<div class="rr-bundles__header">
			<div class="rr-bundles__head-left">
				<?php if ( sf_s( 'heading' ) ) : ?><h2 class="rr-bundles__heading"><?php echo esc_html( sf_s( 'heading' ) ); ?></h2><?php endif; ?>
				<?php if ( sf_s( 'subtext' ) ) : ?><p class="rr-bundles__subtext"><?php echo esc_html( sf_s( 'subtext' ) ); ?></p><?php endif; ?>
			</div>
			<?php if ( sf_s( 'btn_text' ) ) : ?>
				<a href="<?php echo esc_url( sf_s( 'btn_url', '#' ) ); ?>" class="rr-bundles__shop-all"><?php echo esc_html( sf_s( 'btn_text' ) ); ?></a>
			<?php endif; ?>
		</div>

		<div class="rr-bundles__grid">
			<?php foreach ( $bundles as $b ) : ?>
				<a href="<?php echo esc_url( sf_row( $b, 'link', '#' ) ); ?>" class="rr-bcard">
					<?php if ( sf_row( $b, 'badge_text' ) ) : ?>
						<span class="rr-bcard__badge" style="background:<?php echo esc_attr( sf_color( sf_row( $b, 'badge_bg' ), '#2b3f55' ) ); ?>;color:<?php echo esc_attr( sf_color( sf_row( $b, 'badge_color' ), '#FFFFFF' ) ); ?>"><?php echo esc_html( sf_row( $b, 'badge_text' ) ); ?></span>
					<?php endif; ?>
					<div class="rr-bcard__img-wrap"><?php echo sf_img( sf_row( $b, 'image' ), 'woocommerce_thumbnail', array( 'loading' => 'lazy', 'alt' => sf_row( $b, 'title' ) ) ); // phpcs:ignore ?></div>

					<?php if ( sf_row( $b, 'rating' ) && (float) sf_row( $b, 'rating' ) > 0 ) : ?>
						<?php $filled = (int) round( (float) sf_row( $b, 'rating' ) ); ?>
						<div class="rr-bcard__rating">
							<span class="rr-bcard__rating-stars">
								<?php for ( $i = 1; $i <= 5; $i++ ) { echo $i <= $filled ? $star : str_replace( '<svg ', '<svg class="rr-bcard__star--empty" ', $star ); } // phpcs:ignore ?>
							</span>
							<span class="rr-bcard__rating-text"><?php echo esc_html( sf_row( $b, 'rating' ) ); ?><?php echo sf_row( $b, 'review_count' ) ? esc_html( ' (' . sf_row( $b, 'review_count' ) . ' reviews)' ) : ''; ?></span>
						</div>
					<?php endif; ?>

					<?php if ( sf_row( $b, 'title' ) ) : ?><h3 class="rr-bcard__title"><?php echo esc_html( sf_row( $b, 'title' ) ); ?></h3><?php endif; ?>
					<div class="rr-bcard__price-row">
						<?php if ( sf_row( $b, 'price_original' ) ) : ?><span class="rr-bcard__price-orig"><?php echo esc_html( sf_row( $b, 'price_original' ) ); ?></span><?php endif; ?>
						<?php if ( sf_row( $b, 'price_sale' ) ) : ?><span class="rr-bcard__price-sale"><?php echo esc_html( sf_row( $b, 'price_sale' ) ); ?></span><?php endif; ?>
						<?php if ( sf_row( $b, 'save_amount' ) ) : ?><span class="rr-bcard__price-save"><?php echo esc_html( 'SAVE ' . sf_row( $b, 'save_amount' ) ); ?></span><?php endif; ?>
					</div>
					<?php $items = array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) sf_row( $b, 'items' ) ) ) ); ?>
					<?php if ( $items ) : ?>
						<ul class="rr-bcard__items">
							<?php foreach ( $items as $it ) : ?>
								<li class="rr-bcard__item"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg><span><?php echo esc_html( $it ); ?></span></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
