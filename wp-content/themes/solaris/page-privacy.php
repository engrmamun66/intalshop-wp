<?php
/**
 * Template Name: Privacy Policy
 * Also auto-applies to any page with the slug "privacy".
 *
 * IMPORTANT: Placeholder privacy copy for scaffolding only — does not claim
 * compliance with GDPR/CCPA or any other specific regulation. Populate this
 * using WordPress's built-in Privacy Policy guide (Settings → Privacy) and
 * have it reviewed by qualified legal counsel before publishing.
 *
 * @package Solaris
 */
get_header();
?>
<main id="main" class="container section" style="padding-top:32px;max-width:820px;">
	<div class="notice woocommerce-info" style="margin-bottom:32px;">
		<?php esc_html_e( 'Placeholder content. Use Settings → Privacy in wp-admin to generate a policy that reflects the plugins actually active on this site, then have it reviewed by a qualified legal professional.', 'solaris' ); ?>
	</div>

	<h1><?php the_title(); ?></h1>

	<h2><?php esc_html_e( 'Information We Collect', 'solaris' ); ?></h2>
	<p><?php esc_html_e( 'We collect information you provide directly (such as account, order and contact-form details) and information collected automatically (such as browsing behavior).', 'solaris' ); ?></p>

	<h2><?php esc_html_e( 'Account Information', 'solaris' ); ?></h2>
	<p><?php esc_html_e( 'If you create an account, we store your name, email address and order history to support your use of the store.', 'solaris' ); ?></p>

	<h2><?php esc_html_e( 'Order Information', 'solaris' ); ?></h2>
	<p><?php esc_html_e( 'We collect billing and shipping details necessary to process and fulfill your order through WooCommerce.', 'solaris' ); ?></p>

	<h2><?php esc_html_e( 'Payment Processing', 'solaris' ); ?></h2>
	<p><?php esc_html_e( 'Payments are handled by the payment gateway(s) configured in WooCommerce settings. We do not store full payment card details on this server.', 'solaris' ); ?></p>

	<h2><?php esc_html_e( 'Cookies', 'solaris' ); ?></h2>
	<p><?php esc_html_e( 'This site uses cookies required for cart and checkout functionality, and may use additional cookies for analytics if configured.', 'solaris' ); ?></p>

	<h2><?php esc_html_e( 'Analytics', 'solaris' ); ?></h2>
	<p><?php esc_html_e( 'List any analytics tools actually installed on this site (e.g. an analytics plugin) and what they collect.', 'solaris' ); ?></p>

	<h2><?php esc_html_e( 'Marketing', 'solaris' ); ?></h2>
	<p><?php esc_html_e( 'Describe any newsletter or marketing communications customers may opt into, and how to opt out.', 'solaris' ); ?></p>

	<h2><?php esc_html_e( 'Third-Party Services', 'solaris' ); ?></h2>
	<p><?php esc_html_e( 'List third-party services actually in use (payment gateways, shipping carriers, analytics, email providers) and link to their own privacy policies.', 'solaris' ); ?></p>

	<h2><?php esc_html_e( 'Data Retention', 'solaris' ); ?></h2>
	<p><?php esc_html_e( 'Order and account data is retained for as long as needed for legal, accounting or business purposes.', 'solaris' ); ?></p>

	<h2><?php esc_html_e( 'Your Rights', 'solaris' ); ?></h2>
	<p><?php esc_html_e( 'Depending on your location, you may have the right to access, correct, export or delete your personal data. Contact us to make a request.', 'solaris' ); ?></p>

	<h2><?php esc_html_e( 'Security', 'solaris' ); ?></h2>
	<p><?php esc_html_e( 'We apply reasonable technical and organizational measures to protect your data, but no method of transmission or storage is 100% secure.', 'solaris' ); ?></p>

	<h2><?php esc_html_e( 'Contact Information', 'solaris' ); ?></h2>
	<p>
		<?php
		$email = solaris_get_option( 'contact_email' );
		if ( $email ) {
			printf(
				/* translators: %s: contact email address. */
				esc_html__( 'Privacy-related questions can be sent to %s.', 'solaris' ),
				'<a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a>'
			);
		} else {
			esc_html_e( 'Add a contact email in Appearance → Customize → Solaris Theme Options → Contact Information.', 'solaris' );
		}
		?>
	</p>
</main>
<?php get_footer(); ?>
