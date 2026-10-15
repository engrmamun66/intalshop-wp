<?php
/**
 * Generic (non-WooCommerce) hook wiring: body classes, skip link, excerpt tuning.
 * WooCommerce-specific hooks live in inc/woocommerce.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function solaris_body_classes( $classes ) {
	if ( is_front_page() && solaris_get_option( 'header_transparent' ) ) {
		$classes[] = 'has-transparent-header';
	}
	if ( solaris_get_option( 'sticky_header' ) ) {
		$classes[] = 'has-sticky-header';
	}
	return $classes;
}
add_filter( 'body_class', 'solaris_body_classes' );

/**
 * The "no-js" class lives on <html> (set in header.php) rather than <body>,
 * because <body> doesn't exist yet when this early <head> script runs —
 * removing it from a not-yet-parsed node would silently no-op and the
 * scroll-reveal / FAQ CSS would stay in its no-JS (always-visible) state.
 */
function solaris_no_js_script() {
	echo "<script>document.documentElement.classList.remove('no-js');</script>\n";
}
add_action( 'wp_head', 'solaris_no_js_script', 1 );

/**
 * Global full-page preloader — every page, not a Customizer toggle.
 * assets/css/preloader.css owns the visuals; assets/js/preloader.js hides
 * it 2 seconds after the window's "load" event. Registered at the default
 * priority (10) but declared first in this file, and wp_body_open itself
 * fires before any theme/plugin content, so this paints before anything
 * it's meant to cover ever has a chance to render.
 */
function solaris_render_preloader() {
	echo '<div class="solaris-preloader"><span class="solaris-preloader__spinner"></span></div>';
}
add_action( 'wp_body_open', 'solaris_render_preloader' );

function solaris_skip_link() {
	echo '<a class="skip-link screen-reader-text" href="#main">' . esc_html__( 'Skip to content', 'solaris' ) . '</a>';
}
add_action( 'wp_body_open', 'solaris_skip_link' );

/**
 * Fighter-Z used to be rendered from template-parts/home/hero.php, tied to
 * the front page's hero markup. It's now its own Customizer section (Show
 * On: All Pages / Specific Pages) with no dependency on a hero section
 * existing at all, so it hooks globally here instead — solaris_render_hero_jet()
 * still owns its own solaris_jet_should_display() gate internally.
 */
add_action( 'wp_footer', 'solaris_render_hero_jet' );

/**
 * "Fly Birds" — a site-wide decorative flock (Customizer: Fly Birds).
 */
add_action( 'wp_footer', 'solaris_render_fly_birds' );

function solaris_excerpt_more( $more ) {
	return is_admin() ? $more : '&hellip;';
}
add_filter( 'excerpt_more', 'solaris_excerpt_more' );

function solaris_excerpt_length( $length ) {
	return 24;
}
add_filter( 'excerpt_length', 'solaris_excerpt_length', 999 );

/**
 * WordPress's legacy emoji-detection script rewrites text nodes into
 * <img class="emoji"> tags for browsers it decides can't render emoji
 * natively — every current browser can, so it's dead weight, but its
 * detection is also the likely cause of "&ldquo;Product Name&rdquo; has
 * been added to your cart" occasionally rendering the opening curly quote
 * + first letter as a small flag/keycap-style icon instead of text: its
 * regex over dynamically-injected notice HTML can misfire on ordinary
 * punctuation+letter sequences. Removing it is a standard, safe cleanup
 * (real emoji typed by users still render fine via the OS/browser's own
 * font) rather than something this site relies on.
 */
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );
remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
add_filter( 'emoji_svg_url', '__return_false' );
