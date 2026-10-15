<?php
/**
 * Shared view helpers: icon set, breadcrumbs, CTA URL fallbacks, misc formatting.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Minimal inline-SVG icon set (stroke-based, single style) so the theme never
 * mixes icon fonts with SVGs. Extend the $icons map rather than adding a
 * second icon system.
 */
function solaris_icon( $name, $args = array() ) {
	$args = wp_parse_args( $args, array( 'class' => '' ) );

	$icons = array(
		'menu'      => '<line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>',
		'close'     => '<line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>',
		'cart'      => '<circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>',
		'user'      => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
		'search'    => '<circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>',
		'eye'       => '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>',
		'arrow-right' => '<line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>',
		'arrow-up'  => '<line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/>',
		'chevron-down' => '<polyline points="6 9 12 15 18 9"/>',
		'plus'      => '<line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>',
		'check'     => '<polyline points="20 6 9 17 4 12"/>',
		'heart'     => '<path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8z"/>',
		'leaf'      => '<path d="M11 20A7 7 0 0 1 4 13c0-5 4-9 9-9 3 0 6 2 6 6 0 5-4 10-8 10z"/><path d="M4 13c4 0 9-3 11-7"/>',
		'shield'    => '<path d="M12 2 4 5v6c0 5 3.5 9 8 11 4.5-2 8-6 8-11V5z"/>',
		'truck'     => '<rect x="1" y="7" width="15" height="11" rx="1"/><path d="M16 11h4l2 4v3h-6z"/><circle cx="6" cy="20" r="2"/><circle cx="17" cy="20" r="2"/>',
		'wrench'    => '<path d="M14.7 6.3a4 4 0 1 0-5.7 5.7L2 19l3 3 7-7a4 4 0 0 0 5.7-5.7z"/>',
		'headset'   => '<path d="M3 14v-3a9 9 0 1 1 18 0v3"/><path d="M21 14a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-2a2 2 0 0 1 2-2h3zM3 14a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-2a2 2 0 0 0-2-2H3z"/>',
		'sun'       => '<circle cx="12" cy="12" r="4"/><line x1="12" y1="2" x2="12" y2="4"/><line x1="12" y1="20" x2="12" y2="22"/><line x1="4.2" y1="4.2" x2="5.6" y2="5.6"/><line x1="18.4" y1="18.4" x2="19.8" y2="19.8"/><line x1="2" y1="12" x2="4" y2="12"/><line x1="20" y1="12" x2="22" y2="12"/><line x1="4.2" y1="19.8" x2="5.6" y2="18.4"/><line x1="18.4" y1="5.6" x2="19.8" y2="4.2"/>',
		'phone'     => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3 19.5 19.5 0 0 1-6-6 19.8 19.8 0 0 1-3-8.7A2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 2 .7 3a2 2 0 0 1-.5 2.1L8 10a16 16 0 0 0 6 6l1.2-1.3a2 2 0 0 1 2.1-.5c1 .3 2 .5 3 .7a2 2 0 0 1 1.7 2z"/>',
		'mail'      => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 6-10 7L2 6"/>',
		'map-pin'   => '<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 1 1 18 0z"/><circle cx="12" cy="10" r="3"/>',
		'home'      => '<path d="M3 9.5 12 3l9 6.5"/><path d="M5 10v10a1 1 0 0 0 1 1h3v-6h6v6h3a1 1 0 0 0 1-1V10"/>',
		'building'  => '<rect x="4" y="3" width="16" height="18" rx="1"/><path d="M9 21v-4h6v4"/><path d="M8 7h.5M15.5 7h.5M8 11h.5M15.5 11h.5M8 15h.5M15.5 15h.5"/>',
		'factory'   => '<path d="M2 20a1 1 0 0 0 1 1h18a1 1 0 0 0 1-1v-9l-5 4v-4l-5 4v-4l-5 4v-5a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1z"/><path d="M17 17h.5M12 17h.5M7 17h.5"/>',
		'battery'   => '<rect x="1" y="6" width="18" height="12" rx="2"/><line x1="22" y1="10" x2="22" y2="14"/><path d="M7 9l-2 3h3l-2 3"/>',
		'clock'     => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
		'facebook'  => '<path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>',
		'twitter'   => '<path d="M23 4.6c-.8.4-1.7.7-2.6.8a4.5 4.5 0 0 0-7.7 4.1A12.9 12.9 0 0 1 3 3.8a4.5 4.5 0 0 0 1.4 6A4.4 4.4 0 0 1 2 9.3a4.5 4.5 0 0 0 3.6 4.4 4.5 4.5 0 0 1-2 .1 4.5 4.5 0 0 0 4.2 3.1A9 9 0 0 1 1 18.9a12.7 12.7 0 0 0 6.9 2c8.3 0 12.8-6.9 12.8-12.9v-.6c.9-.6 1.6-1.4 2.3-2.3z"/>',
		'instagram' => '<rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><line x1="17.5" y1="6.5" x2="17.5" y2="6.5"/>',
		'linkedin'  => '<path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-4 0v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/>',
		'youtube'   => '<path d="M22.5 8.5a3 3 0 0 0-2.1-2.1C18.5 6 12 6 12 6s-6.5 0-8.4.4A3 3 0 0 0 1.5 8.5 31 31 0 0 0 1 12a31 31 0 0 0 .5 3.5 3 3 0 0 0 2.1 2.1C5.5 18 12 18 12 18s6.5 0 8.4-.4a3 3 0 0 0 2.1-2.1A31 31 0 0 0 23 12a31 31 0 0 0-.5-3.5z"/><polygon points="10 9.5 15 12 10 14.5"/>',
		'volume'      => '<polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M15.5 8.5a5 5 0 0 1 0 7"/><path d="M18.5 5.5a9 9 0 0 1 0 13"/>',
		'volume-off'  => '<polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><line x1="23" y1="9" x2="17" y2="15"/><line x1="17" y1="9" x2="23" y2="15"/>',
	);

	if ( ! isset( $icons[ $name ] ) ) {
		return '';
	}

	return sprintf(
		'<svg class="icon icon-%1$s %2$s" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%3$s</svg>',
		esc_attr( $name ),
		esc_attr( $args['class'] ),
		$icons[ $name ] // Fixed internal icon paths, not user input.
	);
}

