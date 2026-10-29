<?php
/**
 * Section: Hero slider (from Shopify rr-hero).
 *
 * @package Sunflorence
 */

defined( 'ABSPATH' ) || exit;

$demo   = ! empty( $args['demo'] );
$uid    = 'rr-hero-' . (int) ( $args['index'] ?? 0 );
$slides = $demo ? array() : sf_blocks( 'slide' );

if ( ! $slides ) {
	$slides = array(
		array( 'badge_text' => 'Skin Care • Makeup • Hair • K-Beauty', 'heading' => 'Beauty For Every You,', 'heading_italic' => 'All In One Place.', 'subtext' => 'Skin care, makeup, hair care, personal care and K-Beauty favourites, handpicked for your everyday routine.', 'btn1_text' => 'Shop Now', 'btn1_url' => function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ) ),
	);
}

$speed  = max( 3, (int) ( $demo ? 5 : sf_s( 'autoplay_speed', 5 ) ) );
$radius = (int) ( $demo ? 120 : sf_s( 'curve_radius', 80 ) );
?>
<section class="rr-hero" id="<?php echo esc_attr( $uid ); ?>" data-speed="<?php echo esc_attr( $speed ); ?>" style="--rr-hero-radius:<?php echo esc_attr( $radius ); ?>px">
	<div class="rr-hero__track">
		<div class="rr-hero__progress"><div class="rr-hero__progress-fill"></div></div>

		<div class="rr-hero__slider">
			<?php foreach ( $slides as $n => $s ) : ?>
				<div class="rr-hero__slide<?php echo 0 === $n ? ' active' : ''; ?>" style="background-color: <?php echo esc_attr( sf_color( sf_row( $s, 'bg_fallback' ), '#1b2a3c' ) ); ?>; --rr-hero-pos: <?php echo esc_attr( (int) sf_row( $s, 'focus_x', 50 ) ); ?>% <?php echo esc_attr( (int) sf_row( $s, 'focus_y', 50 ) ); ?>%;">
					<?php
					if ( sf_row( $s, 'image' ) ) {
						echo sf_img( sf_row( $s, 'image' ), 'sf-hero', array( // phpcs:ignore
							'class'         => 'rr-hero__slide-img',
							'loading'       => 0 === $n ? 'eager' : 'lazy',
							'fetchpriority' => 0 === $n ? 'high' : 'auto',
							'sizes'         => '100vw',
							'alt'           => wp_strip_all_tags( sf_row( $s, 'heading' ) ),
						) );
					}
					?>
					<div class="rr-hero__overlay"></div>
					<div class="rr-hero__content">
						<div class="rr-hero__text">
							<?php if ( sf_row( $s, 'badge_text' ) ) : ?>
								<div class="rr-hero__badge">
									<span class="rr-hero__badge-stars"><?php echo str_repeat( sf_icon( 'star' ), 5 ); // phpcs:ignore ?></span>
									<span class="rr-hero__badge-text"><?php echo esc_html( sf_row( $s, 'badge_text' ) ); ?></span>
								</div>
							<?php endif; ?>
							<<?php echo 0 === $n ? 'h1' : 'h2'; ?> class="rr-hero__title">
								<?php echo esc_html( sf_row( $s, 'heading' ) ); ?>
								<?php if ( sf_row( $s, 'heading_italic' ) ) : ?><br><em><?php echo esc_html( sf_row( $s, 'heading_italic' ) ); ?></em><?php endif; ?>
							</<?php echo 0 === $n ? 'h1' : 'h2'; ?>>
							<?php if ( sf_row( $s, 'subtext' ) ) : ?>
								<p class="rr-hero__sub"><?php echo esc_html( sf_row( $s, 'subtext' ) ); ?></p>
							<?php endif; ?>
							<div class="rr-hero__actions">
								<?php if ( sf_row( $s, 'btn1_text' ) ) : ?>
									<a href="<?php echo esc_url( sf_row( $s, 'btn1_url', '#' ) ); ?>" class="rr-hero__btn-primary"><?php echo esc_html( sf_row( $s, 'btn1_text' ) ); ?></a>
								<?php endif; ?>
								<?php if ( sf_row( $s, 'btn2_text' ) ) : ?>
									<a href="<?php echo esc_url( sf_row( $s, 'btn2_url', '#' ) ); ?>" class="rr-hero__btn-secondary"><?php echo esc_html( sf_row( $s, 'btn2_text' ) ); ?> <?php echo sf_icon( 'arrow' ); // phpcs:ignore ?></a>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

		<?php if ( count( $slides ) > 1 ) : ?>
			<div class="rr-hero__dots">
				<?php foreach ( $slides as $n => $s ) : ?>
					<button class="rr-hero__dot<?php echo 0 === $n ? ' active' : ''; ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Slide %d', 'sunflorence' ), $n + 1 ) ); ?>"></button>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
