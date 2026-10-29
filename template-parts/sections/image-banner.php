<?php
/**
 * Section: Image banner (from Shopify rr-image-banner).
 *
 * @package Sunflorence
 */

defined( 'ABSPATH' ) || exit;

$img    = sf_s( 'image' );
$img_m  = sf_s( 'image_mobile' );
$style  = sf_vars( array( 'image_focus_x', 'image_focus_y', 'overlay_color', 'overlay_opacity', 'text_color', 'content_horizontal', 'content_vertical', 'banner_height', 'banner_height_mobile', 'corner_radius', 'corner_radius_br', 'corner_radius_mobile', 'corner_radius_br_mobile', 'section_bg', 'padding_vertical', 'padding_horizontal' ) );
$alt    = wp_strip_all_tags( sf_s( 'heading' ) );
?>
<section class="rr-banner<?php echo $img_m ? ' has-mobile' : ''; ?>" style="<?php echo esc_attr( $style ); ?>">
	<div class="rr-banner__inner">
		<?php
		echo sf_img( $img, 'full', array( 'class' => 'rr-banner__img', 'loading' => 'lazy', 'alt' => $alt, 'sizes' => '100vw' ) ); // phpcs:ignore
		echo sf_img( $img_m, 'large', array( 'class' => 'rr-banner__img-mobile', 'loading' => 'lazy', 'alt' => $alt ) ); // phpcs:ignore
		?>
		<div class="rr-banner__overlay"></div>
		<div class="rr-banner__content">
			<?php if ( sf_s( 'eyebrow' ) ) : ?><div class="rr-banner__eyebrow"><?php echo esc_html( sf_s( 'eyebrow' ) ); ?></div><?php endif; ?>
			<?php if ( sf_s( 'heading' ) ) : ?><h2 class="rr-banner__heading"><?php echo esc_html( sf_s( 'heading' ) ); ?></h2><?php endif; ?>
			<?php if ( sf_s( 'subtext' ) ) : ?><p class="rr-banner__subtext"><?php echo esc_html( sf_s( 'subtext' ) ); ?></p><?php endif; ?>
			<?php if ( sf_s( 'btn_text' ) ) : ?>
				<a href="<?php echo esc_url( sf_s( 'btn_url', '#' ) ); ?>" class="rr-banner__btn">
					<?php echo esc_html( sf_s( 'btn_text' ) ); ?>
					<svg viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
				</a>
			<?php endif; ?>
		</div>
	</div>
</section>
