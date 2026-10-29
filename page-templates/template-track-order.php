<?php
/**
 * Template Name: Track Order
 *
 * Shopify sections/rr-track-order.liquid — here the form really looks the order up
 * (order number + billing email) and shows its status and the shipping updates
 * added as "note to customer" on the order.
 *
 * @package Sunflorence
 */

defined( 'ABSPATH' ) || exit;

get_header();
sf_ctx( 'post:track-order' );

$result  = sf_track_lookup();
$support = sf_s( 'support_email', sf_opt( 'contact_email', get_option( 'admin_email' ) ) );
$account = sf_s( 'account_url', function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : '' );
$account = preg_replace( '#^/account(/login)?#', function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : '/', (string) $account );
$steps   = array(
	array( __( 'Order Confirmed', 'sunflorence' ), __( 'You receive an order confirmation email immediately after purchase.', 'sunflorence' ) ),
	array( __( 'Order Shipped', 'sunflorence' ), __( 'Once your order ships, you will receive a tracking number by email.', 'sunflorence' ) ),
	array( __( 'Out for Delivery', 'sunflorence' ), __( 'Standard delivery takes 5-7 business days. Express options are available at checkout.', 'sunflorence' ) ),
	array( __( 'Delivered', 'sunflorence' ), __( 'Your Sunflorence order arrives at your door. Enjoy your new favourites!', 'sunflorence' ) ),
);
$box = 'margin-top:20px; padding:16px 20px; background:rgba(110,31,47,0.06); border:1px solid rgba(110,31,47,0.2); border-radius:10px; font-size:13px; color:#555;';
?>
<div class="rr-track" style="<?php echo esc_attr( sf_vars( array( 'body_bg' ) ) ); ?>">
	<div class="rr-track__hero">
		<div class="rr-track__eyebrow"><?php echo esc_html( sf_s( 'eyebrow' ) ); ?></div>
		<h1 class="rr-track__heading">
			<?php echo esc_html( sf_s( 'heading' ) ); ?>
			<?php if ( sf_s( 'heading_italic' ) ) : ?><br><em><?php echo esc_html( sf_s( 'heading_italic' ) ); ?></em><?php endif; ?>
		</h1>
		<?php if ( sf_s( 'subtext' ) ) : ?><p class="rr-track__sub"><?php echo esc_html( sf_s( 'subtext' ) ); ?></p><?php endif; ?>
	</div>

	<div class="rr-track__body">
		<div class="rr-track__body-inner">
			<div class="rr-track__card">
				<div class="rr-track__card-title"><?php esc_html_e( 'Track Your Package', 'sunflorence' ); ?></div>
				<div class="rr-track__card-sub"><?php esc_html_e( 'Enter your order number and the email you used at checkout to see the latest status of your order.', 'sunflorence' ); ?></div>
				<form method="post" action="#rr-track-result">
					<?php wp_nonce_field( 'sf_track', 'sf_track_nonce' ); ?>
					<div class="rr-track__field">
						<label class="rr-track__label" for="rr-order-number"><?php esc_html_e( 'Order Number', 'sunflorence' ); ?></label>
						<input type="text" id="rr-order-number" name="orderid" class="rr-track__input" placeholder="#1234" required value="<?php echo isset( $_POST['orderid'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_POST['orderid'] ) ) ) : ''; // phpcs:ignore ?>">
					</div>
					<div class="rr-track__field">
						<label class="rr-track__label" for="rr-email-track"><?php esc_html_e( 'Email Address', 'sunflorence' ); ?></label>
						<input type="email" id="rr-email-track" name="order_email" class="rr-track__input" placeholder="your@email.com" autocomplete="email" required value="<?php echo isset( $_POST['order_email'] ) ? esc_attr( sanitize_email( wp_unslash( $_POST['order_email'] ) ) ) : ''; // phpcs:ignore ?>">
					</div>
					<button type="submit" name="sf_track" value="1" class="rr-track__btn"><span><?php echo esc_html( sf_s( 'btn_text' ) ); ?></span></button>
				</form>

				<?php if ( $result instanceof WC_Order ) : ?>
					<div id="rr-track-result" style="<?php echo esc_attr( $box ); ?>" role="status">
						<p style="margin:0 0 6px"><strong><?php echo esc_html( sprintf( __( 'Order #%s', 'sunflorence' ), $result->get_order_number() ) ); ?></strong> — <?php echo esc_html( wc_get_order_status_name( $result->get_status() ) ); ?></p>
						<p style="margin:0"><?php echo esc_html( sprintf( __( 'Placed on %s', 'sunflorence' ), wc_format_datetime( $result->get_date_created() ) ) ); ?></p>
						<?php
						$notes = $result->get_customer_order_notes();
						if ( $notes ) :
							?>
							<ul style="margin:12px 0 0;padding-left:18px">
								<?php foreach ( $notes as $note ) : ?>
									<li style="margin-bottom:6px"><strong><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $note->comment_date ) ) ); ?>:</strong> <?php echo wp_kses_post( make_clickable( wpautop( wptexturize( $note->comment_content ) ) ) ); ?></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</div>
				<?php elseif ( $result ) : ?>
					<div id="rr-track-result" style="<?php echo esc_attr( $box ); ?>" role="alert">
						<?php echo esc_html( $result ); ?>
						<?php if ( $support ) : ?>
							<a href="mailto:<?php echo esc_attr( antispambot( $support ) ); ?>" style="color:#6E1F2F;"><?php echo esc_html( antispambot( $support ) ); ?></a>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php if ( $account ) : ?>
					<div class="rr-track__divider"><?php esc_html_e( 'or', 'sunflorence' ); ?></div>
					<a href="<?php echo esc_url( $account ); ?>" class="rr-track__alt-link"><?php esc_html_e( 'Log in to your account', 'sunflorence' ); ?></a>
				<?php endif; ?>
			</div>

			<div class="rr-track__steps">
				<div class="rr-track__steps-title"><?php esc_html_e( 'How Tracking Works', 'sunflorence' ); ?></div>
				<div class="rr-track__step-list">
					<?php foreach ( $steps as $i => $st ) : ?>
						<div class="rr-track__step">
							<div class="rr-track__step-num"><?php echo esc_html( $i + 1 ); ?></div>
							<div>
								<div class="rr-track__step-title"><?php echo esc_html( $st[0] ); ?></div>
								<div class="rr-track__step-text"><?php echo esc_html( $st[1] ); ?></div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
sf_ctx( 'row' );
get_footer();