/**
 * Picks an icon for a Solar Solution card by matching a keyword in its
 * title — the CPT itself has no dedicated icon field, and title-keyword
 * matching covers the four standard use-cases (residential/commercial/
 * industrial/off-grid) without needing one. Falls back to the generic
 * 'sun' icon for any custom solution title that doesn't match.
 */
function solaris_solution_icon( $title ) {
	$title = strtolower( $title );
	if ( false !== strpos( $title, 'residential' ) ) {
		return 'home';
	}
	if ( false !== strpos( $title, 'commercial' ) ) {
		return 'building';
	}
	if ( false !== strpos( $title, 'industrial' ) ) {
		return 'factory';
	}
	if ( false !== strpos( $title, 'off-grid' ) || false !== strpos( $title, 'off grid' ) ) {
		return 'battery';
	}
	return 'sun';
}

function solaris_shop_url() {
	$shop_page_id = function_exists( 'wc_get_page_id' ) ? wc_get_page_id( 'shop' ) : 0;
	return $shop_page_id > 0 ? get_permalink( $shop_page_id ) : home_url( '/' );
}

/**
 * Active hero slides, in the admin's saved order. Returns an array of
 * associative arrays, one per active slide, each with the resolved image
 * URL (full hero-crop size) plus that slide's raw per-slide text fields
 * ('eyebrow', 'title', 'subtext', 'button1_text', 'button1_url',
 * 'button2_text', 'button2_url') exactly as stored — no global-fallback
 * substitution happens here, that's the caller's job (see
 * template-parts/home/hero.php). Empty array when no slides are configured
 * or none are marked active — callers should fall back to the single
 * hero_image setting.
 */
function solaris_get_hero_slides() {
	$raw = solaris_get_option( 'hero_slides' );
	$decoded = json_decode( is_string( $raw ) ? $raw : '', true );
	if ( ! is_array( $decoded ) ) {
		return array();
	}

	$slides = array();
	foreach ( $decoded as $slide ) {
		if ( empty( $slide['active'] ) || empty( $slide['id'] ) ) {
			continue;
		}
		$url = wp_get_attachment_image_url( absint( $slide['id'] ), 'solaris-hero' );
		if ( ! $url ) {
			continue;
		}
		$slides[] = array(
			'url'           => $url,
			'eyebrow'       => $slide['eyebrow'] ?? '',
			'title'         => $slide['title'] ?? '',
			'subtext'       => $slide['subtext'] ?? '',
			'button1_text'  => $slide['button1_text'] ?? '',
			'button1_url'   => $slide['button1_url'] ?? '',
			'button2_text'  => $slide['button2_text'] ?? '',
			'button2_url'   => $slide['button2_url'] ?? '',
		);
	}

	return $slides;
}

/**
 * Resolve a Customizer CTA URL, falling back to the Shop page when the
 * admin left the field blank.
 */
function solaris_cta_url( $option_key ) {
	$url = solaris_get_option( $option_key );
	return $url ? esc_url( $url ) : esc_url( solaris_shop_url() );
}

function solaris_copyright_text() {
	$text = solaris_get_option( 'copyright_text' );
	return str_replace( '{{year}}', gmdate( 'Y' ), $text );
}

function solaris_social_links() {
	$networks = array( 'facebook', 'twitter', 'instagram', 'linkedin', 'youtube' );
	$links    = array();
	foreach ( $networks as $network ) {
		$url = solaris_get_option( "social_{$network}" );
		if ( $url ) {
			$links[ $network ] = $url;
		}
	}
	return $links;
}

/**
 * Lightweight breadcrumb trail for non-WooCommerce pages. Product pages use
 * WooCommerce's own woocommerce_breadcrumb() instead.
 */
