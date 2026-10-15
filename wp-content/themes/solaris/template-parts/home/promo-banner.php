<?php
/**
 * Gradient promotional CTA (mid-page).
 *
 * @package Solaris
 */
?>
<section class="section">
	<div class="container">
		<div class="cta-banner bg-gradient reveal">
			<h2><?php echo esc_html( solaris_get_option( 'promo_heading' ) ); ?></h2>
			<p><?php echo esc_html( solaris_get_option( 'promo_text' ) ); ?></p>
			<div class="hero-actions">
				<a class="button" style="background-image:none;background-color:var(--color-white);color:var(--color-dark-green);" href="<?php echo esc_url( solaris_cta_url( 'promo_button_url' ) ); ?>">
					<?php echo esc_html( solaris_get_option( 'promo_button_text' ) ); ?>
				</a>
			</div>
		</div>
	</div>
</section>
