<?php
/**
 * Section: Why shop with us — text left, image panel with stats right (from Shopify rr-tested-proven).
 *
 * @package Sunflorence
 */

defined( 'ABSPATH' ) || exit;

$style = sf_vars( array( 'left_text_color', 'image_focus_x', 'image_focus_y', 'overlay_color', 'overlay_opacity', 'right_text_color', 'min_height', 'curve_radius', 'section_bg', 'padding_vertical', 'padding_horizontal' ) );
?>
<section class="rr-tp" style="<?php echo esc_attr( $style ); ?>">
	<div class="rr-tp__inner">
		<div class="rr-tp__left">
			<?php echo sf_img( sf_s( 'left_image' ), 'medium', array( 'class' => 'rr-tp__left-img', 'loading' => 'lazy', 'alt' => sf_s( 'left_heading' ) ) ); // phpcs:ignore ?>
			<?php if ( sf_s( 'left_heading' ) ) : ?><h2 class="rr-tp__left-heading"><?php echo esc_html( sf_s( 'left_heading' ) ); ?></h2><?php endif; ?>
			<?php if ( sf_s( 'left_body' ) ) : ?><div class="rr-tp__left-body"><?php echo wp_kses_post( sf_s( 'left_body' ) ); ?></div><?php endif; ?>
		</div>

		<div class="rr-tp__right">
			<?php echo sf_img( sf_s( 'right_image' ), 'large', array( 'class' => 'rr-tp__right-bg', 'loading' => 'lazy', 'alt' => sf_s( 'right_heading' ) ) ); // phpcs:ignore ?>
			<div class="rr-tp__right-overlay"></div>
			<div class="rr-tp__right-content">
				<?php if ( sf_s( 'right_heading' ) ) : ?><h3 class="rr-tp__right-heading"><?php echo esc_html( sf_s( 'right_heading' ) ); ?></h3><?php endif; ?>
				<?php if ( sf_s( 'product_name' ) ) : ?><div class="rr-tp__product-name"><?php echo esc_html( sf_s( 'product_name' ) ); ?></div><?php endif; ?>
				<?php if ( sf_s( 'product_sub' ) ) : ?><div class="rr-tp__product-sub"><?php echo esc_html( sf_s( 'product_sub' ) ); ?></div><?php endif; ?>
				<div class="rr-tp__stats">
					<?php foreach ( sf_blocks( 'stat' ) as $st ) : ?>
						<div class="rr-tp__stat">
							<div class="rr-tp__stat-num"><?php echo esc_html( sf_row( $st, 'number' ) ); ?></div>
							<p class="rr-tp__stat-text"><?php echo esc_html( sf_row( $st, 'text' ) ); ?></p>
						</div>
					<?php endforeach; ?>
				</div>
				<?php if ( sf_s( 'footnote' ) ) : ?><p class="rr-tp__footnote"><?php echo esc_html( sf_s( 'footnote' ) ); ?></p><?php endif; ?>
			</div>
		</div>
	</div>
</section>
