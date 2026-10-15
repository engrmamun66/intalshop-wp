<?php
/**
 * Baseline hardening. Deliberately does not touch file-editing constants,
 * REST user enumeration, or XML-RPC wholesale — those are hosting/site
 * decisions, not something a theme should silently flip.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

remove_action( 'wp_head', 'wp_generator' );
add_filter( 'the_generator', '__return_empty_string' );

// Pingback amplification hardening: keep XML-RPC available (some plugins/apps
// rely on it) but drop the pingback-specific surface.
add_filter( 'wp_headers', function ( $headers ) {
	unset( $headers['X-Pingback'] );
	return $headers;
} );
add_filter( 'xmlrpc_methods', function ( $methods ) {
	unset( $methods['pingback.ping'], $methods['pingback.extensions.getPingbackURL'] );
	return $methods;
} );

/**
 * Escape hatch used by inc/contact-form.php: verifies both the nonce and a
 * hidden honeypot field before any input is sanitized/processed.
 */
function solaris_verify_form_submission( $nonce_action, $nonce_field = '_wpnonce' ) {
	if ( empty( $_POST[ $nonce_field ] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ $nonce_field ] ) ), $nonce_action ) ) {
		return false;
	}
	if ( ! empty( $_POST['solaris_hp_field'] ) ) {
		return false;
	}
	return true;
}
