<?php
/**
 * Backs the full-screen search modal: renders a product grid fragment for a
 * given search term + sort order, using the exact same wc_get_template_part
 * card markup as the shop loop so results are visually identical.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

function solaris_search_modal_query_args( $term, $orderby ) {
	$args = array(
		'status'  => 'publish',
		'limit'   => 24,
		'orderby' => 'date',
		'order'   => 'DESC',
	);

	if ( '' !== $term ) {
		$args['s'] = $term;
	}

	switch ( $orderby ) {
		case 'price':
			$args['orderby'] = 'price';
			$args['order']   = 'ASC';
			break;
		case 'price-desc':
			$args['orderby'] = 'price';
			$args['order']   = 'DESC';
			break;
		case 'popularity':
			$args['orderby']  = 'meta_value_num';
			$args['meta_key'] = 'total_sales'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			$args['order']    = 'DESC';
			break;
		case 'rating':
			$args['orderby']  = 'meta_value_num';
			$args['meta_key'] = '_wc_average_rating'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			$args['order']    = 'DESC';
			break;
		case 'menu_order':
			$args['orderby'] = 'menu_order title';
			$args['order']   = 'ASC';
			break;
		default: // 'date' — latest first, already the default above.
			break;
	}

	return $args;
}

function solaris_render_search_results() {
	check_ajax_referer( 'solaris_search_modal', 'nonce' );

	$term    = isset( $_POST['term'] ) ? sanitize_text_field( wp_unslash( $_POST['term'] ) ) : '';
	$orderby = isset( $_POST['orderby'] ) ? sanitize_key( wp_unslash( $_POST['orderby'] ) ) : 'date';

	$products = wc_get_products( solaris_search_modal_query_args( $term, $orderby ) );

	ob_start();

	if ( empty( $products ) ) {
		echo '<div class="empty-state">';
		echo solaris_icon( 'search' );
		echo '<h2>' . esc_html__( 'No solar products found', 'solaris' ) . '</h2>';
		echo '<p>' . esc_html__( 'Try another search term.', 'solaris' ) . '</p>';
		echo '</div>';
	} else {
		echo '<ul class="products columns-4">';
		foreach ( $products as $product_object ) {
			$GLOBALS['post'] = get_post( $product_object->get_id() ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			setup_postdata( $GLOBALS['post'] );
			wc_get_template_part( 'content', 'product' );
		}
		wp_reset_postdata();
		echo '</ul>';
	}

	$html = ob_get_clean();

	wp_send_json_success(
		array(
			'html'  => $html,
			'count' => count( $products ),
		)
	);
}
add_action( 'wp_ajax_solaris_search_products', 'solaris_render_search_results' );
add_action( 'wp_ajax_nopriv_solaris_search_products', 'solaris_render_search_results' );