function solaris_breadcrumbs() {
	if ( is_front_page() ) {
		return;
	}

	$crumbs = array();
	if ( is_search() ) {
		$crumbs[] = array( __( 'Search Results', 'solaris' ), '' );
	} elseif ( is_404() ) {
		$crumbs[] = array( __( 'Not Found', 'solaris' ), '' );
	} elseif ( is_singular( 'post' ) ) {
		$blog_page = get_page_by_path( 'blog' );
		if ( $blog_page ) {
			$crumbs[] = array( get_the_title( $blog_page ), get_permalink( $blog_page ) );
		}
		$crumbs[] = array( get_the_title(), '' );
	} elseif ( is_page() ) {
		$crumbs[] = array( get_the_title(), '' );
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$crumbs[] = array( single_term_title( '', false ), '' );
	} elseif ( is_singular() ) {
		$crumbs[] = array( get_the_title(), '' );
	}

	$sep = '<span class="woocommerce-breadcrumb__sep" aria-hidden="true">' . solaris_icon( 'chevron-down', array( 'class' => 'woocommerce-breadcrumb__sep-icon' ) ) . '</span>';

	echo '<div class="breadcrumb-bar"><nav class="woocommerce-breadcrumb" aria-label="' . esc_attr__( 'Breadcrumb', 'solaris' ) . '">';
	echo '<span class="woocommerce-breadcrumb__crumb"><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'solaris' ) . '</a></span>';

	foreach ( $crumbs as $crumb ) {
		echo $sep; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		if ( ! empty( $crumb[1] ) ) {
			echo '<span class="woocommerce-breadcrumb__crumb"><a href="' . esc_url( $crumb[1] ) . '">' . esc_html( $crumb[0] ) . '</a></span>';
		} else {
			echo '<span class="woocommerce-breadcrumb__crumb">' . esc_html( $crumb[0] ) . '</span>';
		}
	}

	echo '</nav></div>';
}

function solaris_excerpt( $limit = 20 ) {
	$excerpt = has_excerpt() ? get_the_excerpt() : wp_trim_words( get_the_content(), $limit );
	echo esc_html( $excerpt );
}

/**
 * Category IDs picked in Customizer → Blog Posts Section ([] = all).
 */
function solaris_get_homepage_blog_categories() {
	$ids = json_decode( (string) solaris_get_option( 'home_blog_categories' ), true );
	return is_array( $ids ) ? array_filter( array_map( 'absint', $ids ) ) : array();
}

/**
 * Posts for the homepage blog section, filtered by the Customizer category
 * picker and limited to its "Number of Posts" setting.
 */
function solaris_get_homepage_posts() {
	$args = array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => max( 1, min( 12, absint( solaris_get_option( 'home_blog_count' ) ) ) ),
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	);

	$categories = solaris_get_homepage_blog_categories();
	if ( $categories ) {
		$args['category__in'] = $categories;
	}

	return new WP_Query( $args );
}

/**
 * "Show All" target: the Customizer URL if set, else the single selected
 * category's archive, else the Blog page (Posts page or /blog slug).
 */
function solaris_get_homepage_blog_url() {
	$url = solaris_get_option( 'home_blog_button_url' );
	if ( $url ) {
		return $url;
	}

	$categories = solaris_get_homepage_blog_categories();
	if ( 1 === count( $categories ) ) {
		$link = get_category_link( reset( $categories ) );
		if ( $link ) {
			return $link;
		}
	}

	$posts_page = (int) get_option( 'page_for_posts' );
	if ( $posts_page ) {
		return get_permalink( $posts_page );
	}

	$blog_page = get_page_by_path( 'blog' );
	return $blog_page ? get_permalink( $blog_page ) : home_url( '/' );
}

/**
 * Renders the primary or (if assigned) dedicated mobile menu.
 */
function solaris_nav_menu( $location = 'primary', $args = array() ) {
	if ( 'mobile' === $location && ! has_nav_menu( 'mobile' ) ) {
		$location = 'primary';
	}

	if ( ! has_nav_menu( $location ) ) {
		return;
	}

	wp_nav_menu(
		wp_parse_args(
			$args,
			array(
				'theme_location' => $location,
				'container'      => false,
				'fallback_cb'    => false,
			)
		)
	);
}

/**
 * '#rrggbb' -> '12, 34, 56', for use inside an rgba(var(--x), alpha) CSS
 * expression. Returns '0, 0, 0' for anything that isn't a 6-digit hex.
 */
function solaris_hex_to_rgb( $hex ) {
	$hex = ltrim( (string) $hex, '#' );
	if ( ! preg_match( '/^[0-9a-fA-F]{6}$/', $hex ) ) {
		return '0, 0, 0';
	}
	return implode( ', ', array_map( 'hexdec', str_split( $hex, 2 ) ) );
}

/**
 * Renders the shared "section background" layers behind a section's
 * .container: an optional image (opacity/filter/repeat/position-controlled)
 * and/or an optional gradient overlay tint, both Customizer-driven off
 * `{$prefix}_bg_image*` and `{$prefix}_bg_overlay_*` fields. Echoes nothing
 * for a layer that isn't configured. The parent <section> needs the
 * `has-section-bg` class (assets/css/home.css) for correct stacking; the
 * overlay paints after (visually above) the image since both share
 * z-index:0 and CSS stacks same-z-index siblings in source order.
 *
 * "No Repeat" stretches the image to cover the section (the pre-existing
 * behavior); any tiling repeat mode switches to the image's natural size —
 * a tiled pattern stretched to cover would just show one giant copy.
 */
/**
 * Resolves an solaris_image_filter_choices() key (inc/customizer.php) to a
 * real CSS filter() value. Shared by solaris_section_bg_image() below and
 * the Hero overflow image.
 */
function solaris_image_filter_css_map() {
	return array(
		'none'      => 'none',
		'grayscale' => 'grayscale(1)',
		'blur'      => 'blur(4px)',
		'sepia'     => 'sepia(0.6)',
		'darken'    => 'brightness(0.55)',
	);
}

