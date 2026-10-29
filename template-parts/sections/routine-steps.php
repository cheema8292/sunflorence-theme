<?php
/**
 * Section: Routine steps — auto-rotating step cards (from Shopify rr-routine-steps).
 *
 * @package Sunflorence
 */

defined( 'ABSPATH' ) || exit;

$steps = sf_blocks( 'step' );
if ( ! $steps ) {
	return;
}
$style = sf_vars( array( 'bg_color', 'text_color', 'card_radius', 'padding_vertical', 'padding_horizontal' ) );
?>
<section class="rr-rs" data-speed="<?php echo esc_attr( max( 2, (int) sf_s( 'auto_speed', 3 ) ) ); ?>" style="<?php echo esc_attr( $style ); ?>">
	<div class="rr-rs__inner">
		<div class="rr-rs__head">
			<?php if ( sf_s( 'heading' ) ) : ?><h2 class="rr-rs__heading"><?php echo esc_html( sf_s( 'heading' ) ); ?></h2><?php endif; ?>
			<?php if ( sf_s( 'subtext' ) ) : ?><p class="rr-rs__subtext"><?php echo esc_html( sf_s( 'subtext' ) ); ?></p><?php endif; ?>
		</div>

		<div class="rr-rs__cards">
			<?php foreach ( $steps as $n => $st ) : ?>
				<div class="rr-rs__card<?php echo 0 === $n ? ' is-active' : ''; ?>" data-idx="<?php echo esc_attr( $n ); ?>">
					<?php echo sf_img( sf_row( $st, 'image' ), 'large', array( 'class' => 'rr-rs__card-img', 'loading' => 'lazy', 'alt' => sf_row( $st, 'title' ) ) ); // phpcs:ignore ?>
					<?php if ( sf_row( $st, 'title' ) || sf_row( $st, 'desc' ) ) : ?>
						<div class="rr-rs__card-content">
							<?php if ( sf_row( $st, 'title' ) ) : ?><div class="rr-rs__card-title"><?php echo esc_html( sf_row( $st, 'title' ) ); ?></div><?php endif; ?>
							<?php if ( sf_row( $st, 'desc' ) ) : ?><p class="rr-rs__card-desc"><?php echo esc_html( sf_row( $st, 'desc' ) ); ?></p><?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>

		<div class="rr-rs__steps">
			<?php foreach ( $steps as $n => $st ) : ?>
				<div class="rr-rs__step<?php echo 0 === $n ? ' is-active' : ''; ?>" data-idx="<?php echo esc_attr( $n ); ?>">
					<button class="rr-rs__step-btn" type="button" aria-label="<?php echo esc_attr( sprintf( __( 'Go to step %d', 'sunflorence' ), $n + 1 ) ); ?>"><?php echo esc_html( '0' . ( $n + 1 ) ); ?></button>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
