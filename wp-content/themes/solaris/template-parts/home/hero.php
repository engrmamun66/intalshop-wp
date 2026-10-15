<?php
/**
 * Full-viewport hero with transparent header overlay. Becomes a slider
 * (configurable transition + dot navigation) when 2+ Hero Slides are
 * configured and active in the Customizer; with 0 or 1 active slide it
 * renders exactly as a single static background image with the global
 * Hero text fields, same as before the slider feature existed.
 *
 * In slider mode, each slide gets its own text block (eyebrow/title/
 * subtext/two buttons) that crossfades in sync with its background image.
 * Any per-slide text field left blank in the Customizer falls back to the
 * matching global Hero text field, so a slide can override just its title
 * and inherit everything else, for example.
 *
 * @package Solaris
 */
$hero_slides = solaris_get_hero_slides();
$is_slider   = count( $hero_slides ) > 1;

if ( $is_slider ) {
	$background_style = '';
} else {
	$single_image     = $hero_slides ? $hero_slides[0]['url'] : solaris_get_option( 'hero_image' );
	$background_style = $single_image ? ' style="background-image:url(' . esc_url( $single_image ) . ');"' : '';
}

/**
 * Resolves one slide's text against the global Hero fields -- anything the
 * slide left blank inherits the site-wide default instead of rendering
 * empty.
 */
function solaris_hero_slide_text( $slide ) {
	return array(
		'eyebrow'      => $slide['eyebrow'] ?: solaris_get_option( 'hero_eyebrow' ),
		'title'        => $slide['title'] ?: solaris_get_option( 'hero_heading' ),
		'subtext'      => $slide['subtext'] ?: solaris_get_option( 'hero_description' ),
		'button1_text' => $slide['button1_text'] ?: solaris_get_option( 'hero_primary_text' ),
		'button1_url'  => $slide['button1_url'] ?: solaris_cta_url( 'hero_primary_url' ),
		'button2_text' => $slide['button2_text'] ?: solaris_get_option( 'hero_secondary_text' ),
		'button2_url'  => $slide['button2_url'] ?: solaris_get_option( 'hero_secondary_url' ),
	);
}

