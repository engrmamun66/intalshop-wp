<?php
/**
 * Custom post types backing editable homepage content (testimonials,
 * solar-use-case cards) so a store admin can manage this copy from wp-admin
 * without touching template files, without pulling in a page-builder plugin.
 * FAQ content used to be a third CPT here too (solar_faq) — it's now a
 * Customizer repeater instead (FAQ Section → Questions & Answers,
 * Solaris_Faq_Items_Control in inc/customizer-controls.php), since that
 * gives live-preview editing without a separate wp-admin screen. The
 * original solar_faq posts are still in the database, just no longer
 * registered/reachable from wp-admin.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function solaris_register_post_types() {
	register_post_type(
		'solar_testimonial',
		array(
			'labels'       => array(
				'name'          => __( 'Testimonials', 'solaris' ),
				'singular_name' => __( 'Testimonial', 'solaris' ),
				'add_new_item'  => __( 'Add New Testimonial', 'solaris' ),
			),
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'menu_icon'    => 'dashicons-format-quote',
			'supports'     => array( 'title', 'editor', 'thumbnail' ),
			'capability_type' => 'page',
			'map_meta_cap' => true,
		)
	);

	register_post_type(
		'solar_solution',
		array(
			'labels'       => array(
				'name'          => __( 'Solar Solutions', 'solaris' ),
				'singular_name' => __( 'Solar Solution', 'solaris' ),
				'add_new_item'  => __( 'Add New Solution', 'solaris' ),
			),
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'menu_icon'    => 'dashicons-admin-site-alt3',
			'supports'     => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
			'capability_type' => 'page',
			'map_meta_cap' => true,
		)
	);
}
add_action( 'init', 'solaris_register_post_types' );

/* -------------------------------------------------------------- Meta boxes */

function solaris_add_meta_boxes() {
	add_meta_box( 'solaris_testimonial_meta', __( 'Role / Company', 'solaris' ), 'solaris_render_testimonial_meta_box', 'solar_testimonial', 'side' );
	add_meta_box( 'solaris_solution_meta', __( 'Learn More Link', 'solaris' ), 'solaris_render_solution_meta_box', 'solar_solution', 'side' );
}
add_action( 'add_meta_boxes', 'solaris_add_meta_boxes' );

function solaris_render_testimonial_meta_box( $post ) {
	wp_nonce_field( 'solaris_save_testimonial_meta', 'solaris_testimonial_nonce' );
	$role = get_post_meta( $post->ID, '_solaris_role', true );
	echo '<label for="solaris_role">' . esc_html__( 'e.g. "Homeowner, Austin TX"', 'solaris' ) . '</label>';
	echo '<input type="text" id="solaris_role" name="solaris_role" value="' . esc_attr( $role ) . '" style="width:100%;margin-top:6px;" />';
}

function solaris_render_solution_meta_box( $post ) {
	wp_nonce_field( 'solaris_save_solution_meta', 'solaris_solution_nonce' );
	$url = get_post_meta( $post->ID, '_solaris_learn_more_url', true );
	echo '<label for="solaris_learn_more_url">' . esc_html__( '"Learn More" URL (leave blank to use the single view)', 'solaris' ) . '</label>';
	echo '<input type="url" id="solaris_learn_more_url" name="solaris_learn_more_url" value="' . esc_attr( $url ) . '" style="width:100%;margin-top:6px;" />';
}

function solaris_save_post_meta( $post_id ) {
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['solaris_testimonial_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['solaris_testimonial_nonce'] ) ), 'solaris_save_testimonial_meta' ) ) {
		if ( isset( $_POST['solaris_role'] ) ) {
			update_post_meta( $post_id, '_solaris_role', sanitize_text_field( wp_unslash( $_POST['solaris_role'] ) ) );
		}
	}

	if ( isset( $_POST['solaris_solution_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['solaris_solution_nonce'] ) ), 'solaris_save_solution_meta' ) ) {
		if ( isset( $_POST['solaris_learn_more_url'] ) ) {
			update_post_meta( $post_id, '_solaris_learn_more_url', esc_url_raw( wp_unslash( $_POST['solaris_learn_more_url'] ) ) );
		}
	}
}
add_action( 'save_post', 'solaris_save_post_meta' );

/* ------------------------------------------------------------------ Getters */

function solaris_get_testimonials( $limit = 6 ) {
	return get_posts(
		array(
			'post_type'      => 'solar_testimonial',
			'posts_per_page' => $limit,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'no_found_rows'  => true,
		)
	);
}

function solaris_get_solutions( $limit = 4 ) {
	return get_posts(
		array(
			'post_type'      => 'solar_solution',
			'posts_per_page' => $limit,
			'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'ASC' ),
			'no_found_rows'  => true,
		)
	);
}
