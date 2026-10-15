<?php
/**
 * Backs "Lazy Load" / "Load More" (Customizer: Products List → Products
 * Loading Mode) — fetches subsequent pages of the shop/category archive as
 * <li> product-card fragments (the same wc_get_template_part('content',
 * 'product') markup the main loop and inc/ajax-search.php already use),
 * appended client-side (assets/js/main.js, initShopLoading) instead of a
 * full page navigation.
 *
 * Reconstructs sorting via WC()->query->get_catalog_ordering_args() — the
 * same method WooCommerce's own sorting dropdown uses — and replicates
 * catalog-visibility/stock-status exclusion manually, since neither is
 * applied automatically to a secondary query: WC_Query::pre_get_posts()
 * explicitly bails when the query isn't the main one. Classic price/
 * attribute filter widgets aren't in use on this site and aren't
 * replicated here.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

function solaris_render_shop_loading_page() {
	check_ajax_referer( 'solaris_shop_loading', 'nonce' );

	$page    = max( 1, absint( $_POST['page'] ?? 1 ) );
	$orderby = isset( $_POST['orderby'] ) ? sanitize_text_field( wp_unslash( $_POST['orderby'] ) ) : '';
	$tax     = isset( $_POST['tax'] ) ? sanitize_key( wp_unslash( $_POST['tax'] ) ) : '';
	// is_scalar() guard: unlike sanitize_key()/sanitize_text_field() above,
	// sanitize_title() has no internal scalar check — passing it an array
	// (e.g. a hand-crafted term[]=x request) throws a TypeError inside
	// remove_accents()'s preg_match() call.
	$term = ( isset( $_POST['term'] ) && is_scalar( $_POST['term'] ) ) ? sanitize_title( wp_unslash( $_POST['term'] ) ) : '';

	$ordering = WC()->query->get_catalog_ordering_args( $orderby );

	$args = array(
		'status'     => 'publish',
		'visibility' => 'catalog',
		'limit'      => absint( solaris_get_option( 'shop_products_per_page' ) ) ?: 12,
		'page'       => $page,
		'paginate'   => true,
		'orderby'    => $ordering['orderby'],
		'order'      => $ordering['order'],
	);
	if ( ! empty( $ordering['meta_key'] ) ) {
		$args['meta_key'] = $ordering['meta_key']; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
	}
	if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
		$args['stock_status'] = 'instock';
	}
	if ( 'product_cat' === $tax && $term ) {
		$args['category'] = array( $term );
	} elseif ( 'product_tag' === $tax && $term ) {
		$args['tag'] = array( $term );
	}

	$result = wc_get_products( $args );

	ob_start();
	foreach ( $result->products as $product_object ) {
		$GLOBALS['post'] = get_post( $product_object->get_id() ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		setup_postdata( $GLOBALS['post'] );
		wc_get_template_part( 'content', 'product' );
	}
	wp_reset_postdata();
	$html = ob_get_clean();

	wp_send_json_success(
		array(
			'html'     => $html,
			'page'     => $page,
			'maxPages' => (int) $result->max_num_pages,
			'hasMore'  => $page < (int) $result->max_num_pages,
		)
	);
}
add_action( 'wp_ajax_solaris_load_shop_products', 'solaris_render_shop_loading_page' );
add_action( 'wp_ajax_nopriv_solaris_load_shop_products', 'solaris_render_shop_loading_page' );
