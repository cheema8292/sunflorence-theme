<?php
/**
 * Template Name: About
 *
 * Shopify sections/rr-about.liquid. Texts: edit this page (fields below the editor).
 *
 * @package Sunflorence
 */

defined( 'ABSPATH' ) || exit;

get_header();
sf_ctx( 'post:about' );

$mission = sf_blocks( 'mission_text' );
$values  = sf_blocks( 'value' );
$style   = sf_vars( array( 'hero_bg', 'mission_bg' ) );
$shield  = '<svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>';
// store promises (what every order gets) — editable with the "sf_about_ribbon" filter
$ribbon  = apply_filters( 'sf_about_ribbon', array( 'Secure Checkout', 'Tracked Shipping', '30-Day Returns', '24/7 Support', 'Handpicked Quality' ) );
$stats   = apply_filters( 'sf_about_stats', array( array( '50+', 'Countries Shipped' ), array( '24/7', 'Customer Support' ), array( '30', 'Day Returns' ) ) );
?>
<div class="rr-about" style="<?php echo esc_attr( $style ); ?>">
	<div class="rr-about__hero">
		<?php if ( sf_s( 'hero_eyebrow' ) ) : ?><div class="rr-about__hero-eyebrow"><?php echo esc_html( sf_s( 'hero_eyebrow' ) ); ?></div><?php endif; ?>
		<h1 class="rr-about__hero-title">
			<?php echo esc_html( sf_s( 'hero_heading' ) ); ?>
			<?php if ( sf_s( 'hero_heading_italic' ) ) : ?><br><em><?php echo esc_html( sf_s( 'hero_heading_italic' ) ); ?></em><?php endif; ?>
		</h1>
		<?php if ( sf_s( 'hero_sub' ) ) : ?><p class="rr-about__hero-sub"><?php echo esc_html( sf_s( 'hero_sub' ) ); ?></p><?php endif; ?>
		<div class="rr-about__hero-stats">
			<?php foreach ( $stats as $st ) : ?>
				<div class="rr-about__stat">
					<div class="rr-about__stat-num"><?php echo esc_html( $st[0] ); ?></div>
					<div class="rr-about__stat-label"><?php echo esc_html( $st[1] ); ?></div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>

	<div class="rr-about__mission">
		<div class="rr-about__mission-inner">
			<div class="rr-about__mission-img">
				<?php echo sf_img( sf_s( 'mission_image' ), 'large', array( 'loading' => 'lazy', 'alt' => __( 'Our Mission', 'sunflorence' ) ) ); // phpcs:ignore ?>
			</div>
			<div>
				<?php if ( sf_s( 'mission_eyebrow' ) ) : ?><div class="rr-about__mission-eyebrow"><?php echo esc_html( sf_s( 'mission_eyebrow' ) ); ?></div><?php endif; ?>
				<h2 class="rr-about__mission-title">
					<?php echo esc_html( sf_s( 'mission_heading' ) ); ?>
					<?php if ( sf_s( 'mission_heading_italic' ) ) : ?><em><?php echo esc_html( sf_s( 'mission_heading_italic' ) ); ?></em><?php endif; ?>
				</h2>
				<?php foreach ( $mission as $m ) : ?>
					<p class="rr-about__mission-text"><?php echo esc_html( sf_row( $m, 'text' ) ); ?></p>
				<?php endforeach; ?>
				<?php
				while ( have_posts() ) :
					the_post();
					if ( get_the_content() ) {
						echo '<div class="rr-about__mission-text">';
						the_content();
						echo '</div>';
					}
				endwhile;
				?>
			</div>
		</div>
	</div>

	<?php if ( $ribbon ) : ?>
		<div class="rr-about__ribbon">
			<?php foreach ( $ribbon as $r ) : ?>
				<div class="rr-about__ribbon-item"><?php echo $shield; // phpcs:ignore ?><span class="rr-about__ribbon-item-text"><?php echo esc_html( $r ); ?></span></div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<?php if ( $values ) : ?>
		<div class="rr-about__values">
			<div class="rr-about__values-inner">
				<div class="rr-about__values-header">
					<?php if ( sf_s( 'values_eyebrow' ) ) : ?><div class="rr-about__values-eyebrow"><?php echo esc_html( sf_s( 'values_eyebrow' ) ); ?></div><?php endif; ?>
					<h2 class="rr-about__values-title">
						<?php echo esc_html( sf_s( 'values_heading' ) ); ?>
						<?php if ( sf_s( 'values_heading_italic' ) ) : ?><em><?php echo esc_html( sf_s( 'values_heading_italic' ) ); ?></em><?php endif; ?>
					</h2>
				</div>
				<div class="rr-about__values-grid">
					<?php foreach ( $values as $i => $v ) : ?>
						<div class="rr-about__value-card">
							<div class="rr-about__value-num"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></div>
							<div class="rr-about__value-title"><?php echo esc_html( sf_row( $v, 'title' ) ); ?></div>
							<div class="rr-about__value-text"><?php echo esc_html( sf_row( $v, 'text' ) ); ?></div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	<?php endif; ?>
</div>
<?php
sf_ctx( 'row' );
get_footer();