function solaris_section_bg_image( $prefix ) {
	$image = solaris_get_option( $prefix . '_bg_image' );
	if ( $image ) {
		$filter_map = solaris_image_filter_css_map();
		$filter   = $filter_map[ solaris_get_option( $prefix . '_bg_image_filter' ) ] ?? 'none';
		$opacity  = solaris_sanitize_float( solaris_get_option( $prefix . '_bg_image_opacity' ) );
		$repeat   = solaris_get_option( $prefix . '_bg_image_repeat' );
		$position = solaris_get_option( $prefix . '_bg_image_position' );
		$size     = 'no-repeat' === $repeat ? 'cover' : 'auto';
		printf(
			'<div class="section-bg-image" style="background-image:url(%s);opacity:%s;filter:%s;background-repeat:%s;background-position:%s;background-size:%s;" aria-hidden="true"></div>',
			esc_url( $image ),
			esc_attr( $opacity ),
			esc_attr( $filter ),
			esc_attr( $repeat ),
			esc_attr( $position ),
			esc_attr( $size )
		);
	}

	if ( solaris_get_option( $prefix . '_bg_overlay_enabled' ) ) {
		$angle   = absint( solaris_get_option( $prefix . '_bg_overlay_angle' ) );
		$opacity = solaris_sanitize_float( solaris_get_option( $prefix . '_bg_overlay_opacity' ) );
		$start   = solaris_hex_to_rgb( solaris_get_option( $prefix . '_bg_overlay_start_color' ) );
		$end     = solaris_hex_to_rgb( solaris_get_option( $prefix . '_bg_overlay_end_color' ) );
		printf(
			'<div class="section-bg-overlay" style="background-image:linear-gradient(%sdeg, rgba(%s, %s) 0%%, rgba(%s, %s) 100%%);" aria-hidden="true"></div>',
			esc_attr( $angle ),
			esc_attr( $start ),
			esc_attr( $opacity ),
			esc_attr( $end ),
			esc_attr( $opacity )
		);
	}
}

/*
 * Fighter-Z: a single decorative fighter-jet silhouette that flies straight
 * across the hero on an occasional "flyby" (assets/js/hero-jet.js drives the
 * actual randomized direction/timing on the frontend — this just renders the
 * static markup once per page load). Top-down icon-style silhouette, nose
 * pointing right by default; the frontend JS mirrors it via scaleX(-1) for
 * leftward passes. Placeholder path pending the final verified design.
 */
function solaris_hero_jet_path() {
	return array(
		'view_box' => '0 0 210 90',
		'path'     => 'M205,45 L192,42 L175,41 L160,40 L148,24 L136,40 L115,40 L100,40 L48,12 L72,41 L55,41 L38,41 L32,40 L24,26 L18,41 L12,41 L8,40 L8,50 L12,49 L18,49 L24,64 L32,50 L38,49 L55,49 L72,49 L48,78 L100,50 L115,50 L136,50 L148,66 L160,50 L175,49 L192,48 Z',
		'extra'    => '',
	);
}

/**
 * Whether Fighter-Z should render on the CURRENT request — "All Pages", or
 * only the pages picked in "Specific Pages" (solaris_sanitize_jet_pages()
 * stores -1 for the front page, since that isn't always a real page ID —
 * e.g. Settings > Reading set to show latest posts).
 */
/**
 * Shared "Show On" scope check for every all-pages/specific-pages display
 * toggle (Fighter-Z, Fly Birds, ...) — $pages_json comes from a setting
 * sanitized by solaris_sanitize_page_ids(), so -1 means "the front page".
 */
function solaris_page_scope_matches( $display_mode, $pages_json ) {
	if ( 'specific_pages' !== $display_mode ) {
		return true;
	}

	$pages = json_decode( $pages_json, true );
	if ( ! is_array( $pages ) ) {
		return false;
	}
	if ( is_front_page() && in_array( -1, $pages, true ) ) {
		return true;
	}
	$real_page_ids = array_values( array_filter( $pages, static fn( $id ) => (int) $id > 0 ) );
	return ! empty( $real_page_ids ) && is_page( $real_page_ids );
}

function solaris_jet_should_display() {
	return solaris_get_option( 'hero_jet_enabled' )
		&& solaris_page_scope_matches( solaris_get_option( 'hero_jet_display_mode' ), solaris_get_option( 'hero_jet_pages' ) );
}

function solaris_fly_birds_should_display() {
	return solaris_get_option( 'fly_birds_enabled' )
		&& solaris_page_scope_matches( solaris_get_option( 'fly_birds_display_mode' ), solaris_get_option( 'fly_birds_pages' ) );
}

