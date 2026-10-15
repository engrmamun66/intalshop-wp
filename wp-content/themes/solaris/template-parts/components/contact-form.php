<?php
/**
 * Contact form markup. Posts to admin-post.php (inc/contact-form.php),
 * protected by a nonce + honeypot field, all input sanitized server-side.
 *
 * @package Solaris
 */
$status = isset( $_GET['solaris_contact'] ) ? sanitize_key( wp_unslash( $_GET['solaris_contact'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
?>
<?php if ( 'success' === $status ) : ?>
	<div class="notice woocommerce-message" role="status"><?php esc_html_e( 'Thanks — your message has been sent. We\'ll get back to you soon.', 'solaris' ); ?></div>
<?php elseif ( 'invalid' === $status ) : ?>
	<div class="notice woocommerce-error" role="alert"><?php esc_html_e( 'Please fill in your name, a valid email address, and a message.', 'solaris' ); ?></div>
<?php elseif ( 'error' === $status ) : ?>
	<div class="notice woocommerce-error" role="alert"><?php esc_html_e( 'Something went wrong sending your message. Please try again or email us directly.', 'solaris' ); ?></div>
<?php endif; ?>

<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate>
	<input type="hidden" name="action" value="solaris_contact" />
	<?php wp_nonce_field( 'solaris_contact_form' ); ?>
	<p class="honeypot-field" aria-hidden="true">
		<label for="solaris_hp_field"><?php esc_html_e( 'Leave this field empty', 'solaris' ); ?></label>
		<input type="text" id="solaris_hp_field" name="solaris_hp_field" tabindex="-1" autocomplete="off" />
	</p>

	<div class="form-row form-row--split">
		<div>
			<label for="contact_name"><?php esc_html_e( 'Full Name', 'solaris' ); ?> <span class="form-required">*</span></label>
			<input type="text" id="contact_name" name="contact_name" required autocomplete="name" />
		</div>
		<div>
			<label for="contact_email"><?php esc_html_e( 'Email Address', 'solaris' ); ?> <span class="form-required">*</span></label>
			<input type="email" id="contact_email" name="contact_email" required autocomplete="email" />
		</div>
	</div>
	<div class="form-row">
		<label for="contact_phone"><?php esc_html_e( 'Phone Number (optional)', 'solaris' ); ?></label>
		<input type="tel" id="contact_phone" name="contact_phone" autocomplete="tel" />
	</div>
	<div class="form-row">
		<label for="contact_message"><?php esc_html_e( 'Message', 'solaris' ); ?> <span class="form-required">*</span></label>
		<textarea id="contact_message" name="contact_message" rows="5" required></textarea>
	</div>

	<button type="submit" class="button btn--block"><?php esc_html_e( 'Send Message', 'solaris' ); ?></button>
</form>
