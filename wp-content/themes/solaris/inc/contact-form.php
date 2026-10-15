<?php
/**
 * Minimal, dependency-free contact form handler. No contact-form plugin is
 * installed in this environment; rather than leave the Contact page inert,
 * this wires template-parts/components/contact-form.php to admin-post.php
 * with nonce + honeypot + full sanitization before anything touches wp_mail().
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function solaris_handle_contact_form() {
	$redirect_base = wp_get_referer() ? wp_get_referer() : home_url( '/contact/' );

	if ( ! solaris_verify_form_submission( 'solaris_contact_form' ) ) {
		wp_safe_redirect( add_query_arg( 'solaris_contact', 'error', $redirect_base ) );
		exit;
	}

	$name    = isset( $_POST['contact_name'] ) ? sanitize_text_field( wp_unslash( $_POST['contact_name'] ) ) : '';
	$email   = isset( $_POST['contact_email'] ) ? sanitize_email( wp_unslash( $_POST['contact_email'] ) ) : '';
	$phone   = isset( $_POST['contact_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['contact_phone'] ) ) : '';
	$message = isset( $_POST['contact_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['contact_message'] ) ) : '';

	if ( '' === $name || ! is_email( $email ) || '' === $message ) {
		wp_safe_redirect( add_query_arg( 'solaris_contact', 'invalid', $redirect_base ) );
		exit;
	}

	$to      = get_option( 'admin_email' );
	$subject = sprintf( /* translators: %s: site name. */ __( 'New contact form message — %s', 'solaris' ), wp_specialchars_decode( get_bloginfo( 'name' ) ) );

	$body  = __( 'New message submitted from the website contact form.', 'solaris' ) . "\n\n";
	$body .= __( 'Name:', 'solaris' ) . ' ' . $name . "\n";
	$body .= __( 'Email:', 'solaris' ) . ' ' . $email . "\n";
	if ( $phone ) {
		$body .= __( 'Phone:', 'solaris' ) . ' ' . $phone . "\n";
	}
	$body .= "\n" . __( 'Message:', 'solaris' ) . "\n" . $message;

	$headers = array(
		'Content-Type: text/plain; charset=UTF-8',
		'Reply-To: ' . $name . ' <' . $email . '>',
	);

	$sent = wp_mail( $to, $subject, $body, $headers );

	wp_safe_redirect( add_query_arg( 'solaris_contact', $sent ? 'success' : 'error', $redirect_base ) );
	exit;
}
add_action( 'admin_post_solaris_contact', 'solaris_handle_contact_form' );
add_action( 'admin_post_nopriv_solaris_contact', 'solaris_handle_contact_form' );
