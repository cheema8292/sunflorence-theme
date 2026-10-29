<?php
/**
 * Footer — newsletter band, brand / categories / customer-care / trust columns, bottom bar.
 * Converted from the Shopify custom-footer section. Settings: Sunflorence → Footer tab.
 *
 * @package Sunflorence
 */

defined( 'ABSPATH' ) || exit;

$style = sprintf(
	'--sf-ft-bg:%s;--sf-ft-bg-2:%s;--sf-ft-text:%s;--sf-ft-accent:%s;--sf-ft-btn:%s;',
	sf_color( sf_opt( 'footer_bg' ), '#22344A' ),
	sf_color( sf_opt( 'footer_bg_2' ), '#17243A' ),
	sf_color( sf_opt( 'footer_text' ), '#F3EDE6' ),
	sf_color( sf_opt( 'footer_accent' ), '#D9B99B' ),
	sf_color( sf_opt( 'footer_button' ), '#6E1F2F' )
);

$email   = sf_opt( 'contact_email', 'sunflorencellc@gmail.com' );
$phone   = sf_opt( 'contact_phone', '+1 321 357 4656' );
$address = sf_opt( 'contact_address', '7901 4th St N, Saint Petersburg, Florida 33702, United States' );
$hours   = sf_opt( 'contact_hours', '24/7 customer support' );
$logo_id = sf_opt( 'footer_logo' );

$socials = array_filter( array(
	'facebook'  => sf_opt( 'social_facebook' ),
	'instagram' => sf_opt( 'social_instagram' ),
	'tiktok'    => sf_opt( 'social_tiktok' ),
	'pinterest' => sf_opt( 'social_pinterest' ),
	'youtube'   => sf_opt( 'social_youtube' ),
) );

// Categories column
$cat_ids = (array) sf_opt( 'footer_categories', array() );
$cats    = array();
if ( taxonomy_exists( 'product_cat' ) ) {
	$cats = $cat_ids
		? get_terms( array( 'taxonomy' => 'product_cat', 'include' => array_map( 'intval', $cat_ids ), 'orderby' => 'include', 'hide_empty' => false ) )
		: get_terms( array( 'taxonomy' => 'product_cat', 'parent' => 0, 'number' => 8, 'hide_empty' => false, 'exclude' => array( (int) get_option( 'default_product_cat' ) ) ) );
	$cats = is_wp_error( $cats ) ? array() : $cats;
}

$trust = sf_opt( 'trust_items', array() );
if ( ! $trust ) {
	$trust = array(
		array( 'trust_icon' => 'truck', 'trust_item_title' => 'Tracked Shipping', 'trust_item_text' => 'Follow every order to your door' ),
		array( 'trust_icon' => 'return', 'trust_item_title' => '30-Day Returns', 'trust_item_text' => 'Easy, stress-free returns' ),
		array( 'trust_icon' => 'lock', 'trust_item_title' => 'Secure Payment', 'trust_item_text' => '100% secure transactions' ),
		array( 'trust_icon' => 'chat', 'trust_item_title' => '24/7 Support', 'trust_item_text' => 'Dedicated assistance' ),
	);
}

