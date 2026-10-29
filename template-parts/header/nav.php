<?php
/**
 * Fixed header: announcement bar + nav (hamburger · logo · icons).
 * Transparent with the white logo over the homepage hero, solid with the colour logo after scrolling
 * and on every other page (same behaviour as the Shopify rr-header).
 *
 * @package Sunflorence
 */

defined( 'ABSPATH' ) || exit;

$logo_h   = (int) sf_opt( 'logo_height', 84 );
$logo_h_m = (int) sf_opt( 'logo_height_mobile', 62 );
$white_id = sf_opt( 'logo_white' );
$color_id = sf_opt( 'logo_color' );
$white    = $white_id ? wp_get_attachment_image_url( $white_id, 'medium' ) : sf_asset( 'images/logo-sunflorence-white.png' );
$color    = $color_id ? wp_get_attachment_image_url( $color_id, 'medium' ) : sf_asset( 'images/logo-sunflorence.png' );
$name     = get_bloginfo( 'name' );

$account_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : wp_login_url();
$cart_url    = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/cart/' );
?>
<div class="rr-header-fixed" id="rr-hf" style="--rr-logo-h:<?php echo esc_attr( $logo_h ); ?>px; --rr-logo-h-m:<?php echo esc_attr( $logo_h_m ); ?>px;">

	<?php if ( sf_opt( 'announce_show', true ) && sf_opt( 'announce_text', 'Secure Checkout  |  30-Day Returns  |  24/7 Support' ) ) : ?>
		<div class="rr-announce" style="background:<?php echo esc_attr( sf_color( sf_opt( 'announce_bg' ), '#22344a' ) ); ?>;color:<?php echo esc_attr( sf_color( sf_opt( 'announce_color' ), '#D9B99B' ) ); ?>">
			<?php echo esc_html( sf_opt( 'announce_text', 'Secure Checkout  |  30-Day Returns  |  24/7 Support' ) ); ?>
		</div>
	<?php endif; ?>

	<nav class="rr-nav" id="rr-nav" aria-label="<?php esc_attr_e( 'Main', 'sunflorence' ); ?>">
		<div class="rr-nav__inner">
			<div class="rr-nav__left">
				<button class="rr-nav__hamburger" id="rr-ham" aria-label="<?php esc_attr_e( 'Open menu', 'sunflorence' ); ?>" aria-expanded="false" aria-controls="rr-drawer">
					<span></span><span></span><span></span>
				</button>
			</div>

			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="rr-nav__logo" aria-label="<?php echo esc_attr( $name ); ?>">
				<span class="rr-nav__logo-stack">
					<img class="rr-logo--white" src="<?php echo esc_url( $white ); ?>" alt="<?php echo esc_attr( $name ); ?>">
					<img class="rr-logo--color" src="<?php echo esc_url( $color ); ?>" alt="" aria-hidden="true">
				</span>
			</a>

			<div class="rr-nav__icons">
				<a href="<?php echo esc_url( home_url( '/?s=&post_type=product' ) ); ?>" aria-label="<?php esc_attr_e( 'Search', 'sunflorence' ); ?>" class="rr-nav__search"><?php echo sf_icon( 'search' ); // phpcs:ignore ?></a>
				<a href="<?php echo esc_url( $account_url ); ?>" aria-label="<?php esc_attr_e( 'Account', 'sunflorence' ); ?>" rel="nofollow"><?php echo sf_icon( 'account' ); // phpcs:ignore ?></a>
				<a href="<?php echo esc_url( $cart_url ); ?>" class="rr-nav__cart" aria-label="<?php esc_attr_e( 'Cart', 'sunflorence' ); ?>">
					<?php echo sf_icon( 'cart' ); // phpcs:ignore ?>
					<?php echo sf_cart_badge_html(); // phpcs:ignore — escaped inside ?>
				</a>
			</div>
		</div>
	</nav>
</div>
<div class="rr-header-spacer" id="rr-spacer"></div>
