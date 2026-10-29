<?php
/**
 * Template Name: FAQ
 *
 * Shopify sections/rr-faq.liquid. Each question has a "Category heading": the heading is shown
 * above a question whenever it changes (Shopify used separate heading blocks).
 *
 * @package Sunflorence
 */

defined( 'ABSPATH' ) || exit;

get_header();
sf_ctx( 'post:faq' );

$faqs    = sf_blocks( 'faq' );
$contact = get_page_by_path( 'contact' );
$cta_url = preg_replace( '#^/pages/#', '/', (string) sf_s( 'cta_url', $contact ? get_permalink( $contact ) : home_url( '/contact/' ) ) );
$last    = null;
?>
<div class="rr-faq" style="<?php echo esc_attr( sf_vars( array( 'body_bg' ) ) ); ?>">
	<div class="rr-faq__hero">
		<div class="rr-faq__eyebrow"><?php echo esc_html( sf_s( 'eyebrow' ) ); ?></div>
		<h1 class="rr-faq__hero-title">
			<?php echo esc_html( sf_s( 'heading' ) ); ?>
			<?php if ( sf_s( 'heading_italic' ) ) : ?><br><em><?php echo esc_html( sf_s( 'heading_italic' ) ); ?></em><?php endif; ?>
		</h1>
		<?php if ( sf_s( 'subtext' ) ) : ?><p class="rr-faq__hero-sub"><?php echo esc_html( sf_s( 'subtext' ) ); ?></p><?php endif; ?>
	</div>

	<div class="rr-faq__body">
		<div class="rr-faq__body-inner">
			<?php foreach ( $faqs as $f ) :
				$cat = trim( (string) sf_row( $f, 'category' ) );
				if ( $cat && $cat !== $last ) :
					?>
					<div class="rr-faq__cat">
						<div class="rr-faq__cat-line"></div>
						<span class="rr-faq__cat-label"><?php echo esc_html( $cat ); ?></span>
						<div class="rr-faq__cat-line"></div>
					</div>
				<?php endif; $last = $cat; ?>
				<details class="rr-faq__item">
					<summary class="rr-faq__summary">
						<?php echo esc_html( sf_row( $f, 'question' ) ); ?>
						<span class="rr-faq__icon"><svg viewBox="0 0 24 24" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span>
					</summary>
					<div class="rr-faq__answer"><?php echo wp_kses_post( sf_row( $f, 'answer' ) ); ?></div>
				</details>
			<?php endforeach; ?>

			<?php
			while ( have_posts() ) :
				the_post();
				if ( get_the_content() ) {
					echo '<div class="rr-faq__answer" style="padding:24px 0">';
					the_content();
					echo '</div>';
				}
			endwhile;
			?>

			<?php if ( sf_s( 'cta_show' ) ) : ?>
				<div class="rr-faq__cta">
					<div class="rr-faq__cta-eyebrow"><?php esc_html_e( 'Need More Help?', 'sunflorence' ); ?></div>
					<div class="rr-faq__cta-title"><?php echo wp_kses( __( 'Still have <em>questions?</em>', 'sunflorence' ), array( 'em' => array() ) ); ?></div>
					<div class="rr-faq__cta-sub"><?php echo esc_html( sf_s( 'cta_sub' ) ); ?></div>
					<a href="<?php echo esc_url( $cta_url ); ?>" class="rr-faq__cta-btn"><?php echo esc_html( sf_s( 'cta_text' ) ); ?></a>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
<?php
sf_ctx( 'row' );
get_footer();
