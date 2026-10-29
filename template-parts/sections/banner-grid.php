<?php
/**
 * Section: Banner grid — category tiles (4 squares), 2 banners side by side, or 1 full-width banner
 * (client request, like theskinfit.com). A banner with only a category gets its image, title and link
 * from the category (category image, else its newest product photo).
 *
 * @package Sunflorence
 */

defined( 'ABSPATH' ) || exit;

$layout = in_array( sf_s( 'layout', 'tiles' ), array( 'tiles', 'split', 'wide' ), true ) ? sf_s( 'layout', 'tiles' ) : 'tiles';
$items  = array();
foreach ( sf_blocks( 'banner' ) as $b ) {
	$term  = sf_term( sf_row( $b, 'category' ) );
	$img   = (int) ( is_array( sf_row( $b, 'image' ) ) ? ( sf_row( $b, 'image' )['ID'] ?? 0 ) : sf_row( $b, 'image' ) );
	$img_m = (int) ( is_array( sf_row( $b, 'image_mobile' ) ) ? ( sf_row( $b, 'image_mobile' )['ID'] ?? 0 ) : sf_row( $b, 'image_mobile' ) );
	if ( ! $img && $term ) {
		$img = (int) get_term_meta( $term->term_id, 'thumbnail_id', true );
		if ( ! $img ) {
			$p   = sf_products_in_cat( $term, 1 );
			$img = $p ? (int) $p[0]->get_image_id() : 0;
		}
	}
	$link = sf_row( $b, 'link' ) ? sf_url( sf_row( $b, 'link' ) ) : ( $term ? sf_cat_link( $term ) : '' );
	$title = sf_row( $b, 'title' ) ? sf_row( $b, 'title' ) : ( $term ? $term->name : '' );
	if ( ! $img && ! $title ) {
		continue;
	}
	$items[] = compact( 'img', 'img_m', 'link', 'title' ) + array(
		'text'   => sf_row( $b, 'text' ),
		'button' => sf_row( $b, 'button', __( 'Shop Now', 'sunflorence' ) ),
	);
}
if ( ! $items ) {
	return;
}
$show_text = (bool) sf_s( 'show_text', true );
$size      = 'wide' === $layout ? 'full' : 'large';
?>
<section class="sf-bg sf-bg--<?php echo esc_attr( $layout ); ?>">
	<?php if ( sf_s( 'heading' ) ) : ?>
		<h2 class="sf-bg__heading"><?php echo sf_inline( sf_s( 'heading' ) ); // phpcs:ignore ?></h2>
	<?php endif; ?>
	<div class="sf-bg__grid">
		<?php foreach ( $items as $it ) :
			$tag = $it['link'] ? 'a' : 'div';
			?>
			<<?php echo $tag; // phpcs:ignore ?> class="sf-bg__item<?php echo $it['img'] ? '' : ' sf-bg__item--empty'; ?>"<?php echo $it['link'] ? ' href="' . esc_url( $it['link'] ) . '"' : ''; ?>>
				<?php if ( $it['img'] ) : ?>
					<picture class="sf-bg__media">
						<?php if ( $it['img_m'] ) : ?>
							<source media="(max-width: 640px)" srcset="<?php echo esc_url( (string) wp_get_attachment_image_url( $it['img_m'], 'large' ) ); ?>">
						<?php endif; ?>
						<?php echo wp_get_attachment_image( $it['img'], $size, false, array( 'loading' => 'lazy', 'alt' => $it['title'], 'sizes' => 'tiles' === $layout ? '(max-width: 640px) 50vw, 25vw' : ( 'split' === $layout ? '(max-width: 640px) 100vw, 50vw' : '100vw' ) ) ); ?>
					</picture>
				<?php endif; ?>
				<?php if ( $show_text && ( $it['title'] || $it['text'] ) ) : ?>
					<span class="sf-bg__text">
						<?php if ( $it['title'] ) : ?><span class="sf-bg__title"><?php echo esc_html( $it['title'] ); ?></span><?php endif; ?>
						<?php if ( $it['text'] ) : ?><span class="sf-bg__sub"><?php echo esc_html( $it['text'] ); ?></span><?php endif; ?>
						<?php if ( $it['link'] && $it['button'] ) : ?><span class="sf-bg__btn"><?php echo esc_html( $it['button'] ); ?> &rarr;</span><?php endif; ?>
					</span>
				<?php endif; ?>
			</<?php echo $tag; // phpcs:ignore ?>>
		<?php endforeach; ?>
	</div>
</section>
