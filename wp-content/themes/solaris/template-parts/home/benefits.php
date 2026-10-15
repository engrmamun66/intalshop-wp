<?php
/**
 * Product-benefit strip. Only claims things WooCommerce/the store can
 * actually back up (shipping threshold pulled from Woo shipping zones would
 * require real configuration, so it's left editable via the filter below —
 * do not restate this as a hard promise without checking store settings).
 *
 * @package Solaris
 */
$benefits = apply_filters(
	'solaris_benefits_items',
	array(
		array( 'icon' => 'truck', 'title' => __( 'Free Shipping', 'solaris' ), 'text' => __( 'On qualifying orders — set your threshold in WooCommerce shipping settings.', 'solaris' ) ),
		array( 'icon' => 'shield', 'title' => __( 'Secure Payments', 'solaris' ), 'text' => __( 'Checkout protected by your configured WooCommerce payment gateways.', 'solaris' ) ),
		array( 'icon' => 'wrench', 'title' => __( 'Warranty Support', 'solaris' ), 'text' => __( 'Manufacturer warranty terms listed on every product page.', 'solaris' ) ),
		array( 'icon' => 'headset', 'title' => __( 'Customer Assistance', 'solaris' ), 'text' => __( 'Reach our team through the contact page for pre- and post-sale help.', 'solaris' ) ),
	)
);
?>
<section class="section bg-surface">
	<div class="container">
		<div class="benefits-strip">
			<?php foreach ( $benefits as $benefit ) : ?>
				<div class="benefit-item reveal">
					<?php echo solaris_icon( $benefit['icon'] ); ?>
					<strong><?php echo esc_html( $benefit['title'] ); ?></strong>
					<span><?php echo esc_html( $benefit['text'] ); ?></span>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
