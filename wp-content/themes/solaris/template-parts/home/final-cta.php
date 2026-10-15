<?php
/**
 * Large closing CTA before the footer.
 *
 * @package Solaris
 */
?>
<section class="section">
	<div class="container">
		<div class="cta-banner bg-dark reveal final-cta">
			<h2><?php echo esc_html( solaris_get_option( 'final_cta_heading' ) ); ?></h2>
			<p><?php echo esc_html( solaris_get_option( 'final_cta_text' ) ); ?></p>
			<div class="hero-actions">
				<a class="button" href="<?php echo esc_url( solaris_cta_url( 'final_cta_button_url' ) ); ?>">
					<?php echo esc_html( solaris_get_option( 'final_cta_button_text' ) ); ?>
				</a>
			</div>
		</div>
	</div>
</section>
