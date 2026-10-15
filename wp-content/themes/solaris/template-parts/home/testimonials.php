<?php
/**
 * Testimonials, backed by the `solar_testimonial` CPT. Falls back to clearly
 * marked placeholder entries when no real testimonials exist yet — never
 * invents a named customer. Section/card styling is Customizer-driven
 * (see inc/customizer.php).
 *
 * @package Solaris
 */
$testimonials = solaris_get_testimonials( 6 );

$section_classes = array(
	'section',
	'has-section-bg',
	'testimonials-section',
	'testimonials-hover-' . solaris_get_option( 'testimonials_card_hover_effect' ),
	'testimonials-anim-' . solaris_get_option( 'testimonials_card_animation' ),
);
?>
<section class="<?php echo esc_attr( implode( ' ', $section_classes ) ); ?>" id="testimonials">
	<?php solaris_section_bg_image( 'testimonials' ); ?>
	<div class="container">
		<div class="section-header is-centered">
			<p class="eyebrow"><?php esc_html_e( 'Customer Stories', 'solaris' ); ?></p>
			<h2><?php echo esc_html( solaris_get_option( 'testimonials_title' ) ); ?></h2>
		</div>
		<div class="grid grid-3">
			<?php if ( ! empty( $testimonials ) ) : ?>
				<?php foreach ( $testimonials as $testimonial ) : ?>
					<div class="testimonial-card reveal">
						<p class="testimonial-card__quote"><?php echo esc_html( wp_strip_all_tags( $testimonial->post_content ) ); ?></p>
						<div class="testimonial-card__author">
							<span class="testimonial-card__avatar">
								<?php if ( has_post_thumbnail( $testimonial ) ) : ?>
									<?php echo get_the_post_thumbnail( $testimonial, 'thumbnail' ); ?>
								<?php else : ?>
									<?php echo esc_html( mb_substr( get_the_title( $testimonial ), 0, 1 ) ); ?>
								<?php endif; ?>
							</span>
							<span>
								<span class="testimonial-card__name"><?php echo esc_html( get_the_title( $testimonial ) ); ?></span><br />
								<span class="testimonial-card__role"><?php echo esc_html( get_post_meta( $testimonial->ID, '_solaris_role', true ) ); ?></span>
							</span>
						</div>
					</div>
				<?php endforeach; ?>
			<?php else : ?>
				<?php
				$placeholders = array(
					array( __( 'Placeholder Customer', 'solaris' ), __( 'Homeowner (placeholder)', 'solaris' ), __( 'The install was smooth and our energy bill dropped right away. Add real testimonials in wp-admin under "Testimonials".', 'solaris' ) ),
					array( __( 'Placeholder Customer', 'solaris' ), __( 'Business Owner (placeholder)', 'solaris' ), __( 'Great support sizing our commercial system. Replace this with a real customer quote.', 'solaris' ) ),
					array( __( 'Placeholder Customer', 'solaris' ), __( 'Off-Grid Cabin Owner (placeholder)', 'solaris' ), __( 'Reliable equipment for our off-grid setup. Swap in genuine feedback once available.', 'solaris' ) ),
				);
				foreach ( $placeholders as $item ) :
					list( $name, $role, $quote ) = $item;
					?>
					<div class="testimonial-card reveal">
						<p class="testimonial-card__quote"><?php echo esc_html( $quote ); ?></p>
						<div class="testimonial-card__author">
							<span class="testimonial-card__avatar"><?php echo esc_html( mb_substr( $name, 0, 1 ) ); ?></span>
							<span>
								<span class="testimonial-card__name"><?php echo esc_html( $name ); ?></span><br />
								<span class="testimonial-card__role"><?php echo esc_html( $role ); ?></span>
							</span>
						</div>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</div>
</section>
