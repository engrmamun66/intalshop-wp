<?php
/**
 * Featured / best-selling / latest products, per the Customizer source
 * picker. Rendered with wc_get_template_part() so every card (price, sale
 * badge, add-to-cart) is genuine WooCommerce markup, not reimplemented here
 * — this section's Customizer controls (see inc/customizer.php) only style
 * the section itself (background, title colors), never the product cards.
 *
 * @package Solaris
 */
if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

$products = solaris_get_homepage_products();
if ( empty( $products ) ) {
	return;
}
?>
<section class="section has-section-bg featured-products-section" id="featured-products">
	<?php solaris_section_bg_image( 'featured_products' ); ?>
	<div class="container">
		<div class="section-header is-centered">
			<p class="eyebrow"><?php esc_html_e( 'Top Picks', 'solaris' ); ?></p>
			<h2><?php echo esc_html( solaris_get_option( 'featured_products_heading' ) ); ?></h2>
		</div>
		<ul class="products columns-4">
			<?php
			foreach ( $products as $post_object ) {
				$GLOBALS['post'] = get_post( $post_object->get_id() ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				setup_postdata( $GLOBALS['post'] );
				wc_get_template_part( 'content', 'product' );
			}
			wp_reset_postdata();
			?>
		</ul>
		<div class="hero-actions" style="justify-content:center;margin-top:40px;">
			<a class="button btn--outline" href="<?php echo esc_url( solaris_shop_url() ); ?>"><?php esc_html_e( 'View All Products', 'solaris' ); ?></a>
		</div>
	</div>
</section>
