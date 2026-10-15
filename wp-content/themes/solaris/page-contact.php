<?php
/**
 * Template Name: Contact Page
 * Also auto-applies to any page with the slug "contact".
 *
 * @package Solaris
 */
get_header();

$phone   = solaris_get_option( 'contact_phone' );
$email   = solaris_get_option( 'contact_email' );
$address = solaris_get_option( 'contact_address' );
$hours   = solaris_get_option( 'contact_hours' );
?>
<main id="main">
	<section class="bg-dark section" style="padding-block:80px 56px;">
		<div class="container">
			<p class="eyebrow"><?php esc_html_e( 'Get In Touch', 'solaris' ); ?></p>
			<h1><?php the_title(); ?></h1>
			<p class="hero-lead" style="max-width:560px;"><?php esc_html_e( 'Questions about sizing a system, an order, or a product spec? Send us a message and our team will follow up.', 'solaris' ); ?></p>
		</div>
	</section>

	<section class="section">
		<div class="container">
			<div class="grid grid-2" style="align-items:start;">
				<div>
					<h2><?php esc_html_e( 'Send a Message', 'solaris' ); ?></h2>
					<?php get_template_part( 'template-parts/components/contact-form' ); ?>
				</div>
				<div>
					<h2><?php esc_html_e( 'Contact Information', 'solaris' ); ?></h2>
					<div class="grid" style="gap:20px;">
						<?php if ( $phone ) : ?>
							<div class="feature-card" style="display:flex;gap:16px;align-items:flex-start;padding:20px 24px;">
								<span style="color:var(--color-accent);"><?php echo solaris_icon( 'phone' ); ?></span>
								<div>
									<strong><?php esc_html_e( 'Phone', 'solaris' ); ?></strong>
									<p style="margin:4px 0 0;"><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></p>
								</div>
							</div>
						<?php endif; ?>
						<?php if ( $email ) : ?>
							<div class="feature-card" style="display:flex;gap:16px;align-items:flex-start;padding:20px 24px;">
								<span style="color:var(--color-accent);"><?php echo solaris_icon( 'mail' ); ?></span>
								<div>
									<strong><?php esc_html_e( 'Email', 'solaris' ); ?></strong>
									<p style="margin:4px 0 0;"><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></p>
								</div>
							</div>
						<?php endif; ?>
						<?php if ( $address ) : ?>
							<div class="feature-card" style="display:flex;gap:16px;align-items:flex-start;padding:20px 24px;">
								<span style="color:var(--color-accent);"><?php echo solaris_icon( 'map-pin' ); ?></span>
								<div>
									<strong><?php esc_html_e( 'Address', 'solaris' ); ?></strong>
									<p style="margin:4px 0 0;"><?php echo esc_html( $address ); ?></p>
								</div>
							</div>
						<?php endif; ?>
						<?php if ( $hours ) : ?>
							<div class="feature-card" style="display:flex;gap:16px;align-items:flex-start;padding:20px 24px;">
								<span style="color:var(--color-accent);"><?php echo solaris_icon( 'clock' ); ?></span>
								<div>
									<strong><?php esc_html_e( 'Business Hours', 'solaris' ); ?></strong>
									<p style="margin:4px 0 0;"><?php echo esc_html( $hours ); ?></p>
								</div>
							</div>
						<?php endif; ?>
						<?php if ( ! $phone && ! $email && ! $address ) : ?>
							<p class="text-small"><?php esc_html_e( 'Add your phone, email, address and hours in Appearance → Customize → Solaris Theme Options → Contact Information.', 'solaris' ); ?></p>
						<?php endif; ?>
					</div>

					<?php $socials = solaris_social_links(); ?>
					<?php if ( $socials ) : ?>
						<div class="footer-social" style="margin-top:28px;">
							<?php foreach ( $socials as $network => $url ) : ?>
								<a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( ucfirst( $network ) ); ?>" style="background:var(--color-background-alt);color:var(--color-heading);">
									<?php echo solaris_icon( $network ); ?>
								</a>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

					<?php if ( $address ) : ?>
						<div style="margin-top:28px;border-radius:var(--radius-lg);overflow:hidden;border:1px solid var(--color-border);">
							<iframe
								title="<?php esc_attr_e( 'Map location', 'solaris' ); ?>"
								src="https://maps.google.com/maps?q=<?php echo rawurlencode( $address ); ?>&output=embed"
								width="100%" height="260" style="border:0;display:block;" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
							</iframe>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</section>
</main>
<?php get_footer(); ?>
