<?php
/**
 * Solar use-case cards (residential/commercial/industrial/off-grid), backed
 * by the `solar_solution` CPT so a store admin can edit these without code.
 * Section/card styling is Customizer-driven (see inc/customizer.php).
 *
 * @package Solaris
 */
$solutions = solaris_get_solutions( 4 );
if ( empty( $solutions ) ) {
	return;
}

$section_classes = array(
	'section',
	'has-section-bg',
	'solutions-section',
	'solutions-hover-' . solaris_get_option( 'solutions_card_hover_effect' ),
	'solutions-anim-' . solaris_get_option( 'solutions_card_animation' ),
);
?>
<section class="<?php echo esc_attr( implode( ' ', $section_classes ) ); ?>" id="solar-solutions">
	<?php solaris_section_bg_image( 'solutions' ); ?>
	<div class="container">
		<div class="section-header is-centered">
			<p class="eyebrow"><?php esc_html_e( 'Solar Solutions', 'solaris' ); ?></p>
			<h2><?php echo esc_html( solaris_get_option( 'solutions_title' ) ); ?></h2>
		</div>
		<div class="grid grid-4">
			<?php foreach ( $solutions as $solution ) : ?>
				<?php
				$image_url  = get_the_post_thumbnail_url( $solution, 'solaris-solution-card' );
				$learn_more = get_post_meta( $solution->ID, '_solaris_learn_more_url', true );
				$learn_more = $learn_more ? $learn_more : get_permalink( $solution );
				?>
				<div class="solution-card reveal"<?php echo $image_url ? ' style="background-image:url(' . esc_url( $image_url ) . ');"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<div class="solution-card__icon"><?php echo solaris_icon( solaris_solution_icon( get_the_title( $solution ) ) ); ?></div>
					<div class="solution-card__body">
						<h3><?php echo esc_html( get_the_title( $solution ) ); ?></h3>
						<p><?php echo esc_html( wp_trim_words( $solution->post_content, 18 ) ); ?></p>
						<a class="btn--ghost" href="<?php echo esc_url( $learn_more ); ?>" style="color:var(--solutions-card-text);">
							<?php esc_html_e( 'Learn More', 'solaris' ); ?>
						</a>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
