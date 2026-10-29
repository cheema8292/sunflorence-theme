<?php
/**
 * Template Name: Contact
 *
 * Shopify sections/rr-contact.liquid. Messages are emailed to the contact email
 * (Sunflorence → Footer → Contact email, else the site admin email).
 *
 * @package Sunflorence
 */

defined( 'ABSPATH' ) || exit;

get_header();
sf_ctx( 'post:contact' );

$email  = sf_s( 'email', sf_opt( 'contact_email', get_option( 'admin_email' ) ) );
$status = isset( $_GET['sf_contact'] ) ? sanitize_key( $_GET['sf_contact'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
?>
<div class="rr-contact" style="<?php echo esc_attr( sf_vars( array( 'body_bg' ) ) ); ?>">
	<div class="rr-contact__hero">
		<div class="rr-contact__eyebrow"><?php echo esc_html( sf_s( 'eyebrow' ) ); ?></div>
		<h1 class="rr-contact__hero-title">
			<?php echo esc_html( sf_s( 'heading' ) ); ?><?php if ( sf_s( 'heading_italic' ) ) : ?><em><?php echo esc_html( sf_s( 'heading_italic' ) ); ?></em><?php endif; ?>
		</h1>
		<?php if ( sf_s( 'subtext' ) ) : ?><p class="rr-contact__hero-sub"><?php echo esc_html( sf_s( 'subtext' ) ); ?></p><?php endif; ?>
	</div>

	<div class="rr-contact__body">
		<div class="rr-contact__body-inner">
			<div class="rr-contact__sidebar">
				<h2 class="rr-contact__info-heading"><?php echo wp_kses( __( 'Get in<br>Touch', 'sunflorence' ), array( 'br' => array() ) ); ?></h2>
				<p class="rr-contact__info-sub"><?php esc_html_e( "We're here to help with any questions about your order, products, or anything else.", 'sunflorence' ); ?></p>
				<div class="rr-contact__info-cards">
					<?php if ( $email ) : ?>
						<div class="rr-contact__info-card">
							<div class="rr-contact__info-card-icon"><svg viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></div>
							<div>
								<div class="rr-contact__info-label"><?php esc_html_e( 'Email', 'sunflorence' ); ?></div>
								<div class="rr-contact__info-value"><a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a></div>
							</div>
						</div>
					<?php endif; ?>
					<?php if ( sf_s( 'hours' ) ) : ?>
						<div class="rr-contact__info-card">
							<div class="rr-contact__info-card-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
							<div>
								<div class="rr-contact__info-label"><?php esc_html_e( 'Business Hours', 'sunflorence' ); ?></div>
								<div class="rr-contact__info-value"><?php echo esc_html( sf_s( 'hours' ) ); ?></div>
							</div>
						</div>
					<?php endif; ?>
					<?php if ( sf_s( 'response_time' ) ) : ?>
						<div class="rr-contact__info-card">
							<div class="rr-contact__info-card-icon"><svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></div>
							<div>
								<div class="rr-contact__info-label"><?php esc_html_e( 'Response Time', 'sunflorence' ); ?></div>
								<div class="rr-contact__info-value"><?php echo esc_html( sf_s( 'response_time' ) ); ?></div>
							</div>
						</div>
					<?php endif; ?>
				</div>
				<div class="rr-contact__trust">
					<div class="rr-contact__trust-item">
						<span class="rr-contact__trust-icon"><svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></span>
						<span class="rr-contact__trust-text"><strong><?php esc_html_e( '30-Day Returns', 'sunflorence' ); ?></strong> — <?php esc_html_e( 'On all items', 'sunflorence' ); ?></span>
					</div>
					<div class="rr-contact__trust-item">
						<span class="rr-contact__trust-icon"><svg viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13" rx="1"/><path d="M16 8h4l3 5v3h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg></span>
						<span class="rr-contact__trust-text"><strong><?php esc_html_e( 'Tracked Shipping', 'sunflorence' ); ?></strong> — <?php esc_html_e( 'To over 50 countries', 'sunflorence' ); ?></span>
					</div>
					<div class="rr-contact__trust-item">
						<span class="rr-contact__trust-icon"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></span>
						<span class="rr-contact__trust-text"><strong><?php esc_html_e( '24/7 Support', 'sunflorence' ); ?></strong> — <?php esc_html_e( 'Dedicated assistance', 'sunflorence' ); ?></span>
					</div>
				</div>
			</div>

			<div class="rr-contact__form-wrap">
				<h2 class="rr-contact__form-heading"><?php esc_html_e( 'Send a Message', 'sunflorence' ); ?></h2>
				<p class="rr-contact__form-sub"><?php esc_html_e( "Fill out the form and we'll be in touch shortly.", 'sunflorence' ); ?></p>
				<form id="rr-contact-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="sf_contact">
					<?php wp_nonce_field( 'sf_contact', 'sf_contact_nonce' ); ?>
					<div style="position:absolute;left:-9999px" aria-hidden="true"><input type="text" name="sf_website" tabindex="-1" autocomplete="off"></div>
					<?php if ( 'sent' === $status ) : ?>
						<div class="rr-contact__success" role="status">
							<svg viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
							<span><?php esc_html_e( "Thank you! We'll get back to you within 24 hours.", 'sunflorence' ); ?></span>
						</div>
					<?php elseif ( $status ) : ?>
						<div class="rr-contact__success rr-contact__error" role="alert">
							<span><?php echo 'invalid' === $status ? esc_html__( 'Please fill in your name, a valid email and your message.', 'sunflorence' ) : esc_html__( 'Sorry, your message could not be sent. Please email us directly.', 'sunflorence' ); ?></span>
						</div>
					<?php endif; ?>
					<div class="rr-contact__row">
						<div class="rr-contact__field">
							<label class="rr-contact__label" for="rr-name"><?php esc_html_e( 'Your Name', 'sunflorence' ); ?></label>
							<input type="text" id="rr-name" name="contact[name]" class="rr-contact__input" placeholder="Jane Smith" required autocomplete="name">
						</div>
						<div class="rr-contact__field">
							<label class="rr-contact__label" for="rr-email"><?php esc_html_e( 'Email Address', 'sunflorence' ); ?></label>
							<input type="email" id="rr-email" name="contact[email]" class="rr-contact__input" placeholder="jane@email.com" required autocomplete="email">
						</div>
					</div>
					<div class="rr-contact__field">
						<label class="rr-contact__label" for="rr-subject"><?php esc_html_e( 'Subject', 'sunflorence' ); ?></label>
						<input type="text" id="rr-subject" name="contact[subject]" class="rr-contact__input" placeholder="<?php esc_attr_e( 'How can we help?', 'sunflorence' ); ?>">
					</div>
					<div class="rr-contact__field">
						<label class="rr-contact__label" for="rr-order"><?php esc_html_e( 'Order Number (optional)', 'sunflorence' ); ?></label>
						<input type="text" id="rr-order" name="contact[order_number]" class="rr-contact__input" placeholder="#1234">
					</div>
					<div class="rr-contact__field">
						<label class="rr-contact__label" for="rr-message"><?php esc_html_e( 'Message', 'sunflorence' ); ?></label>
						<textarea id="rr-message" name="contact[body]" class="rr-contact__textarea" placeholder="<?php esc_attr_e( 'Tell us how we can help you...', 'sunflorence' ); ?>" required></textarea>
					</div>
					<button type="submit" class="rr-contact__submit">
						<span><?php echo esc_html( sf_s( 'btn_text' ) ); ?></span>
						<svg viewBox="0 0 24 24"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
					</button>
				</form>
			</div>
		</div>
	</div>
</div>
<?php
sf_ctx( 'row' );
get_footer();