$sub_state = isset( $_GET['sf_sub'] ) ? sanitize_key( wp_unslash( $_GET['sf_sub'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
?>
<footer class="rr-ft" style="<?php echo esc_attr( $style ); ?>">
	<div class="rr-ft__wrap">

		<?php if ( sf_opt( 'news_show', true ) ) : ?>
			<div class="rr-ft__news" id="sf-newsletter">
				<div>
					<div class="rr-ft__eyebrow"><?php echo esc_html( sf_opt( 'news_eyebrow', 'Join the Sunflorence circle' ) ); ?></div>
					<h2 class="rr-ft__news-title"><?php echo sf_inline( sf_opt( 'news_title', 'Beauty news, offers & <em>new arrivals</em>' ) ); // phpcs:ignore ?></h2>
					<p class="rr-ft__news-text"><?php echo esc_html( sf_opt( 'news_text', 'Sign up to get first dibs on new arrivals, sales, exclusive content, events and more!' ) ); ?></p>
				</div>
				<div>
					<?php if ( sf_opt( 'news_shortcode' ) ) : ?>
						<?php echo do_shortcode( sf_opt( 'news_shortcode' ) ); // phpcs:ignore ?>
					<?php else : ?>
						<form class="rr-ft__formwrap" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
							<input type="hidden" name="action" value="sf_subscribe">
							<?php wp_nonce_field( 'sf_subscribe', 'sf_nonce' ); ?>
							<input type="text" name="sf_website" value="" tabindex="-1" autocomplete="off" class="sf-hp" aria-hidden="true">
							<div class="rr-ft__form">
								<input type="email" name="sf_email" class="rr-ft__input" placeholder="<?php esc_attr_e( 'Enter your email address', 'sunflorence' ); ?>" aria-label="<?php esc_attr_e( 'Email address', 'sunflorence' ); ?>" autocomplete="email" required>
								<button type="submit" class="rr-ft__btn"><?php esc_html_e( 'Subscribe', 'sunflorence' ); ?> <?php echo sf_icon( 'arrow' ); // phpcs:ignore ?></button>
							</div>
						</form>
						<?php if ( 'ok' === $sub_state ) : ?>
							<p class="rr-ft__msg" role="status"><?php esc_html_e( 'Thanks for subscribing!', 'sunflorence' ); ?></p>
						<?php elseif ( $sub_state ) : ?>
							<p class="rr-ft__msg" role="alert"><?php esc_html_e( 'Please enter a valid email address.', 'sunflorence' ); ?></p>
						<?php endif; ?>
					<?php endif; ?>
					<div class="rr-ft__disclaimer"><p>
						<?php
						printf(
							/* translators: 1: privacy policy link, 2: terms link */
							esc_html__( 'By signing up you agree to our %1$s and %2$s.', 'sunflorence' ),
							'<a href="' . esc_url( get_privacy_policy_url() ? get_privacy_policy_url() : home_url( '/privacy-policy/' ) ) . '">' . esc_html__( 'Privacy Policy', 'sunflorence' ) . '</a>',
							'<a href="' . esc_url( function_exists( 'wc_terms_and_conditions_page_id' ) && wc_terms_and_conditions_page_id() ? get_permalink( wc_terms_and_conditions_page_id() ) : home_url( '/terms/' ) ) . '">' . esc_html__( 'Terms of Service', 'sunflorence' ) . '</a>'
						);
						?>
					</p></div>
				</div>
			</div>
		<?php endif; ?>

		<div class="rr-ft__main">

			<div class="rr-ft__col rr-ft__col--brand">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="rr-ft__logo" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
					<?php if ( $logo_id ) : ?>
						<?php echo sf_img( $logo_id, 'medium', array( 'alt' => get_bloginfo( 'name' ) ) ); // phpcs:ignore ?>
					<?php else : ?>
						<img src="<?php echo esc_url( sf_asset( 'images/logo-sunflorence-white.png' ) ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" loading="lazy">
					<?php endif; ?>
				</a>
				<p class="rr-ft__tagline"><?php echo esc_html( sf_opt( 'footer_tagline', 'Skin care, makeup, hair care, personal care and K-Beauty favourites, handpicked for your everyday routine.' ) ); ?></p>
				<ul class="rr-ft__contact">
					<?php if ( $email ) : ?><li><?php echo sf_icon( 'mail' ); // phpcs:ignore ?><a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a></li><?php endif; ?>
					<?php if ( $phone ) : ?><li><?php echo sf_icon( 'phone' ); // phpcs:ignore ?><a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></li><?php endif; ?>
					<?php if ( $address ) : ?><li class="rr-ft__contact-address"><?php echo sf_icon( 'pin' ); // phpcs:ignore ?><a href="<?php echo esc_url( 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( $address ) ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $address ); ?></a></li><?php endif; ?>
					<?php if ( $hours ) : ?><li><?php echo sf_icon( 'clock' ); // phpcs:ignore ?><?php echo esc_html( $hours ); ?></li><?php endif; ?>
				</ul>
				<?php if ( $socials ) : ?>
					<div class="rr-ft__socials">
						<?php foreach ( $socials as $net => $url ) : ?>
							<a href="<?php echo esc_url( $url ); ?>" aria-label="<?php echo esc_attr( ucfirst( $net ) ); ?>" target="_blank" rel="noopener"><?php echo sf_icon( $net ); // phpcs:ignore ?></a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

			<div class="rr-ft__col">
				<h3 class="rr-ft__title"><?php echo esc_html( sf_opt( 'footer_cat_title', 'Categories' ) ); ?></h3>
				<ul class="rr-ft__links<?php echo count( $cats ) > 7 ? ' rr-ft__links--two' : ''; ?>">
					<?php foreach ( $cats as $t ) : ?>
						<li><a href="<?php echo esc_url( get_term_link( $t ) ); ?>"><?php echo esc_html( $t->name ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>

			<div class="rr-ft__col">
				<h3 class="rr-ft__title"><?php echo esc_html( sf_opt( 'footer_menu_title', 'Customer Care' ) ); ?></h3>
				<?php
				if ( has_nav_menu( 'footer-menu' ) ) {
					wp_nav_menu( array( 'theme_location' => 'footer-menu', 'container' => false, 'menu_class' => 'rr-ft__links', 'depth' => 1 ) );
				} else {
					echo '<ul class="rr-ft__links">';
					foreach ( array( 'about' => 'About Us', 'contact' => 'Contact Us', 'faq' => 'FAQ', 'track-order' => 'Track Your Order', 'privacy-policy' => 'Privacy Policy' ) as $slug => $label ) {
						$page = get_page_by_path( $slug );
						if ( $page ) {
							echo '<li><a href="' . esc_url( get_permalink( $page ) ) . '">' . esc_html( $label ) . '</a></li>';
						}
					}
					echo '</ul>';
				}
				?>
			</div>

			<div class="rr-ft__col rr-ft__col--trust">
				<h3 class="rr-ft__title"><?php echo esc_html( sf_opt( 'trust_title', 'Shop with confidence' ) ); ?></h3>
				<ul class="rr-ft__trust">
					<?php foreach ( $trust as $t ) : ?>
						<li>
							<span class="rr-ft__trust-icon"><?php echo sf_icon( sf_row( $t, 'trust_icon', 'star' ) ); // phpcs:ignore ?></span>
							<div><strong><?php echo esc_html( sf_row( $t, 'trust_item_title' ) ); ?></strong><span><?php echo esc_html( sf_row( $t, 'trust_item_text' ) ); ?></span></div>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>

		<div class="rr-ft__bottom">
			<p class="rr-ft__copy"><?php echo esc_html( sf_opt( 'copyright', '© ' . gmdate( 'Y' ) . ' SUNFLORENCE LLC. All rights reserved.' ) ); ?></p>
			<?php if ( sf_opt( 'show_payment_icons', true ) ) : ?>
				<ul class="rr-ft__payments" aria-label="<?php esc_attr_e( 'Payment methods', 'sunflorence' ); ?>">
					<?php foreach ( array( 'Visa', 'Mastercard', 'Amex', 'PayPal', 'Apple Pay', 'Google Pay' ) as $pm ) : ?>
						<li class="rr-ft__pay"><?php echo esc_html( $pm ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>

	</div>
</footer>
