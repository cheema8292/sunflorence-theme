<?php
/**
 * Built-in newsletter form for the footer (used when no form shortcode is set).
 * Emails are stored in an option and listed under Sunflorence → Subscribers (CSV export).
 *
 * @package Sunflorence
 */

defined( 'ABSPATH' ) || exit;

add_action( 'admin_post_nopriv_sf_subscribe', 'sf_handle_subscribe' );
add_action( 'admin_post_sf_subscribe', 'sf_handle_subscribe' );

function sf_handle_subscribe() {
	$back = wp_get_referer() ? wp_get_referer() : home_url( '/' );

	if ( ! isset( $_POST['sf_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['sf_nonce'] ) ), 'sf_subscribe' ) ) {
		wp_safe_redirect( add_query_arg( 'sf_sub', 'error', $back ) . '#sf-newsletter' );
		exit;
	}
	// Honeypot against bots
	if ( ! empty( $_POST['sf_website'] ) ) {
		wp_safe_redirect( add_query_arg( 'sf_sub', 'ok', $back ) . '#sf-newsletter' );
		exit;
	}

	$email = isset( $_POST['sf_email'] ) ? sanitize_email( wp_unslash( $_POST['sf_email'] ) ) : '';
	if ( ! is_email( $email ) ) {
		wp_safe_redirect( add_query_arg( 'sf_sub', 'invalid', $back ) . '#sf-newsletter' );
		exit;
	}

	$list = get_option( 'sf_subscribers', array() );
	if ( ! isset( $list[ $email ] ) ) {
		$list[ $email ] = current_time( 'mysql' );
		update_option( 'sf_subscribers', $list, false );
	}
	wp_safe_redirect( add_query_arg( 'sf_sub', 'ok', $back ) . '#sf-newsletter' );
	exit;
}

/* Admin page: list + CSV export */
add_action( 'admin_menu', function () {
	add_submenu_page(
		function_exists( 'acf_add_options_page' ) ? 'sunflorence-settings' : 'themes.php',
		__( 'Subscribers', 'sunflorence' ),
		__( 'Subscribers', 'sunflorence' ),
		'manage_options',
		'sf-subscribers',
		'sf_subscribers_page'
	);
}, 20 );

add_action( 'admin_init', function () {
	if ( isset( $_GET['page'], $_GET['sf_export'] ) && 'sf-subscribers' === $_GET['page'] && current_user_can( 'manage_options' ) ) {
		check_admin_referer( 'sf_export' );
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=sunflorence-subscribers.csv' );
		$out = fopen( 'php://output', 'w' );
		fputcsv( $out, array( 'email', 'subscribed' ) );
		foreach ( get_option( 'sf_subscribers', array() ) as $email => $date ) {
			fputcsv( $out, array( $email, $date ) );
		}
		fclose( $out );
		exit;
	}
} );

function sf_subscribers_page() {
	$list = get_option( 'sf_subscribers', array() );
	echo '<div class="wrap"><h1>' . esc_html__( 'Newsletter subscribers', 'sunflorence' ) . ' (' . count( $list ) . ')</h1>';
	echo '<p><a class="button button-primary" href="' . esc_url( wp_nonce_url( admin_url( 'admin.php?page=sf-subscribers&sf_export=1' ), 'sf_export' ) ) . '">' . esc_html__( 'Export CSV', 'sunflorence' ) . '</a></p>';
	echo '<table class="widefat striped"><thead><tr><th>Email</th><th>Date</th></tr></thead><tbody>';
	foreach ( array_reverse( $list, true ) as $email => $date ) {
		echo '<tr><td>' . esc_html( $email ) . '</td><td>' . esc_html( $date ) . '</td></tr>';
	}
	echo '</tbody></table></div>';
}
