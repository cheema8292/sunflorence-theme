<?php
/**
 * Contact form (Contact page) and order lookup (Track Order page).
 *
 * @package Sunflorence
 */

defined( 'ABSPATH' ) || exit;

/* ───────────── Contact form → email to the store ───────────── */

add_action( 'admin_post_nopriv_sf_contact', 'sf_handle_contact' );
add_action( 'admin_post_sf_contact', 'sf_handle_contact' );

function sf_handle_contact() {
	$back = wp_get_referer() ? wp_get_referer() : home_url( '/' );
	$back = remove_query_arg( array( 'sf_contact' ), $back );
	if ( ! isset( $_POST['sf_contact_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['sf_contact_nonce'] ), 'sf_contact' ) ) {
		wp_safe_redirect( add_query_arg( 'sf_contact', 'error', $back ) . '#rr-contact-form' );
		exit;
	}
	if ( ! empty( $_POST['sf_website'] ) ) { // honeypot: bots fill every field
		wp_safe_redirect( add_query_arg( 'sf_contact', 'sent', $back ) . '#rr-contact-form' );
		exit;
	}
	$c       = isset( $_POST['contact'] ) && is_array( $_POST['contact'] ) ? wp_unslash( $_POST['contact'] ) : array(); // phpcs:ignore
	$name    = sanitize_text_field( $c['name'] ?? '' );
	$email   = sanitize_email( $c['email'] ?? '' );
	$subject = sanitize_text_field( $c['subject'] ?? '' );
	$order   = sanitize_text_field( $c['order_number'] ?? '' );
	$body    = sanitize_textarea_field( $c['body'] ?? '' );
	if ( ! $name || ! is_email( $email ) || ! $body ) {
		wp_safe_redirect( add_query_arg( 'sf_contact', 'invalid', $back ) . '#rr-contact-form' );
		exit;
	}
	$to = sanitize_email( (string) sf_opt( 'contact_email', '' ) );
	$to = $to ? $to : get_option( 'admin_email' );
	$text = sprintf( "Name: %s\nEmail: %s\nOrder: %s\n\n%s", $name, $email, $order ? $order : '-', $body );
	$sent = wp_mail(
		$to,
		sprintf( '[%s] %s', wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ), $subject ? $subject : __( 'New contact message', 'sunflorence' ) ),
		$text,
		array( 'Reply-To: ' . $name . ' <' . $email . '>' )
	);
	wp_safe_redirect( add_query_arg( 'sf_contact', $sent ? 'sent' : 'error', $back ) . '#rr-contact-form' );
	exit;
}

/* ───────────── Track order: order number + billing email ───────────── */

/**
 * Look up an order the way WooCommerce's [woocommerce_order_tracking] does.
 *
 * @return WC_Order|string Order, or an error message.
 */
function sf_track_lookup() {
	if ( ! function_exists( 'wc_get_order' ) || empty( $_POST['sf_track'] ) ) {
		return '';
	}
	if ( ! isset( $_POST['sf_track_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['sf_track_nonce'] ), 'sf_track' ) ) {
		return __( 'Please try again.', 'sunflorence' );
	}
	$number = isset( $_POST['orderid'] ) ? ltrim( sanitize_text_field( wp_unslash( $_POST['orderid'] ) ), '#' ) : '';
	$email  = isset( $_POST['order_email'] ) ? sanitize_email( wp_unslash( $_POST['order_email'] ) ) : '';
	if ( ! $number || ! $email ) {
		return __( 'Please enter your order number and email address.', 'sunflorence' );
	}
	$order = wc_get_order( apply_filters( 'woocommerce_shortcode_order_tracking_order_id', $number ) );
	if ( $order && $order->get_id() && strtolower( $order->get_billing_email() ) === strtolower( $email ) ) {
		return $order;
	}
	return __( 'Sorry, we could not find an order with these details. Please check them, or contact us.', 'sunflorence' );
}