$hero_transition     = solaris_get_option( 'hero_slider_transition' );
$hero_content_anim   = solaris_get_option( 'hero_content_animation' );
$hero_content_class  = 'none' !== $hero_content_anim ? ' hero-content-anim-' . $hero_content_anim : '';
?>
<section class="hero<?php echo $is_slider ? ' has-slider' : ''; ?>"<?php echo $background_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php if ( $is_slider ) : ?>
		<div class="hero-slider hero-slider--<?php echo esc_attr( $hero_transition ); ?>" data-hero-slider aria-hidden="true">
			<?php foreach ( $hero_slides as $index => $slide ) : ?>
				<div class="hero-slide<?php echo 0 === $index ? ' is-active' : ''; ?>" style="background-image:url(<?php echo esc_url( $slide['url'] ); ?>);"></div>
			<?php endforeach; ?>
		</div>
		<div class="hero-dots" role="tablist" aria-label="<?php esc_attr_e( 'Hero slide navigation', 'solaris' ); ?>">
			<?php foreach ( $hero_slides as $index => $slide ) : ?>
				<button
					type="button"
					class="hero-dots__dot<?php echo 0 === $index ? ' is-active' : ''; ?>"
					role="tab"
					data-hero-dot="<?php echo esc_attr( $index ); ?>"
					aria-selected="<?php echo 0 === $index ? 'true' : 'false'; ?>"
					aria-label="<?php echo esc_attr( sprintf( /* translators: %d: slide number. */ __( 'Show slide %d', 'solaris' ), $index + 1 ) ); ?>"
				></button>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<?php $hero_overflow_image = solaris_get_option( 'hero_overflow_image' ); ?>
	<?php if ( solaris_get_option( 'hero_overflow_image_enabled' ) && $hero_overflow_image ) : ?>
		<?php
		$hero_overflow_filter_map = solaris_image_filter_css_map();
		$hero_overflow_filter     = $hero_overflow_filter_map[ solaris_get_option( 'hero_overflow_image_effect' ) ] ?? 'none';
		?>
		<img class="hero-overflow-image" src="<?php echo esc_url( $hero_overflow_image ); ?>" alt="" aria-hidden="true" style="filter:<?php echo esc_attr( $hero_overflow_filter ); ?>;" />
	<?php endif; ?>

	<?php if ( solaris_get_option( 'hero_solar_system_enabled' ) ) : ?>
		<div class="solaris-solar-system" aria-hidden="true">
			<div class="solaris-sun"></div>
			<div class="solaris-mercury"></div>
			<div class="solaris-venus"></div>
			<div class="solaris-earth"></div>
			<div class="solaris-mars"></div>
			<div class="solaris-jupiter"></div>
			<div class="solaris-saturn"></div>
			<div class="solaris-uranus"></div>
			<div class="solaris-neptune"></div>
			<div class="solaris-pluto"></div>
			<div class="solaris-asteroids-belt"></div>
		</div>
	<?php endif; ?>

	<div class="hero-inner<?php echo $is_slider ? ' hero-inner--multi' : ''; ?>">
		<?php if ( $is_slider ) : ?>
			<?php foreach ( $hero_slides as $index => $slide ) : ?>
				<?php $text = solaris_hero_slide_text( $slide ); ?>
				<div class="hero-content<?php echo 0 === $index ? ' is-active' : ''; ?><?php echo esc_attr( $hero_content_class ); ?>" data-hero-content="<?php echo esc_attr( $index ); ?>">
					<?php if ( $text['eyebrow'] ) : ?>
						<p class="eyebrow"><?php echo esc_html( $text['eyebrow'] ); ?></p>
					<?php endif; ?>

					<h1><?php echo esc_html( $text['title'] ); ?></h1>
					<p class="hero-lead"><?php echo esc_html( $text['subtext'] ); ?></p>

					<div class="hero-actions">
						<?php if ( $text['button1_text'] ) : ?>
							<a class="button" href="<?php echo esc_url( $text['button1_url'] ); ?>">
								<?php echo esc_html( $text['button1_text'] ); ?>
							</a>
						<?php endif; ?>
						<?php if ( $text['button2_text'] ) : ?>
							<a class="button" href="<?php echo esc_url( $text['button2_url'] ); ?>">
								<?php echo esc_html( $text['button2_text'] ); ?>
							</a>
						<?php endif; ?>
					</div>
				</div>
			<?php endforeach; ?>
		<?php else : ?>
			<div class="hero-content is-active reveal<?php echo esc_attr( $hero_content_class ); ?>">
				<?php $eyebrow = solaris_get_option( 'hero_eyebrow' ); ?>
				<?php if ( $eyebrow ) : ?>
					<p class="eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
				<?php endif; ?>

				<h1><?php echo esc_html( solaris_get_option( 'hero_heading' ) ); ?></h1>
				<p class="hero-lead"><?php echo esc_html( solaris_get_option( 'hero_description' ) ); ?></p>

				<div class="hero-actions">
					<a class="button" href="<?php echo esc_url( solaris_cta_url( 'hero_primary_url' ) ); ?>">
						<?php echo esc_html( solaris_get_option( 'hero_primary_text' ) ); ?>
					</a>
					<?php $secondary_text = solaris_get_option( 'hero_secondary_text' ); ?>
					<?php if ( $secondary_text ) : ?>
						<a class="button" href="<?php echo esc_url( solaris_get_option( 'hero_secondary_url' ) ); ?>">
							<?php echo esc_html( $secondary_text ); ?>
						</a>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
	<div class="hero-scroll-cue" aria-hidden="true">
		<span><?php esc_html_e( 'Scroll', 'solaris' ); ?></span>
		<?php echo solaris_icon( 'chevron-down' ); ?>
	</div>
</section>
