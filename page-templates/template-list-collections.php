<?php
/**
 * Template Name: All Categories
 *
 * Shopify sections/rr-list-collections.liquid — every product category as an image card.
 * Card image = the category thumbnail (Products → Categories). Texts: Sunflorence → Pages → All categories page.
 *
 * @package Sunflorence
 */

defined( 'ABSPATH' ) || exit;

get_header();
sf_ctx( 'opt:list-collections' );

$cats = get_terms( array(
	'taxonomy'   => 'product_cat',
	'hide_empty' => true,
	'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
	'orderby'    => 'name',
) );
$cats = is_wp_error( $cats ) ? array() : $cats;
$shop = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
?>
<div class="rr-cols">
	<div class="rr-cols__hero">
		<div class="rr-cols__eyebrow"><?php echo esc_html( sf_s( 'eyebrow' ) ); ?></div>
		<h1 class="rr-cols__title">
			<?php echo esc_html( sf_s( 'heading' ) ); ?><?php if ( sf_s( 'heading_italic' ) ) : ?> <em><?php echo esc_html( sf_s( 'heading_italic' ) ); ?></em><?php endif; ?>
		</h1>
		<?php if ( sf_s( 'subtext' ) ) : ?><p class="rr-cols__sub"><?php echo esc_html( sf_s( 'subtext' ) ); ?></p><?php endif; ?>
	</div>

	<div class="rr-cols__body">
		<div class="rr-cols__body-inner">
			<div class="rr-cols__grid">
				<?php foreach ( $cats as $i => $c ) :
					$thumb = (int) get_term_meta( $c->term_id, 'thumbnail_id', true );
					$feat  = 0 === $i && sf_s( 'show_featured' );
					?>
					<a href="<?php echo esc_url( get_term_link( $c ) ); ?>" class="rr-cols__card<?php echo $feat ? ' rr-cols__card--featured' : ''; ?>">
						<?php if ( $thumb ) : ?>
							<div class="rr-cols__card-img"><?php echo wp_get_attachment_image( $thumb, 'large', false, array( 'loading' => 'lazy', 'alt' => $c->name ) ); ?></div>
						<?php else : ?>
							<div class="rr-cols__card-placeholder"></div>
						<?php endif; ?>
						<div class="rr-cols__card-overlay"></div>
						<div class="rr-cols__card-content">
							<span class="rr-cols__card-tag"><?php esc_html_e( 'Collection', 'sunflorence' ); ?></span>
							<div class="rr-cols__card-name"><?php echo esc_html( $c->name ); ?></div>
							<div class="rr-cols__card-count"><?php echo esc_html( sprintf( _n( '%s Product', '%s Products', $c->count, 'sunflorence' ), number_format_i18n( $c->count ) ) ); ?></div>
						</div>
						<div class="rr-cols__card-arrow"><svg viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg></div>
					</a>
				<?php endforeach; ?>
			</div>
			<?php if ( sf_s( 'show_shop_all' ) ) : ?>
				<div class="rr-cols__view-all">
					<a href="<?php echo esc_url( $shop ); ?>" class="rr-cols__view-all-btn">
						<span><?php esc_html_e( 'Shop All Products', 'sunflorence' ); ?></span>
						<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="position:relative;z-index:1"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
					</a>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
<?php
sf_ctx( 'row' );
get_footer();
