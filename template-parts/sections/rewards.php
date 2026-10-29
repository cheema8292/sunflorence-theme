<?php
/**
 * Section: Rewards banner — image with badge + content panel (from Shopify rr-rewards).
 *
 * @package Sunflorence
 */

defined( 'ABSPATH' ) || exit;

$style = sf_vars( array( 'image_focus_x', 'image_focus_y', 'badge_text_color', 'badge_border_color', 'left_corner_radius', 'left_corner_radius_br', 'right_bg', 'right_text_color', 'right_corner_radius', 'btn_bg', 'btn_color', 'min_height', 'section_bg', 'padding_vertical', 'padding_horizontal' ) );
?>
<section class="rr-rw" style="<?php echo esc_attr( $style ); ?>">
	<div class="rr-rw__inner">
		<div class="rr-rw__left">
			<?php echo sf_img( sf_s( 'left_image' ), 'large', array( 'class' => 'rr-rw__left-img', 'loading' => 'lazy', 'alt' => sf_s( 'badge_main' ) ) ); // phpcs:ignore ?>
			<?php if ( sf_s( 'badge_top' ) || sf_s( 'badge_main' ) ) : ?>
				<div class="rr-rw__left-badge">
					<?php if ( sf_s( 'badge_top' ) ) : ?><div class="rr-rw__left-badge-top"><?php echo esc_html( sf_s( 'badge_top' ) ); ?></div><?php endif; ?>
					<?php if ( sf_s( 'badge_main' ) ) : ?><div class="rr-rw__left-badge-main"><?php echo esc_html( sf_s( 'badge_main' ) ); ?></div><?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
		<div class="rr-rw__right">
			<?php if ( sf_s( 'heading' ) ) : ?><h2 class="rr-rw__right-heading"><?php echo esc_html( sf_s( 'heading' ) ); ?></h2><?php endif; ?>
			<?php if ( sf_s( 'body' ) ) : ?><p class="rr-rw__right-body"><?php echo esc_html( sf_s( 'body' ) ); ?></p><?php endif; ?>
			<div class="rr-rw__actions">
				<?php if ( sf_s( 'btn1_text' ) ) : ?>
					<a href="<?php echo esc_url( sf_s( 'btn1_url', '#' ) ); ?>" class="rr-rw__btn-primary"><?php echo esc_html( sf_s( 'btn1_text' ) ); ?></a>
				<?php endif; ?>
				<?php if ( sf_s( 'btn2_text' ) ) : ?>
					<a href="<?php echo esc_url( sf_s( 'btn2_url', '#' ) ); ?>" class="rr-rw__btn-secondary">
						<?php echo esc_html( sf_s( 'btn2_text' ) ); ?>
						<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
					</a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
