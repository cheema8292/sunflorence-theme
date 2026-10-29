<?php
/**
 * Section: FAQ accordion (from Shopify rr-home-faq).
 * Also used on the product page: $args = [ 'faqs' => [ [question, answer], … ], 'heading' => … ].
 *
 * @package Sunflorence
 */

defined( 'ABSPATH' ) || exit;

if ( isset( $args['faqs'] ) ) {
	$faqs   = $args['faqs'];
	$h      = $args['heading'] ?? __( 'Frequently Asked Questions', 'sunflorence' );
	$sub    = $args['subtext'] ?? '';
	$first  = false;
	$single = true;
	$cta    = $args['cta_text'] ?? '';
	$btn    = $args['btn_text'] ?? '';
	$url    = $args['btn_url'] ?? '';
	$style  = '';
} else {
	$faqs   = sf_blocks( 'faq' );
	$h      = sf_s( 'heading', 'Answers To Your Questions' );
	$sub    = sf_s( 'subtext' );
	$first  = (bool) sf_s( 'first_open', false );
	$single = (bool) sf_s( 'single_open', true );
	$cta    = sf_s( 'cta_text' );
	$btn    = sf_s( 'btn_text' );
	$faq_pg = get_page_by_path( 'faq' );
	$url    = sf_s( 'btn_url', $faq_pg ? get_permalink( $faq_pg ) : home_url( '/faq/' ) );
	$style  = sf_vars( array( 'bg_color', 'text_color', 'btn_bg', 'btn_color', 'padding_vertical', 'padding_horizontal' ) );
}
if ( ! $faqs ) {
	return;
}
// Shopify URLs like /pages/faq → WordPress /faq/
$url = preg_replace( '#^/pages/#', '/', (string) $url );
?>
<section class="rr-hfaq" data-single="<?php echo $single ? '1' : '0'; ?>" style="<?php echo esc_attr( $style ); ?>">
	<div class="rr-hfaq__inner">
		<div class="rr-hfaq__head">
			<?php if ( $h ) : ?><h2 class="rr-hfaq__heading"><?php echo esc_html( $h ); ?></h2><?php endif; ?>
			<?php if ( $sub ) : ?><p class="rr-hfaq__subtext"><?php echo esc_html( $sub ); ?></p><?php endif; ?>
		</div>
		<div class="rr-hfaq__body">
			<div class="rr-hfaq__list">
				<?php foreach ( $faqs as $n => $f ) : $open = $first && 0 === $n; ?>
					<div class="rr-hfaq__item<?php echo $open ? ' is-open' : ''; ?>">
						<button class="rr-hfaq__q" type="button" aria-expanded="<?php echo $open ? 'true' : 'false'; ?>">
							<span><?php echo esc_html( sf_row( $f, 'question' ) ); ?></span>
							<span class="rr-hfaq__q-icon" aria-hidden="true"></span>
						</button>
						<div class="rr-hfaq__a-wrap"><div class="rr-hfaq__a"><?php echo wp_kses_post( wpautop( (string) sf_row( $f, 'answer' ) ) ); ?></div></div>
					</div>
				<?php endforeach; ?>
			</div>
			<?php if ( $cta || $btn ) : ?>
				<div class="rr-hfaq__cta">
					<?php if ( $cta ) : ?><span class="rr-hfaq__cta-text"><?php echo esc_html( $cta ); ?></span><?php endif; ?>
					<?php if ( $btn ) : ?><a href="<?php echo esc_url( $url ); ?>" class="rr-hfaq__cta-btn"><?php echo esc_html( $btn ); ?></a><?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
