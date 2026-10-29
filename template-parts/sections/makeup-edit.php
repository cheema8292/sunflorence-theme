<?php
/**
 * Section: Makeup edit — promo panel + category tabs + product grid (from Shopify rr-makeup-edit).
 *
 * @package Sunflorence
 */

defined( 'ABSPATH' ) || exit;

$uid        = 'rr-mk-' . (int) ( $args['index'] ?? 0 );
$show_promo = (bool) sf_s( 'show_promo', true );
$limit      = max( 2, (int) sf_s( 'products_count', 6 ) );
$main       = sf_term( sf_s( 'collection' ) );
$view_url   = sf_s( 'view_all_url', $main ? sf_cat_link( $main ) : sf_cat_link( null ) );

$tabs = array();
foreach ( sf_blocks( 'tab' ) as $row ) {
	$t = sf_term( sf_row( $row, 'collection' ) );
	if ( $t ) {
		$tabs[] = array( 'term' => $t, 'label' => sf_row( $row, 'label', $t->name ) );
	}
}

$style  = sf_vars( array( 'bg_color', 'blush_color', 'rose_color', 'berry_color', 'plum_color', 'gold_color', 'padding_vertical', 'padding_horizontal', 'columns' ) );
$style .= '--s-columns-plus:' . ( (int) sf_s( 'columns', 3 ) + 1 ) . ';';

$grid = function ( $term ) use ( $limit ) {
	$products = $term ? sf_products_in_cat( $term, $limit ) : array();
	if ( $term && ! $products ) {
		echo '<div class="rr-mk__empty">' . esc_html__( 'This category has no products yet.', 'sunflorence' ) . '</div>';
		return;
	}
	echo '<div class="rr-mk__grid">';
	if ( $products ) {
		foreach ( $products as $n => $p ) {
			get_template_part( 'template-parts/product/card-makeup', null, array( 'product' => $p, 'index' => $n + 1 ) );
		}
	} else {
		for ( $i = 1; $i <= $limit; $i++ ) {
			get_template_part( 'template-parts/product/card-makeup', null, array( 'product' => null, 'index' => $i ) );
		}
	}
	echo '</div>';
};
?>
<section id="<?php echo esc_attr( $uid ); ?>" class="rr-mk<?php echo $show_promo ? '' : ' rr-mk--no-promo'; ?>" style="<?php echo esc_attr( $style ); ?>">
	<div class="rr-mk__inner">

		<div class="rr-mk__head">
			<?php if ( sf_s( 'eyebrow' ) ) : ?><div class="rr-mk__eyebrow"><?php echo esc_html( sf_s( 'eyebrow' ) ); ?></div><?php endif; ?>
			<?php if ( sf_s( 'heading' ) ) : ?><h2 class="rr-mk__heading"><?php echo sf_inline( sf_s( 'heading' ) ); // phpcs:ignore ?></h2><?php endif; ?>
			<?php if ( sf_s( 'subheading' ) ) : ?><p class="rr-mk__sub"><?php echo esc_html( sf_s( 'subheading' ) ); ?></p><?php endif; ?>
			<?php if ( sf_s( 'show_swatches', true ) ) : ?>
				<div class="rr-mk__swatches" aria-hidden="true">
					<?php
					foreach ( array( 'swatch_1', 'swatch_2', 'swatch_3', 'swatch_4', 'swatch_5', 'swatch_6' ) as $k ) {
						$c = sanitize_hex_color( (string) sf_s( $k ) );
						if ( $c ) {
							echo '<span class="rr-mk__swatch" style="background:' . esc_attr( $c ) . '"></span>';
						}
					}
					?>
				</div>
			<?php endif; ?>
		</div>

		<div class="rr-mk__layout">
			<?php if ( $show_promo ) : ?>
				<a class="rr-mk__promo" href="<?php echo esc_url( sf_s( 'promo_url', $view_url ) ); ?>">
					<?php echo sf_img( sf_s( 'promo_image' ), 'large', array( 'loading' => 'lazy', 'alt' => sf_s( 'promo_title' ), 'sizes' => '(max-width: 1100px) 100vw, 33vw' ) ); // phpcs:ignore ?>
					<span class="rr-mk__promo-deco" aria-hidden="true"></span>
					<div class="rr-mk__promo-body">
						<?php if ( sf_s( 'promo_tag' ) ) : ?><span class="rr-mk__promo-tag"><?php echo esc_html( sf_s( 'promo_tag' ) ); ?></span><?php endif; ?>
						<?php if ( sf_s( 'promo_title' ) ) : ?><h3 class="rr-mk__promo-title"><?php echo esc_html( sf_s( 'promo_title' ) ); ?></h3><?php endif; ?>
						<?php if ( sf_s( 'promo_text' ) ) : ?><p class="rr-mk__promo-text"><?php echo esc_html( sf_s( 'promo_text' ) ); ?></p><?php endif; ?>
						<?php if ( sf_s( 'promo_button' ) ) : ?><span class="rr-mk__btn"><?php echo esc_html( sf_s( 'promo_button' ) ); ?> <span aria-hidden="true">&rarr;</span></span><?php endif; ?>
					</div>
				</a>
			<?php endif; ?>

			<div class="rr-mk__main">
				<div class="rr-mk__bar">
					<?php if ( count( $tabs ) > 1 ) : ?>
						<div class="rr-mk__tabs" role="tablist">
							<?php foreach ( $tabs as $n => $t ) : ?>
								<button type="button" role="tab" class="rr-mk__tab<?php echo 0 === $n ? ' is-active' : ''; ?>"
									id="<?php echo esc_attr( $uid . '-tab-' . $n ); ?>" aria-controls="<?php echo esc_attr( $uid . '-panel-' . $n ); ?>"
									aria-selected="<?php echo 0 === $n ? 'true' : 'false'; ?>" data-view-all="<?php echo esc_url( sf_cat_link( $t['term'] ) ); ?>"><?php echo esc_html( $t['label'] ); ?></button>
							<?php endforeach; ?>
						</div>
					<?php else : ?><span></span><?php endif; ?>
					<?php if ( sf_s( 'view_all_text', 'Shop all makeup' ) ) : ?>
						<a class="rr-mk__view-all" href="<?php echo esc_url( $tabs ? sf_cat_link( $tabs[0]['term'] ) : $view_url ); ?>" data-mk-view-all><?php echo esc_html( sf_s( 'view_all_text', 'Shop all makeup' ) ); ?> &rsaquo;</a>
					<?php endif; ?>
				</div>

				<?php if ( $tabs ) : ?>
					<?php foreach ( $tabs as $n => $t ) : ?>
						<div class="rr-mk__panel<?php echo 0 === $n ? ' is-active' : ''; ?>" role="tabpanel" id="<?php echo esc_attr( $uid . '-panel-' . $n ); ?>" aria-labelledby="<?php echo esc_attr( $uid . '-tab-' . $n ); ?>">
							<?php $grid( $t['term'] ); ?>
						</div>
					<?php endforeach; ?>
				<?php else : ?>
					<div class="rr-mk__panel is-active"><?php $grid( $main ); ?></div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