function solaris_render_hero_jet() {
	if ( ! solaris_jet_should_display() ) {
		return;
	}

	$source        = solaris_get_option( 'hero_jet_source' );
	$width         = absint( solaris_get_option( 'hero_jet_width' ) );
	$top_offset    = absint( solaris_get_option( 'hero_jet_top_offset' ) );
	$thrust_length = absint( solaris_get_option( 'hero_jet_thrust_length' ) );
	$aspect        = '210 / 90'; // Fallback: the built-in silhouette's own viewBox ratio.

	if ( $source ) {
		$body = sprintf( '<img class="hero-jet__body" src="%s" alt="" />', esc_url( $source ) );

		// Derive the box's aspect ratio from the uploaded image's own real
		// dimensions so Height is never set independently of Width — a
		// mismatched box only ever produced a stretched/distorted jet or
		// (with object-fit:contain) a shrunken one with a gap to the thrust
		// flame. SVGs usually have no stored width/height in WP's attachment
		// metadata, in which case this silently keeps the fallback above.
		$attachment_id = attachment_url_to_postid( $source );
		if ( $attachment_id ) {
			$image_src = wp_get_attachment_image_src( $attachment_id, 'full' );
			if ( $image_src && ! empty( $image_src[1] ) && ! empty( $image_src[2] ) ) {
				$aspect = $image_src[1] . ' / ' . $image_src[2];
			}
		}
	} else {
		$jet  = solaris_hero_jet_path();
		$body = sprintf(
			'<svg class="hero-jet__body" viewBox="%s" fill="currentColor"><path d="%s"/>%s</svg>',
			esc_attr( $jet['view_box'] ),
			esc_attr( $jet['path'] ),
			$jet['extra'] // Trusted, theme-authored SVG markup, not user input.
		);
	}

	$sound_toggle = '';
	if ( solaris_get_option( 'hero_jet_sound_icon_enabled' ) ) {
		$sound_toggle = sprintf(
			'<button type="button" class="hero-jet-sound-toggle is-playing" aria-pressed="true" aria-label="%s">%s%s</button>',
			esc_attr__( 'Mute fighter jet engine sound', 'solaris' ),
			solaris_icon( 'volume-off', array( 'class' => 'hero-jet-sound-icon hero-jet-sound-icon--off' ) ),
			solaris_icon( 'volume', array( 'class' => 'hero-jet-sound-icon hero-jet-sound-icon--on' ) )
		);
	}

	// Wrapped together (not siblings of .hero-jet directly) so the single
	// is-facing-left flip transform on .hero-jet__craft carries the thrust
	// flame along with the jet body — it always trails the tail regardless
	// of which way the jet is currently facing.
	printf(
		'<div class="hero-jet-layer" aria-hidden="false">' .
			'%s' .
			'<span class="hero-jet" data-jet="1" style="width:%dpx;aspect-ratio:%s;top:calc(var(--header-height) + %dpx);">' .
				'<span class="hero-jet__craft">' .
					'%s' .
					'<span class="hero-jet-thrust" style="width:%dpx;"></span>' .
				'</span>' .
			'</span>' .
		'</div>',
		$sound_toggle, // Built above from fixed theme markup, or '' when the icon is turned off.
		$width,
		esc_attr( $aspect ),
		$top_offset,
		$body, // Either trusted inline SVG or an <img> with an esc_url()'d src, built above.
		$thrust_length
	);
}

/**
 * The 10-pose wing-flap cycle the site owner supplied (originally a sprite
 * sheet at assets/images/bird-cells.svg — self-hosted, not hotlinked to the
 * source CodePen's CDN copy). Each pose is inlined here as its own
 * (viewBox, path) pair rather than referenced as an external image/mask:
 * this project's asset-loading sandbox for previewing changes couldn't load
 * ANY externally-referenced CSS resource (background-image or mask-image
 * alike, even same-origin), so inlining is what's actually been verified to
 * render — and it's also the same fill="currentColor" technique already
 * used everywhere else in this theme, so Birds Color (fixed/random) just
 * works with no separate recoloring mechanism needed.
 */
