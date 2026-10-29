<?php
/**
 * Section: See it in action — vertical video cards + testimonials (from Shopify rr-see-in-action).
 * Upload videos to the Media Library (MP4) or use a video URL (YouTube/Vimeo embed link).
 *
 * @package Sunflorence
 */

defined( 'ABSPATH' ) || exit;

$cards = sf_blocks( 'video_card' );
$tblocks = sf_blocks( 'testimonial' );
$fallback = false;
if ( ! $tblocks ) {
	$tblocks  = $cards;
	$fallback = true;
}
if ( ! $cards && ! $tblocks ) {
	return;
}
$style = sf_vars( array( 'bg_color', 'text_color', 'accent_color', 'padding_vertical', 'padding_horizontal' ) );
$star  = '<svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
?>
<section class="rr-sia" style="<?php echo esc_attr( $style ); ?>">
	<div class="rr-sia__inner">
		<div class="rr-sia__head">
			<?php if ( sf_s( 'heading' ) ) : ?><h2 class="rr-sia__heading"><?php echo esc_html( sf_s( 'heading' ) ); ?></h2><?php endif; ?>
			<?php if ( sf_s( 'badge_text' ) ) : ?>
				<a href="<?php echo esc_url( sf_s( 'badge_url', '#' ) ); ?>" class="rr-sia__badge" target="_blank" rel="noopener">
					<svg class="rr-sia__badge-ig" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
					<span><?php echo esc_html( sf_s( 'badge_text' ) ); ?></span>
				</a>
			<?php endif; ?>
		</div>

		<div class="rr-sia__slider">
			<?php foreach ( $cards as $n => $c ) :
				$file   = sf_row( $c, 'video_file' );
				$src    = is_array( $file ) ? ( $file['url'] ?? '' ) : ( $file ? wp_get_attachment_url( (int) $file ) : '' );
				$poster = sf_row( $c, 'poster' );
				$avatar = sf_row( $c, 'reviewer_avatar' ) ? wp_get_attachment_image_url( sf_row( $c, 'reviewer_avatar' ), 'thumbnail' ) : '';
				?>
				<div class="rr-sia__card<?php echo 0 === $n ? ' is-active' : ''; ?>" data-idx="<?php echo esc_attr( $n ); ?>"
					<?php if ( $src ) : ?>data-video-src="<?php echo esc_url( $src ); ?>"<?php endif; ?>
					<?php if ( sf_row( $c, 'video_url' ) ) : ?>data-video-url="<?php echo esc_url( sf_row( $c, 'video_url' ) ); ?>"<?php endif; ?>
					data-name="<?php echo esc_attr( sf_row( $c, 'reviewer_name' ) ); ?>" data-verified="<?php echo sf_row( $c, 'verified' ) ? 'true' : 'false'; ?>"
					data-rating="<?php echo esc_attr( (int) sf_row( $c, 'rating', 5 ) ); ?>" data-text="<?php echo esc_attr( sf_row( $c, 'review_text' ) ); ?>" data-avatar="<?php echo esc_url( (string) $avatar ); ?>">
					<?php if ( $poster ) : ?>
						<?php echo sf_img( $poster, 'medium_large', array( 'class' => 'rr-sia__poster', 'loading' => 'lazy', 'alt' => sf_row( $c, 'reviewer_name' ) ) ); // phpcs:ignore ?>
					<?php elseif ( $src ) : ?>
						<video class="rr-sia__poster" src="<?php echo esc_url( $src ); ?>#t=0.5" muted playsinline preload="metadata" aria-hidden="true"></video>
					<?php else : ?>
						<div class="rr-sia__empty"><svg viewBox="0 0 24 24"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg><span><?php esc_html_e( 'Add a video', 'sunflorence' ); ?></span></div>
					<?php endif; ?>
					<div class="rr-sia__play"><div class="rr-sia__play-icon"><svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg></div></div>
				</div>
			<?php endforeach; ?>
		</div>

		<?php if ( $tblocks && sf_s( 'show_testimonial', true ) ) : ?>
			<div class="rr-sia__testimonials">
				<?php foreach ( $tblocks as $i => $t ) :
					if ( $fallback && $i >= 3 ) {
						break;
					}
					$r = max( 0, min( 5, (int) round( (float) sf_row( $t, 'rating', 5 ) ) ) );
					?>
					<div class="rr-sia__testimonial">
						<div class="rr-sia__t-avatar"><?php echo sf_img( sf_row( $t, 'reviewer_avatar' ), 'thumbnail', array( 'loading' => 'lazy', 'alt' => sf_row( $t, 'reviewer_name' ) ) ); // phpcs:ignore ?></div>
						<div class="rr-sia__t-body">
							<div class="rr-sia__t-head">
								<span class="rr-sia__t-name"><?php echo esc_html( sf_row( $t, 'reviewer_name' ) ); ?></span>
								<?php if ( sf_row( $t, 'verified' ) ) : ?><span class="rr-sia__t-verified"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></span><?php endif; ?>
								<span class="rr-sia__t-stars"><?php echo str_repeat( $star, $r ); // phpcs:ignore ?></span>
							</div>
							<p class="rr-sia__t-text"><?php echo esc_html( sf_row( $t, 'review_text' ) ); ?></p>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<div class="rr-sia__nav">
			<button class="rr-sia__nav-btn" data-dir="-1" aria-label="<?php esc_attr_e( 'Previous', 'sunflorence' ); ?>"><svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg></button>
			<button class="rr-sia__nav-btn" data-dir="1" aria-label="<?php esc_attr_e( 'Next', 'sunflorence' ); ?>"><svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></button>
		</div>
	</div>
</section>
