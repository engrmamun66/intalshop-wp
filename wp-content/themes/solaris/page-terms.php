<?php
/**
 * Template Name: Terms & Conditions
 * Also auto-applies to any page with the slug "terms".
 *
 * IMPORTANT: The body text below is generic placeholder legal copy for
 * scaffolding purposes only. It has not been reviewed by a lawyer and must
 * not be published as-is — have qualified legal counsel review and adapt it
 * to your jurisdiction and business before this page goes live.
 *
 * @package Solaris
 */
get_header();
?>
<main id="main" class="container section" style="padding-top:32px;max-width:820px;">
	<div class="notice woocommerce-info" style="margin-bottom:32px;">
		<?php esc_html_e( 'Placeholder legal content — have this reviewed and customized by a qualified legal professional before publishing.', 'solaris' ); ?>
	</div>

	<h1><?php the_title(); ?></h1>

	<h2><?php esc_html_e( '1. Introduction', 'solaris' ); ?></h2>
	<p><?php esc_html_e( 'These Terms & Conditions govern your use of this website and any purchases made through it. By accessing the site or placing an order, you agree to these terms.', 'solaris' ); ?></p>

	<h2><?php esc_html_e( '2. Definitions', 'solaris' ); ?></h2>
	<p><?php esc_html_e( '"We", "us" and "our" refer to this store. "You" refers to the visitor or customer using the site.', 'solaris' ); ?></p>

	<h2><?php esc_html_e( '3. Website Usage', 'solaris' ); ?></h2>
	<p><?php esc_html_e( 'You agree to use this website only for lawful purposes and in a way that does not infringe the rights of others or restrict their use of the site.', 'solaris' ); ?></p>

	<h2><?php esc_html_e( '4. Products', 'solaris' ); ?></h2>
	<p><?php esc_html_e( 'We make reasonable efforts to display product specifications (wattage, voltage, dimensions, etc.) accurately. Manufacturer specifications take precedence in case of discrepancy.', 'solaris' ); ?></p>

	<h2><?php esc_html_e( '5. Pricing', 'solaris' ); ?></h2>
	<p><?php esc_html_e( 'All prices are listed in the store\'s configured currency and may change without notice. Prices displayed at checkout are final for that order.', 'solaris' ); ?></p>

	<h2><?php esc_html_e( '6. Orders & Payments', 'solaris' ); ?></h2>
	<p><?php esc_html_e( 'Orders are processed through our WooCommerce checkout and configured payment gateways. We reserve the right to refuse or cancel any order at our discretion.', 'solaris' ); ?></p>

	<h2><?php esc_html_e( '7. Shipping', 'solaris' ); ?></h2>
	<p><?php esc_html_e( 'Shipping methods, costs and estimated delivery times are shown at checkout based on your location and the shipping zones configured in the store.', 'solaris' ); ?></p>

	<h2><?php esc_html_e( '8. Returns', 'solaris' ); ?></h2>
	<p><?php esc_html_e( 'Describe your return window, condition requirements, and refund process here.', 'solaris' ); ?></p>

	<h2><?php esc_html_e( '9. Warranty', 'solaris' ); ?></h2>
	<p><?php esc_html_e( 'Warranty terms vary by manufacturer and product; refer to individual product pages for specific coverage.', 'solaris' ); ?></p>

	<h2><?php esc_html_e( '10. Intellectual Property', 'solaris' ); ?></h2>
	<p><?php esc_html_e( 'All content on this site, including text, images and branding, is owned by or licensed to us and may not be reused without permission.', 'solaris' ); ?></p>

	<h2><?php esc_html_e( '11. Limitation of Liability', 'solaris' ); ?></h2>
	<p><?php esc_html_e( 'To the fullest extent permitted by law, we are not liable for indirect or consequential losses arising from use of this website or its products.', 'solaris' ); ?></p>

	<h2><?php esc_html_e( '12. Changes to These Terms', 'solaris' ); ?></h2>
	<p><?php esc_html_e( 'We may update these terms from time to time. Continued use of the site after changes constitutes acceptance of the updated terms.', 'solaris' ); ?></p>

	<h2><?php esc_html_e( '13. Contact Information', 'solaris' ); ?></h2>
	<p>
		<?php
		$email = solaris_get_option( 'contact_email' );
		if ( $email ) {
			printf(
				/* translators: %s: contact email address. */
				esc_html__( 'Questions about these terms can be sent to %s.', 'solaris' ),
				'<a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a>'
			);
		} else {
			esc_html_e( 'Add a contact email in Appearance → Customize → Solaris Theme Options → Contact Information.', 'solaris' );
		}
		?>
	</p>
</main>
<?php get_footer(); ?>