function solaris_fly_bird_frames() {
	return array(
		array( 'view_box' => '0.5 0 359 510', 'd' => 'M51 288.38c13.33-5.34 35.33-9.34 66-12 0 0 18.44-9.95 55-15 1.04-.15 7.5-8 36-13 16 0 21-3.88 30 5 3.53-13.53 19.13-16.58 29-15 6.9 1.1 11.3 5.93 12 8 4 12 21 5 30 12-23 6-23 7-32 11-16 14-31 31-47 31-16 4-32.83 5.33-50.5 4-1 4-3.5 6.66-7.5 8-6 2-1-9-4-12s-3 10-17 18c-1-14-5-10-5-18s4 13-17 18c-14 3.33-31.67 4-53 2 26-8.67 41.33-16.34 46-23-5 0-18-1-21-9-14-4.5-30.67-4.5-50 0z' ),
		array( 'view_box' => '359.5 0 359 510', 'd' => 'M639 273c0 2.07-10 3-16 5-9 1-8 8-14 7s-5 8-19 14c2 11 21 9 36 19 29 30.62 18 19 37 63 14 28 18 30 27 57-1 1-11-1-14-7-1 5-5 1-4 7-5-1-8-8-12-11-4-1 1 7-2 8-4 0-5-8-9-8-4 1 1 6-2 7-33-30-43-21-43-36-8 10-10-2-19 3-4-5-6-10-2-17-1-5-7 5-11 3-1.33-6 1-12.67 7-20-6.67 6.67-14 10-22 10 1-10-1-6.5 5-16-6 7.5-13 8-19 6-4.43-2.1 4-8 2-11.5-8 0-22.76-.3-24-3.5-3.26-8.4 8-19 4-25-3.12-4.67-12 9-15-3 0-12-4.67-17-14-15-8.67-1.33-19.33-1.33-32 0-19 2-27-5-58-5 10-5 9-13 27-13-21-3-10-10-45-13 30-8 32 0 54 7 68-7 22-15 94-22 14-2 36-4 58 2 5-2 6-5 15-6 8 0 12.67 3.67 14 11 10.67.72 16 1.72 16 3z' ),
		array( 'view_box' => '718.5 0 359 510', 'd' => 'M729 299.03c32-14 56-26 90-40 20-6 37-5 56-8 28 0 17-8 55-3 8-1 10.95-7.5 19-7 8.67 0 13.33 2.66 14 8l26 5-24 5c-2.67 5.33-8 8.33-16 9-12 12-11 18-29 21 22 13 23 17 64 38 13 11 13 15 17 31 4 15.98 14.06 25.74 21 35 26 46 23 59 45 80-11-1-23-8-28-13-4-1-3 5-3 8-6-2-8-15-16-13-3.46 0 4 5 0 13-13-5-14-7-16-13-3-4-7-11-3-12-2 8 0 12-6 12-5-10-7-3-11-12-8-14-17-12-17-29-4 0-7 4-10 9-3-4 0-9-1-12-2 .66-4.33 1.66-7 3-2-12.67-2-21.67 0-27-8 2-4 6-15 10-5-6-4-12-2-21-5 6-5-3-12 2-9-9-10-18-25-18-5-12-3-20-11-22-3.23.86-4 10-6 7s-2-10-5-12c-2-3-6 6-9 3 0-5-2-11-6-14-3-4-5 0-10 3-2-5-2-9-1-14-4-1-14 5-12-2 0-7-4-4-8-4-5 0 1-5-1-6-5-2-13 2-21 6-5-2 4-10 0-10-31-1-38 7-76 4z' ),
		array( 'view_box' => '1077.5 0 370 510', 'd' => 'M1367 254.1l-18-3c-2-6-5.33-9.35-10-10-14-1-17 7-22 10-21-3-19-1-35 0-30-5-36 4-50 7-36 5-33 9-48 16-34 18-41 19-75 29 6.67.65 14 0 22-2-14.67 4-21 6.32-19 7 31-5 29.02 4.23 52 0 11-3 10-9 13-12 2 7 1 7 2 13 16-3 21-2 30-2 3 4-3.66 8.6 0 10 4-1.35 9.33-2.68 16-4-4 4.65-5.67 8.32-5 11 15 2 12 4 24 1-11 4-11 4-16 10 16 3 14 1 24 2-4 4-3 8-1 11 6-3 9.53-4.5 12-3-3 3.44-5 7-2 9 10-2 12.4-5.28 15-2-3 8 0 11 1 22 5 0 10-2 15-4 1 8-3 8-3 16 6 3 13-3 17-1-7 4-7 6-8 9 7 3 6 2 14 0-5.33 4-7 7.65-5 11 5.33-3.35 8.33-3.35 9 0-2 6 0 7 1 15 10-5 10 2 21 5 5.97 3.27 6.4 10.54 11 14 9 6.74 20 9 19 9s-9-11-5-10c3.33 4 7.67 8.32 13 13 0-8-.67-13.68-2-17 9 14 12 12 28 23 3-13-6-18-15-28 19 22 19 20 30 24-24-30-20-26-29-53-14-26-24-61-39-75-18-12-19-17-38-26-3.33-4.68-7.33-8-12-10 12-2 21.33-5.68 28-11 2-5 7-5 11-9 6-1.35 9.67-4.35 11-9l18-6z' ),
		array( 'view_box' => '1448.5 0 370 510', 'd' => 'M1751 256c-11-1-12-4-24-6-3-6-4-5-10-7-14 0-21 7-29 12-24-3-31-1-44 2-46 12-43 12-70 23-46 27-39 30-58 40 23 6 31-3 47-7-6.67 2.67-8.67 6-6 10 6.67-2.67 11-3.67 13-3-5 2-9.7 10.57-5 10 27-2 30.7-14.27 46-15-4 5-6 8-4 13 12-3 11-2 18-4-2 6-3 6 0 15 8.5 2 13-5 13-5 4 4 0 11 0 20 11 0 11 2 15 0-2 8 2 8 5 14 4-8 7-5 14-12-2 13 5 12 6 21 10-9 11-11 13-21-4 15 2 17 5 28 4-7 7-7 8-16 1 5 3.98 7.87 5 6 3-10 3.32-8.12 4-6-3 13 2 16 5 27 7-12 5-18 5-19 2 15 15 8 28 33 4-14-16-38-23-73-4-23-3-28-14-43-3-7-10-8-13-7 8-4 7-8 12-15 15-2 11-5 15-10-1 0 5-4 23-5z' ),
		array( 'view_box' => '1818.5 0 370 510', 'd' => 'M2119.66 270.36c0-3-11-6-20-6-1.34-2.67-3.67-5-7-7-5.34-2.67-12.34-2.67-21 0-8 1-16-13-8-26 7-3 10-9 11-14-8-4-11-5-22-4-65 4-85 28-110 50-8 7 10 5 18 5-42 0-78 41-72 37s16 6 35-11c2-1-7 5-15 15 21-3 21-6 39-4-2 7 3.6 7.22 9 8 4.33.62 9.2-.44 15 0 12.53 1.4 13-1 18-1 6 0 5.35.6 9 0 9-2 7-3 10-7-3 8 1 11 6 21 7-6 6-14 6-19 0 13-1 9 2 17 6-5 6-4 8-12-3 10-1 17 3 28 5 0 6.84-4.66 10-3 0 5 0 3 6 13 5-5 2 3 7-8-1 10 4 4 10 18 5-10 3-14-1-23 6 13 8 12 13 19 2-19-1-23-7-36 3-19-6-20-5-33.5 8-10.5 15 2 24-6.5 7-1 4-4 9-7 8-5 19 1 20-3z' ),
		array( 'view_box' => '2189.5 0 370 510', 'd' => 'M2485 278.84c-6-3-7-1-16-3 1-5-4.66-10-11-10-9.16 0-9.5 5.2-21 8-9 2.2-12.7 3.03-18-2-24-15-11-19-32-44-9-7-12-11-4-21 13-14 17-22 17-32-4-7-85-3-113 34 5.06 1.15 16.8-6.7 20.65-6 4.35 1-18.65 13-9.65 11 9-2 15-7 21-7 2.67-.05-2 2-3 7 3.78.9 9.8-1.85 16-3 3.52-.65 7.9.34 10 0-4 3-10 1-20 17 6 5.5 14-1.5 20-1.5-3 5.5-11 2.5-15 20.5 2 4 11-6 9-2 0 4-7 2-9 18 6 0 8-7 9-2-2 3-5 3-5 9 3 3 9-4 11-4-1 2-6 2-6 10-38 9-49 18-68 30 7 4.9 23-5 25-1-1.46 3.22-11 5.9-28 11 17 2 31 2 42.65-5.1 10.35 1.1 18.57-1.9 21.35-1.9 4 6 0 13 7 15 3-5 8-12 9-15 3-2-5 11 3 13 9 0 19 7 36 7 18 0 22-6 42-13 12-1 20-5 25-18 3-8 9-4 18-13 2-2 12-3 16-6z' ),
		array( 'view_box' => '2559.5 0 370 510', 'd' => 'M2859 275.74c-.52-2.35-8-3-20-5-6.62-3.1-7.68-9-14-9-13 0-7.83 6.16-21 9-7.7 1.67-15 2-20 0s-6.32-3.7-10-6c-19.2-11.9-10.17.25-19.17-20-10.54-12-10.6-24.55-6.83-42 .97-4.5 2.27-8.15 4-11 6-9.83 12.67-30.6 11-45-1.4-12.16-11-14-27-17-21-4-33-5-58-2-9 8-21 5-33 21 9-1 7-3 14 3 8-4 15.48-1.3 16-1 7.16 4.13-3.54 3.73 1 6 6 0 2 4 0 9 5-.5 8-1 6 4 4.4 3.22 8.36-3.24 9 1 .86 5.76-11.7 9.4-9 17 18 3 20 5 22 7-4 1 3 2-26 11 3 10 14 6 15 16 0 7-9 4-12 17 12 2 9 6 23 6-5 0-18 4-26 12 9 2 6 6 14 7 7 3 6 3.36 4 7-6.5 11.86-5 15-20 23s-26.3 15.78-35 22c-4.63 3.32-7.48 8.23-11 12 12-2 12-3 15-2 2 4 1-3 0 11 6-5 16-12 22-16 5 0 13-3 17 0 5 0 0-7 0-8.96 0-1.96 3.48 5.8 5 3.96 5-6 4-9 3-14 4 3 1 10.9 6 12 19 6 21 5 42-1 1.2-.07 2.95-.92 4.3-.96 1.2 1.42.7 3 3.7 3.96 1.57-2.35 4.93-3.06 6.83-3.1.52 0-.24-.93 0-.86 10 3 17.17 3.96 32.17.96 13-8 24-5 35-17 14-6 14-10 17-16 4-3 9 0 20-4z' ),
		array( 'view_box' => '2930.5 0 370 510', 'd' => 'M3243 286c-8-3-7-2-17-4-7.6-3.28-5-11-21-13-16.1 1.03-15 14-31 13-10.43-.65-13-7-21-11s-13-5-19-12c-13-42-1.27-9.56-3-59-.27-7.56-6-44-1-54 8-3 4.84-13.8 0-22-32-34-40.22-58.03-60-79-4-1 5 12-2 7-8.8-5.08-7-14-23-22 1 5 4 12 2 15-5-3-14-23-20-23 1 4 8 11 8 23 0 4-14-6-9-1s7 13 9 19c7.25 13.18 22 23 22 22-3-1-17-12-26-11 2 5 8 7 10 17-2 2 0 3-8 0 4 19 14 28 35 41 2.6 4.24-11.1-4.18-14-1-2.6 2.82 1.67 8.6 5 15 9.17 17.6 18.32 18.92 20 30-1 4-9.77-1.26-14 2-2.27 1.74 4.75 5.74 4 10-.75 4.24-9.22 1.68-8 6 2.48 2.98-2 12-1 17s4 6 4 11 7-2 8 6c5 4-1 4-8 12 5 5 10 4 8 14s-7 6-12 12c-5 7-3 11-10 17-9 8-15 4-31 16-13 7-14 2-30 13 18 2 36 12 68 5 5 2-5 5-2 7 14 1 16 3 23 0-4 3-10.62 8.7-7 9 8.53.74 23.38-.95 30 0 8.4 1.2 12.75 5 27 5 7.7 0 16.25-8.18 21-7 7.3 1.8 6 2.93 17 0 19.33-5.15 24-9 39-19 8-12 13-9 20-21 11-2 18-2 18-5z' ),
		array( 'view_box' => '3300.5 0 370 510', 'd' => 'M3595.2 274c0-2.56-5-4-13-7-4.25-3.13 0-8-10-10-17 0-11.03 8.98-21 12-10-1-9-2-21 1-4.15 1.04-3.9-10.8-6-13-2.97-3.06-5-3-10-8-3-11 6-24 3-45-1.34-9.32-4.68-21.56-9-33-2.45-13.1-2-10-13-23-21-28-37-37-47-67-4.96-4.56-.68 12.44-5 11-7.68-2.56-15.4-19-19-19-2.9 0 7.5 17.9 4 23-3 4.4-17-7.67-17-5 0 3.33 8 16 15 19-4 4-9.5-4.6-11-1 7 9 1 11 6 24 5.77 12.05 11 8 16 18-3.62 4.17-24.5-6.8-26-3-1.02 2.53 9.7 2.7 17 10 6 6 1.8 13.48 6 16 5 3 2 6 8 13s10-2 8 8c-.94 4.66-4 4.34-4 9 0 5.34 8-1 6 5 0 2.57-4 4-6 8 0 .72 7.58-1.47 8 1 1.08 6.53-7.23 11.8-4 17 3.27 5.3 12 4 17 3-5 4-13 3.95-13 9 0 9.45 10 13 4 18 2 5-5 9-4 13s5.17 8.47 10 10c3.24 1.03 9 2 6 3-6 2-7-1-12 3-4.25 0-2-6-10-4-24 4-60 20-73 34-2 3 12-3 21-4-8 3-20 7-5 6 11 1 28 0 57-5 21.46-.66 17 7 39 2 11 3 28 6 43-5 14-2 15.58-9.85 33-23 6-2 11-11 14-17 7.8-3.53 12-1 18-4z' ),
	);
}

