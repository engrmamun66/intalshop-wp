<?php
/**
 * Core theme supports, nav menus, sidebars, image sizes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function solaris_setup() {
	load_theme_textdomain( 'solaris', SOLARIS_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'script', 'style' ) );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 60,
			'width'       => 200,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	// WooCommerce.
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'solaris' ),
			'mobile'  => __( 'Mobile Menu (optional, falls back to Primary)', 'solaris' ),
			'footer'  => __( 'Footer Links', 'solaris' ),
			'legal'   => __( 'Footer Legal Links', 'solaris' ),
		)
	);

	add_image_size( 'solaris-category-card', 480, 360, true );
	add_image_size( 'solaris-solution-card', 640, 480, true );
	add_image_size( 'solaris-hero', 1920, 1080, true );

	add_post_type_support( 'page', 'excerpt' );
}
add_action( 'after_setup_theme', 'solaris_setup' );

function solaris_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'solaris_content_width', 860 );
}
add_action( 'after_setup_theme', 'solaris_content_width', 0 );

function solaris_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'Footer Widget 1', 'solaris' ),
			'id'            => 'footer-widget-1',
			'description'   => __( 'First of three footer columns. Suggested use: company info / about.', 'solaris' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		)
	);
	register_sidebar(
		array(
			'name'          => __( 'Footer Widget 2', 'solaris' ),
			'id'            => 'footer-widget-2',
			'description'   => __( 'Second of three footer columns. Suggested use: shop / useful links.', 'solaris' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		)
	);
	register_sidebar(
		array(
			'name'          => __( 'Footer Widget 3', 'solaris' ),
			'id'            => 'footer-widget-3',
			'description'   => __( 'Third of three footer columns. Suggested use: contact / newsletter.', 'solaris' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		)
	);
}
add_action( 'widgets_init', 'solaris_widgets_init' );
