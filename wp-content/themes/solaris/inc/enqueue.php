<?php
/**
 * Style/script registration. Fonts limited to the weights actually used;
 * JS is a single small vanilla file (no framework) split logically by feature.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function solaris_fonts_url() {
	$fonts_url = '';
	$subsets   = 'latin';

	$font_families = array(
		'Space+Grotesk:wght@500;700',
		'Manrope:wght@700;800',
		'Inter:wght@400;500;600;700',
	);

	$fonts_url = add_query_arg(
		array(
			'family'  => implode( '&family=', $font_families ),
			'subset'  => $subsets,
			'display' => 'swap',
		),
		'https://fonts.googleapis.com/css2'
	);

	return $fonts_url;
}

/**
 * Version a theme asset by its file's last-modified time instead of the
 * static SOLARIS_VERSION constant. Editing a CSS/JS file during development
 * previously left every visitor's browser holding onto its old cached copy
 * indefinitely, since the query string never changed — this makes every
 * save automatically bust the cache without needing a hard refresh.
 */
function solaris_asset_version( $relative_path ) {
	$file_path = SOLARIS_DIR . $relative_path;
	return file_exists( $file_path ) ? (string) filemtime( $file_path ) : SOLARIS_VERSION;
}

function solaris_enqueue_assets() {
	wp_enqueue_style( 'solaris-fonts', solaris_fonts_url(), array(), null );

	wp_enqueue_style( 'solaris-variables', SOLARIS_URI . '/assets/css/variables.css', array(), solaris_asset_version( '/assets/css/variables.css' ) );
	wp_enqueue_style( 'solaris-preloader', SOLARIS_URI . '/assets/css/preloader.css', array( 'solaris-variables' ), solaris_asset_version( '/assets/css/preloader.css' ) );
	wp_enqueue_script( 'solaris-preloader', SOLARIS_URI . '/assets/js/preloader.js', array(), solaris_asset_version( '/assets/js/preloader.js' ), true );
	wp_enqueue_style( 'solaris-base', SOLARIS_URI . '/assets/css/base.css', array( 'solaris-variables' ), solaris_asset_version( '/assets/css/base.css' ) );
	wp_enqueue_style( 'solaris-tooltip', SOLARIS_URI . '/assets/css/tooltip.css', array( 'solaris-variables' ), solaris_asset_version( '/assets/css/tooltip.css' ) );
	wp_enqueue_style( 'solaris-buttons', SOLARIS_URI . '/assets/css/buttons.css', array( 'solaris-base' ), solaris_asset_version( '/assets/css/buttons.css' ) );
	wp_enqueue_style( 'solaris-forms', SOLARIS_URI . '/assets/css/forms.css', array( 'solaris-base' ), solaris_asset_version( '/assets/css/forms.css' ) );
	wp_enqueue_style( 'solaris-header', SOLARIS_URI . '/assets/css/header.css', array( 'solaris-base' ), solaris_asset_version( '/assets/css/header.css' ) );
	wp_enqueue_style( 'solaris-footer', SOLARIS_URI . '/assets/css/footer.css', array( 'solaris-base' ), solaris_asset_version( '/assets/css/footer.css' ) );
	wp_enqueue_style( 'solaris-home', SOLARIS_URI . '/assets/css/home.css', array( 'solaris-base' ), solaris_asset_version( '/assets/css/home.css' ) );

	if ( is_front_page() && solaris_get_option( 'hero_solar_system_enabled' ) ) {
		wp_enqueue_style( 'solaris-solar-system', SOLARIS_URI . '/assets/css/solar-system.css', array( 'solaris-home' ), solaris_asset_version( '/assets/css/solar-system.css' ) );
	}

	if ( solaris_jet_should_display() ) {
		wp_enqueue_style( 'solaris-hero-jet', SOLARIS_URI . '/assets/css/hero-jet.css', array( 'solaris-home' ), solaris_asset_version( '/assets/css/hero-jet.css' ) );
		wp_enqueue_script( 'solaris-hero-jet', SOLARIS_URI . '/assets/js/hero-jet.js', array(), solaris_asset_version( '/assets/js/hero-jet.js' ), true );

		$jet_sound_id  = absint( solaris_get_option( 'hero_jet_sound' ) );
		$jet_sound_url = $jet_sound_id ? wp_get_attachment_url( $jet_sound_id ) : '';

		$jet_volume_by_mode = array( 'low' => 0.5, 'medium' => 0.75, 'high' => 1.0 );
		$jet_volume         = $jet_volume_by_mode[ solaris_get_option( 'hero_jet_sound_mode' ) ] ?? $jet_volume_by_mode['medium'];

		wp_localize_script(
			'solaris-hero-jet',
			'solarisHeroJet',
			array(
				'duration'         => solaris_sanitize_float( solaris_get_option( 'hero_jet_speed' ) ) ?: 5,
				'soundUrl'         => $jet_sound_url ?: '',
				'pauseMinMs'       => absint( solaris_get_option( 'hero_jet_pause_min' ) ) * 1000,
				'pauseMaxMs'       => absint( solaris_get_option( 'hero_jet_pause_max' ) ) * 1000,
				'volumeMultiplier' => $jet_volume,
			)
		);
	}

	if ( solaris_fly_birds_should_display() ) {
		wp_enqueue_style( 'solaris-fly-birds', SOLARIS_URI . '/assets/css/fly-birds.css', array( 'solaris-home' ), solaris_asset_version( '/assets/css/fly-birds.css' ) );
	}

	wp_enqueue_style( 'solaris-style', get_stylesheet_uri(), array(), solaris_asset_version( '/style.css' ) );

	if ( class_exists( 'WooCommerce' ) ) {
		wp_enqueue_style( 'solaris-woocommerce', SOLARIS_URI . '/assets/css/woocommerce.css', array( 'solaris-base' ), solaris_asset_version( '/assets/css/woocommerce.css' ) );
		wp_enqueue_style( 'solaris-search-modal', SOLARIS_URI . '/assets/css/search-modal.css', array( 'solaris-base' ), solaris_asset_version( '/assets/css/search-modal.css' ) );

		if ( 'page' !== solaris_get_option( 'cart_click_action' ) ) {
			wp_enqueue_style( 'solaris-cart-panel', SOLARIS_URI . '/assets/css/cart-panel.css', array( 'solaris-base' ), solaris_asset_version( '/assets/css/cart-panel.css' ) );
			/*
			 * Enables real AJAX remove-from-cart inside the panel (binds
			 * .remove_from_cart_button, no page reload) and keeps
			 * .widget_shopping_cart_content in sync on load — otherwise
			 * only enqueued by WC's own Cart widget, which this theme
			 * doesn't use. See template-parts/components/cart-panel.php.
			 */
			wp_enqueue_script( 'wc-cart-fragments' );
		}

		if ( solaris_get_option( 'quick_view_enabled' ) ) {
			wp_enqueue_style( 'solaris-quick-view', SOLARIS_URI . '/assets/css/quick-view.css', array( 'solaris-base' ), solaris_asset_version( '/assets/css/quick-view.css' ) );
			// Variation swapping/quantity behavior for the add-to-cart form
			// the quick-view modal injects — normally only enqueued on the
			// single-product page itself (below), but a quick-viewed product
			// can be variable too, and quick view can appear on any page
			// with a product loop (shop, category, homepage).
			wp_enqueue_script( 'wc-single-product' );
		}
	}

	if ( is_singular( 'post' ) || is_page_template( 'page-blog.php' ) || is_page( 'blog' ) ) {
		wp_enqueue_style( 'solaris-blog', SOLARIS_URI . '/assets/css/blog.css', array( 'solaris-home' ), solaris_asset_version( '/assets/css/blog.css' ) );
	}

	wp_enqueue_script( 'solaris-main', SOLARIS_URI . '/assets/js/main.js', array(), solaris_asset_version( '/assets/js/main.js' ), true );
	wp_localize_script(
		'solaris-main',
		'solarisData',
		array(
			'ajaxUrl'                  => admin_url( 'admin-ajax.php' ),
			'searchNonce'              => wp_create_nonce( 'solaris_search_modal' ),
			'quickViewNonce'           => wp_create_nonce( 'solaris_quick_view' ),
			'flyToCartEnabled'         => class_exists( 'WooCommerce' ) ? (bool) solaris_get_option( 'fly_to_cart_enabled' ) : false,
			'flyToCartMultiEnabled'    => class_exists( 'WooCommerce' ) ? (bool) solaris_get_option( 'fly_to_cart_multi_enabled' ) : false,
			'openCartPanelOnAddToCart' => class_exists( 'WooCommerce' ) ? (bool) solaris_get_option( 'open_cart_panel_on_add_to_cart' ) : false,
			/*
			 * WC_AJAX::add_to_cart()'s own endpoint — the exact same handler
			 * the shop-loop's ajax_add_to_cart links already POST to. The
			 * single-product page's own form.cart isn't ajax-ified by
			 * WooCommerce core at all (only loop buttons are); main.js
			 * (initSingleProductAjaxAddToCart) reuses this same endpoint so
			 * that page gets the identical no-reload behavior instead of
			 * reimplementing add-to-cart logic.
			 */
			'wcAjaxAddToCartUrl'       => class_exists( 'WooCommerce' ) ? WC_AJAX::get_endpoint( 'add_to_cart' ) : '',
		)
	);

	if ( is_singular( 'product' ) ) {
		wp_enqueue_script( 'wc-single-product' );
	}

	if ( function_exists( 'is_checkout' ) && is_checkout() ) {
		wp_enqueue_script( 'solaris-checkout-actions', SOLARIS_URI . '/assets/js/checkout-actions.js', array(), solaris_asset_version( '/assets/js/checkout-actions.js' ), true );
		wp_localize_script(
			'solaris-checkout-actions',
			'solarisCheckoutActions',
			array(
				'continueShoppingUrl'  => solaris_shop_url(),
				'continueShoppingText' => __( 'Continue Shopping', 'solaris' ),
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'solaris_enqueue_assets' );

/**
 * Print the Customizer-driven CSS variable overrides right after the base
 * design-system stylesheet, so admin color/typography changes always win.
 */
function solaris_inline_customizer_css() {
	wp_add_inline_style( 'solaris-variables', solaris_customizer_css() );
}
add_action( 'wp_enqueue_scripts', 'solaris_inline_customizer_css', 20 );

function solaris_editor_assets() {
	add_editor_style( array( 'assets/css/variables.css', 'assets/css/base.css' ) );
}
add_action( 'admin_init', 'solaris_editor_assets' );