/**
 * "Fly Birds" — a separate, site-wide decorative flock (Customizer: Fly
 * Birds). Color/size are picked per-bird from a small deterministic-by-
 * index formula rather than mt_rand(), so the markup is identical on every
 * request — page caches don't freeze one random draw forever, and it's
 * easy to reason about.
 */
function solaris_render_fly_birds() {
	if ( ! solaris_fly_birds_should_display() ) {
		return;
	}

	$quantity    = max( 1, absint( solaris_get_option( 'fly_birds_quantity' ) ) );
	$color_mode  = solaris_get_option( 'fly_birds_color_mode' );
	$fixed_color = solaris_get_option( 'fly_birds_color' );
	$size_mode   = solaris_get_option( 'fly_birds_size_mode' );
	$fixed_size  = absint( solaris_get_option( 'fly_birds_size' ) );
	$size_min    = absint( solaris_get_option( 'fly_birds_size_min' ) );
	$size_max    = absint( solaris_get_option( 'fly_birds_size_max' ) );
	$base_speed  = solaris_sanitize_float( solaris_get_option( 'fly_birds_speed' ) ) ?: 15;
	$frames      = solaris_fly_bird_frames();
	$frame_count = count( $frames );

	echo '<div class="fly-birds-layer" aria-hidden="true">';

	for ( $i = 0; $i < $quantity; $i++ ) {
		$duration_factor = 0.82 + ( $i % 5 ) * 0.09;
		$duration        = round( $base_speed * $duration_factor, 2 );
		$delay           = round( ( $i / $quantity ) * $duration * -1, 2 ); // Negative: starts mid-cycle, so the flock is already spread out on first paint.
		$top             = $quantity > 1 ? round( 5 + ( $i * 70 / ( $quantity - 1 ) ), 1 ) : 20;
		$flap_duration   = round( 0.7 + ( $i % 3 ) * 0.1, 2 ); // One full 10-cell wing-flap cycle.
		$path_variant    = ( 0 === $i % 2 ) ? 1 : 2;

		if ( 'random' === $color_mode ) {
			$color = sprintf( 'hsl(%d, 55%%, 65%%)', ( $i * 67 ) % 360 );
		} else {
			$color = $fixed_color;
		}

		if ( 'range' === $size_mode && $size_max > $size_min ) {
			$size = $size_min + ( ( $i * 37 ) % ( $size_max - $size_min + 1 ) );
		} else {
			$size = $fixed_size;
		}

		$pose_frames = '';
		foreach ( $frames as $frame_index => $frame ) {
			$frame_delay = round( ( $frame_index / $frame_count ) * $flap_duration * -1, 3 );
			$pose_frames .= sprintf(
				'<svg class="fly-bird__frame" viewBox="%s" fill="currentColor" style="animation-duration:%ss;animation-delay:%ss;"><path d="%s"/></svg>',
				esc_attr( $frame['view_box'] ),
				esc_attr( $flap_duration ),
				esc_attr( $frame_delay ),
				esc_attr( $frame['d'] )
			);
		}

		printf(
			'<span class="fly-bird-container fly-bird-container--v%d" style="top:%s%%;animation-duration:%ss;animation-delay:%ss;">' .
				'<span class="fly-bird" style="width:%dpx;color:%s;">%s</span>' .
			'</span>',
			$path_variant,
			esc_attr( $top ),
			esc_attr( $duration ),
			esc_attr( $delay ),
			$size,
			esc_attr( $color ),
			$pose_frames // Built above from trusted theme-authored SVG data, not user input.
		);
	}

	echo '</div>';
}
