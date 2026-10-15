<?php
/**
 * Backs the product-card "Quick View" button (Customizer: Product Cards →
 * Show Quick View Button): given a product ID, renders that product's
 * image/price/short description/add-to-cart into an HTML fragment for
 * template-parts/components/quick-view-modal.php to display. The add-to-cart
 * form is WooCommerce's own woocommerce_template_single_add_to_cart() — the
 * exact same core template the single-product page uses (correct per
 * product type: simple/variable/etc.) — nothing about cart/pricing is
 * reimplemented here.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

function solaris_render_quick_view() {
	check_ajax_referer( 'solaris_quick_view', 'nonce' );

	$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
	$product    = $product_id ? wc_get_product( $product_id ) : false;

	if ( ! $product || ! $product->is_visible() ) {
		wp_send_json_error();
	}

	// setup_postdata() fires core's own 'the_post' action, which is what
	// WooCommerce hooks wc_setup_product_data() to — the same mechanism
	// inc/ajax-search.php already relies on to make global $product (not
	// just $post) correct outside the main loop, which
	// woocommerce_template_single_add_to_cart() below depends on.
	$GLOBALS['post'] = get_post( $product_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
	setup_postdata( $GLOBALS['post'] );

	ob_start();
	?>
	<div class="quick-view-product">
		<div class="quick-view-product__gallery">
			<?php echo $product->get_image( 'woocommerce_single' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
		<div class="quick-view-product__summary">
			<h2 class="quick-view-product__title"><?php echo esc_html( $product->get_name() ); ?></h2>
			<div class="quick-view-product__price"><?php echo $product->get_price_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
			<?php if ( $product->get_short_description() ) : ?>
				<div class="quick-view-product__excerpt"><?php echo wp_kses_post( wpautop( $product->get_short_description() ) ); ?></div>
			<?php endif; ?>
			<?php woocommerce_template_single_add_to_cart(); ?>
			<a class="quick-view-product__full-link" href="<?php echo esc_url( get_permalink( $product_id ) ); ?>">
				<?php esc_html_e( 'View full details', 'solaris' ); ?>
			</a>
		</div>
	</div>
	<?php
	$html = ob_get_clean();

	wp_reset_postdata();

	wp_send_json_success( array( 'html' => $html ) );
}
add_action( 'wp_ajax_solaris_quick_view', 'solaris_render_quick_view' );
add_action( 'wp_ajax_nopriv_solaris_quick_view', 'solaris_render_quick_view' );
