<?php
/**
 * "Why choose us" feature cards. Content (icon/title/text) is static,
 * curated copy (not a CPT) — filterable so a site owner's dev can adjust
 * without hunting through files. Section/card styling (background, card
 * colors, hover effect, entrance animation) is Customizer-driven.
 *
 * @package Solaris
 */
$features = apply_filters(
	'solaris_why_choose_us_items',
	array(
		array( 'icon' => 'shield', 'title' => __( 'High-Quality Products', 'solaris' ), 'text' => __( 'Panels, inverters and batteries sourced for durability and long-term performance.', 'solaris' ) ),
		array( 'icon' => 'headset', 'title' => __( 'Expert Support', 'solaris' ), 'text' => __( 'Our team helps you size and spec the right system before you buy.', 'solaris' ) ),
		array( 'icon' => 'truck', 'title' => __( 'Fast Delivery', 'solaris' ), 'text' => __( 'Quick order processing and shipping so your install stays on schedule.', 'solaris' ) ),
		array( 'icon' => 'wrench', 'title' => __( 'Reliable Warranty', 'solaris' ), 'text' => __( 'Manufacturer-backed warranty coverage on every product we sell.', 'solaris' ) ),
		array( 'icon' => 'leaf', 'title' => __( 'Sustainable Solutions', 'solaris' ), 'text' => __( 'Every product is chosen to help you cut emissions and energy costs.', 'solaris' ) ),
		array( 'icon' => 'shield', 'title' => __( 'Secure Shopping', 'solaris' ), 'text' => __( 'Encrypted checkout backed by WooCommerce\'s trusted payment gateways.', 'solaris' ) ),
	)
);

$section_classes = array(
	'section',
	'has-section-bg',
	'why-choose-us-section',
	'why-choose-hover-' . solaris_get_option( 'why_choose_card_hover_effect' ),
	'why-choose-anim-' . solaris_get_option( 'why_choose_card_animation' ),
);
?>
<section class="<?php echo esc_attr( implode( ' ', $section_classes ) ); ?>" id="why-choose-us">
	<?php solaris_section_bg_image( 'why_choose' ); ?>
	<div class="container">
		<div class="section-header is-centered">
			<p class="eyebrow"><?php esc_html_e( 'Why Choose Us', 'solaris' ); ?></p>
			<h2><?php echo esc_html( solaris_get_option( 'why_choose_title' ) ); ?></h2>
		</div>
		<div class="grid grid-3">
			<?php foreach ( $features as $feature ) : ?>
				<div class="feature-card reveal">
					<div class="feature-card__icon"><?php echo solaris_icon( $feature['icon'] ); ?></div>
					<h3><?php echo esc_html( $feature['title'] ); ?></h3>
					<p><?php echo esc_html( $feature['text'] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
