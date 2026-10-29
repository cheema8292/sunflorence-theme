<?php
/**
 * 404 — Shopify sections/rr-404.liquid. Texts: Sunflorence → Pages → 404 page.
 *
 * @package Sunflorence
 */

defined( 'ABSPATH' ) || exit;

get_header();
sf_ctx( 'opt:404' );

$shop  = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
$links = array();
foreach ( array( 'about' => __( 'About', 'sunflorence' ), 'contact' => __( 'Contact', 'sunflorence' ), 'faq' => __( 'FAQ', 'sunflorence' ), 'track-order' => __( 'Track My Order', 'sunflorence' ) ) as $slug => $label ) {
	$pg = get_page_by_path( $slug );
	if ( $pg ) {
		$links[ get_permalink( $pg ) ] = $label;
	}
}
$links[ $shop ] = __( 'Shop All', 'sunflorence' );
?>
<section class="rr-404">
	<div class="rr-404__inner">
		<div class="rr-404__number">404</div>
		<div class="rr-404__eyebrow"><?php echo esc_html( sf_s( 'eyebrow' ) ); ?></div>
		<h1 class="rr-404__heading">
			<?php echo esc_html( sf_s( 'heading' ) ); ?>
			<?php if ( sf_s( 'heading_italic' ) ) : ?><br><em><?php echo esc_html( sf_s( 'heading_italic' ) ); ?></em><?php endif; ?>
		</h1>
		<p class="rr-404__sub"><?php echo esc_html( sf_s( 'subtext' ) ); ?></p>
		<div class="rr-404__actions">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="rr-404__btn-primary"><span><?php echo esc_html( sf_s( 'btn_primary_text' ) ); ?></span></a>
			<a href="<?php echo esc_url( $shop ); ?>" class="rr-404__btn-secondary"><?php echo esc_html( sf_s( 'btn_secondary_text' ) ); ?></a>
		</div>
		<div class="rr-404__links">
			<?php foreach ( $links as $url => $label ) : ?>
				<a href="<?php echo esc_url( $url ); ?>" class="rr-404__link"><?php echo esc_html( $label ); ?></a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php
sf_ctx( 'row' );
get_footer();
