<?php
/**
 * WooCommerce integration. Presentation only — no cart/pricing/tax logic is
 * reimplemented here, everything routes through core WooCommerce hooks/APIs
 * so plugin updates, payment gateways and Blocks keep working unmodified.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

/* --------------------------------------------------------- Layout wrapper */

function solaris_wc_is_sidebar_context() {
	return is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy();
}

remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
// Shop/category archive page title — separate from woocommerce_page_title()
// above; this one is baked into archive-product.php itself via WC 8.6+'s
// shop-loop-header template part, not the theme wrapper hooks.
remove_action( 'woocommerce_shop_loop_header', 'woocommerce_product_taxonomy_archive_header' );

/*
 * WooCommerce renders the result-count and ordering dropdown as bare
 * siblings that rely on float:left/float:right (from its own CSS) to sit on
 * one line — with no wrapping element for us to flex instead. Wrapping them
 * ourselves via these two hooks (result_count=20, catalog_ordering=30) turns
 * that into a real flex row and removes any dependency on the float/clear
 * interaction with the ul.products grid that follows.
 */
add_action( 'woocommerce_before_shop_loop', function () { echo '<div class="shop-toolbar">'; }, 15 );
add_action( 'woocommerce_before_shop_loop', function () { echo '</div>'; }, 35 );

/**
 * Customizer: Products List → "Show Categories for list Page?" — the shop
 * page and product_cat archives ("categorywise products page"), not
 * product_tag/other taxonomy archives.
 */
function solaris_shop_categories_sidebar_active() {
	return ( is_shop() || is_product_category() ) && solaris_get_option( 'shop_show_categories' ) && solaris_shop_has_categories();
}

function solaris_shop_has_categories() {
	$terms = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'parent'     => 0,
			'hide_empty' => true,
			'number'     => 1,
			'fields'     => 'ids',
		)
	);
	return ! is_wp_error( $terms ) && ! empty( $terms );
}

/**
 * Recursive product_cat <ul>, filtered to top-level terms only when "Show
 * Child Categories?" is off. Active-state (current archive + its ancestors)
 * only matters when actually browsing a category, which this sidebar isn't
 * shown on (solaris_shop_categories_sidebar_active() is is_shop()-only) —
 * kept anyway since it costs nothing and the same function could reasonably
 * be reused from a category archive later.
 */
function solaris_shop_category_tree( $parent = 0, $show_children = true ) {
	$terms = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'parent'     => $parent,
			'hide_empty' => true,
		)
	);
	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return '';
	}

	$queried    = is_product_category() ? get_queried_object() : null;
	$active_ids = $queried instanceof WP_Term ? array_merge( array( $queried->term_id ), get_ancestors( $queried->term_id, 'product_cat' ) ) : array();

	$html = '<ul class="' . ( 0 === $parent ? 'shop-categories__list' : 'shop-categories__children' ) . '">';
	foreach ( $terms as $term ) {
		$html .= '<li class="shop-categories__item' . ( in_array( $term->term_id, $active_ids, true ) ? ' is-active' : '' ) . '">';
		$html .= '<a href="' . esc_url( get_term_link( $term ) ) . '">' . esc_html( $term->name ) . ' <span class="shop-categories__count">' . intval( $term->count ) . '</span></a>';
		if ( $show_children ) {
			$html .= solaris_shop_category_tree( $term->term_id, true );
		}
		$html .= '</li>';
	}
	return $html . '</ul>';
}

function solaris_render_shop_categories() {
	$tree = solaris_shop_category_tree( 0, (bool) solaris_get_option( 'shop_show_child_categories' ) );
	if ( '' === $tree ) {
		return;
	}
	// Collapsed by default below 900px (assets/css/woocommerce.css) — the
	// media query is what gives the class/attribute below any visual
	// effect at all, so a single aria-expanded="false" default is correct
	// for both: collapsed on mobile, and a harmless no-op on desktop where
	// the list is unconditionally shown regardless. Toggling is
	// assets/js/main.js, initShopCategoriesToggle.
	echo '<nav class="shop-categories" aria-label="' . esc_attr__( 'Product categories', 'solaris' ) . '">';
	echo '<button type="button" class="shop-categories__title" aria-expanded="false" aria-controls="shop-categories-list">';
	echo esc_html__( 'Categories', 'solaris' );
	echo solaris_icon( 'chevron-down', array( 'class' => 'shop-categories__toggle-icon' ) );
	echo '</button>';
	echo '<div class="shop-categories__body" id="shop-categories-list">';
	echo $tree; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built entirely from esc_url()/esc_html() above.
	echo '</div>';
	echo '</nav>';
}

function solaris_wc_wrapper_start() {
	$is_shop_context = solaris_wc_is_sidebar_context();

	if ( $is_shop_context ) {
		echo '<div class="container" style="padding-top:32px">';
		echo '<div class="breadcrumb-bar">';
		woocommerce_breadcrumb();
		echo '</div>';
		echo '<main id="main" class="shop-content">';

		if ( solaris_shop_categories_sidebar_active() ) {
			$position = solaris_get_option( 'shop_category_position' );
			// Position (left/right) comes from source order below, not a
			// modifier class — no CSS depends on $position here.
			echo '<div class="shop-layout">';
			if ( 'right' !== $position ) {
				solaris_render_shop_categories();
			}
			echo '<div class="shop-products-column">';
		}
	} else {
		echo '<main id="main" class="container section" style="padding-top:32px">';
		if ( is_singular( 'product' ) ) {
			echo '<div class="breadcrumb-bar">';
			woocommerce_breadcrumb();
			echo '</div>';
		}
	}
}
add_action( 'woocommerce_before_main_content', 'solaris_wc_wrapper_start', 10 );

function solaris_wc_wrapper_end() {
	if ( solaris_wc_is_sidebar_context() ) {
		if ( solaris_shop_categories_sidebar_active() ) {
			$position = solaris_get_option( 'shop_category_position' );
			echo '</div>'; // .shop-products-column
			if ( 'right' === $position ) {
				solaris_render_shop_categories();
			}
			echo '</div>'; // .shop-layout
		}
		echo '</main></div>';
	} else {
		echo '</main>';
	}
}
add_action( 'woocommerce_after_main_content', 'solaris_wc_wrapper_end', 10 );

/* ----------------------------------------------------------- Product loop */

/*
 * Customizer: Products List → Products Per Row (Desktop) / Products Per
 * Page. Related/upsell stay hardcoded at 4 below — that section of the spec
 * only covers the main shop/category grid, not those.
 */
add_filter( 'loop_shop_columns', function () {
	return absint( solaris_get_option( 'shop_columns_desktop' ) ) ?: 4;
} );
add_filter( 'loop_shop_per_page', function () {
	return absint( solaris_get_option( 'shop_products_per_page' ) ) ?: 12;
}, 20 );
add_filter( 'woocommerce_output_related_products_args', function ( $args ) {
	$args['posts_per_page'] = 4;
	$args['columns']        = 4;
	return $args;
} );
add_filter( 'woocommerce_upsell_display_args', function ( $args ) {
	$args['posts_per_page'] = 4;
	$args['columns']        = 4;
	return $args;
} );

/**
 * Swap WC's plain "&larr;"/"&rarr;" pagination prev/next text for the same
 * SVG arrow used on the blog's pagination (assets/css/blog.css), so both
 * paginations — built from different markup (WC's <ul> "list" type vs. the
 * blog's flat paginate_links() output) — read as the same component.
 */
add_filter( 'woocommerce_pagination_args', function ( $args ) {
	$args['prev_text'] = solaris_icon( 'arrow-right', array( 'class' => 'woocommerce-pagination__arrow woocommerce-pagination__arrow--prev' ) );
	$args['next_text'] = solaris_icon( 'arrow-right', array( 'class' => 'woocommerce-pagination__arrow' ) );
	return $args;
} );

/**
 * Customizer: Products List → Products Loading Mode. "Pagination" is WC's
 * own default (woocommerce_pagination on woocommerce_after_shop_loop, set
 * up above) and is left untouched. "Lazy Load"/"Load More" swap that for a
 * sentinel/button that assets/js/main.js (initShopLoading) drives via
 * inc/ajax-shop-loading.php, fetching subsequent pages of the same query
 * without a full page reload. Hooked on 'wp' (after the query, before
 * template output) since removing an already-fired action is a no-op.
 */
add_action( 'wp', function () {
	if ( ! solaris_wc_is_sidebar_context() || 'pagination' === solaris_get_option( 'shop_loading_mode' ) ) {
		return;
	}
	remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 10 );
	add_action( 'woocommerce_after_shop_loop', 'solaris_render_shop_loading_control', 10 );
} );

function solaris_render_shop_loading_control() {
	global $wp_query;
	if ( $wp_query->max_num_pages <= 1 ) {
		return;
	}

	$mode = solaris_get_option( 'shop_loading_mode' );

	$queried_object = get_queried_object();
	$tax            = $queried_object instanceof WP_Term ? $queried_object->taxonomy : '';
	$term           = $queried_object instanceof WP_Term ? $queried_object->slug : '';

	// Same value WC_Query::get_catalog_ordering_args() would derive on this
	// exact page load — captured here so the AJAX handler (which has no
	// $_GET of its own to fall back on, being a POST) sorts identically to
	// whatever the shopper is currently looking at.
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$orderby = isset( $_GET['orderby'] )
		? sanitize_text_field( wp_unslash( $_GET['orderby'] ) )
		: apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby', 'menu_order' ) );

	printf(
		'<div class="shop-loading" data-mode="%1$s" data-tax="%2$s" data-term="%3$s" data-orderby="%4$s" data-page="1" data-max-pages="%5$d" data-nonce="%6$s">',
		esc_attr( $mode ),
		esc_attr( $tax ),
		esc_attr( $term ),
		esc_attr( $orderby ),
		absint( $wp_query->max_num_pages ),
		esc_attr( wp_create_nonce( 'solaris_shop_loading' ) )
	);
	if ( 'load_more' === $mode ) {
		echo '<button type="button" class="button shop-loading__button">' . esc_html__( 'Load More', 'solaris' ) . '</button>';
	}
	echo '<span class="shop-loading__spinner" aria-hidden="true"></span>';
	echo '</div>';
}

function solaris_stock_badge() {
	global $product;
	if ( $product instanceof WC_Product && ! $product->is_in_stock() ) {
		echo '<span class="out-of-stock-badge">' . esc_html__( 'Out of Stock', 'solaris' ) . '</span>';
	}
}
add_action( 'woocommerce_before_shop_loop_item_title', 'solaris_stock_badge', 12 );

/**
 * Real favorite/wishlist toggle. No account system or DB table for it —
 * state lives in the visitor's own browser (localStorage, bound in
 * assets/js/main.js) via the product ID on data-wishlist-toggle. Works for
 * guests and logged-in users alike, survives reloads, needs no backend.
 */
function solaris_wishlist_button() {
	global $product;
	if ( ! $product instanceof WC_Product ) {
		return;
	}
	printf(
		'<button type="button" class="wishlist-toggle" data-wishlist-toggle="%1$d" aria-pressed="false" aria-label="%2$s">%3$s</button>',
		esc_attr( $product->get_id() ),
		esc_attr__( 'Add to favorites', 'solaris' ),
		solaris_icon( 'heart' )
	);
}
add_action( 'woocommerce_before_shop_loop_item_title', 'solaris_wishlist_button', 15 );

/**
 * Quick View button (Customizer: Product Cards → Show Quick View Button).
 * JS (initQuickView, assets/js/main.js) fetches this product's markup from
 * inc/ajax-quick-view.php and shows it in the modal template-parts/
 * components/quick-view-modal.php prints once in header.php — nothing
 * product-specific rendered here beyond the trigger button itself.
 */
function solaris_quick_view_button() {
	global $product;
	if ( ! $product instanceof WC_Product || ! solaris_get_option( 'quick_view_enabled' ) ) {
		return;
	}
	printf(
		'<button type="button" class="quick-view-toggle" data-quick-view="%1$d" aria-haspopup="dialog" aria-label="%2$s">%3$s</button>',
		esc_attr( $product->get_id() ),
		esc_attr__( 'Quick view', 'solaris' ),
		solaris_icon( 'eye' )
	);
}
add_action( 'woocommerce_before_shop_loop_item_title', 'solaris_quick_view_button', 16 );

/**
 * Quantity input next to the price on simple, purchasable, in-stock loop
 * cards — lets a shopper pick a custom quantity before hitting "Add to
 * cart" instead of always adding 1. Routes entirely through WooCommerce's
 * own woocommerce_quantity_input() (same function the single-product page
 * uses), so min/max/step/sold-individually all come from the product's
 * real settings, not reimplemented here. Its pre-filled value comes from
 * the "Default Selected Qty" filter further down this file
 * (woocommerce_quantity_input_args), not set here. assets/js/main.js
 * (initLoopQuantity) copies its value onto the card's add-to-cart link's
 * data-quantity attribute, which is what wc-add-to-cart.js actually reads
 * when it fires the AJAX request.
 *
 * Wrapped around core's own price hook (priority 10) rather than styled as
 * a standalone element, so the two sit in one flex row (.loop-price-row,
 * woocommerce.css) with the price left and quantity right — a wrapper is
 * needed because price and quantity come from two separate hook callbacks.
 */
add_action( 'woocommerce_after_shop_loop_item_title', function () { echo '<div class="loop-price-row">'; }, 9 );
add_action( 'woocommerce_after_shop_loop_item_title', function () { echo '</div>'; }, 12 );

function solaris_loop_quantity_input() {
	global $product;
	if ( ! solaris_get_option( 'loop_quantity_enabled' ) ) {
		return;
	}
	if ( ! $product instanceof WC_Product || ! $product->is_type( 'simple' ) || ! $product->is_purchasable() || ! $product->is_in_stock() ) {
		return;
	}

	$min_value = apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product );
	$max_value = apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product );

	echo '<div class="loop-qty">';
	// input_value deliberately omitted — the "Default Selected Qty" filter
	// (woocommerce_quantity_input_args, further down this file) sets it
	// from the product's own DSQ meta / available stock.
	//
	// woocommerce_quantity_input_type is a global filter (no $product/context
	// arg) — added/removed around just this one render call so only the
	// list/loop card switches to a plain text input (digits-only, enforced by
	// assets/js/main.js initLoopQuantity) instead of the number spinner;
	// single-product, cart, and Quick View keep core's default type="number".
	add_filter( 'woocommerce_quantity_input_type', 'solaris_loop_quantity_input_type' );
	woocommerce_quantity_input(
		array(
			'min_value' => $min_value,
			'max_value' => $max_value,
			// Set directly, not left to wc_get_quantity_input_args()'s own
			// wc_is_stock_amount_integer() default — this field is a plain
			// text input now (digits-only, enforced in main.js), so it needs
			// a numeric mobile keypad regardless of whether the store allows
			// decimal stock quantities.
			'inputmode' => 'numeric',
		)
	);
	remove_filter( 'woocommerce_quantity_input_type', 'solaris_loop_quantity_input_type' );
	echo '</div>';
}
add_action( 'woocommerce_after_shop_loop_item_title', 'solaris_loop_quantity_input', 11 );

function solaris_loop_quantity_input_type( $type ) {
	return 'hidden' === $type ? $type : 'text';
}

/**
 * +/- stepper buttons around every quantity input site-wide. Hooked onto
 * woocommerce_before/after_quantity_input_field — the two hooks core's own
 * global/quantity-input.php template fires immediately outside the <input>,
 * inside the shared "div.quantity" wrapper — so this reaches every classic
 * render of woocommerce_quantity_input() in one place (loop cards, Quick
 * View, single-product page) without touching each caller. Click handling
 * (increment/decrement, clamped to min/max/step) is in assets/js/main.js
 * (initQtySteppers); hidden here (CSS, .qty-step display:none via :has())
 * for the "sold individually" case where the input itself is type="hidden".
 * Not reached by the /cart/ page's own Cart block, which renders its own
 * React quantity selector — already has its own +/- controls, untouched.
 */
add_action( 'woocommerce_before_quantity_input_field', function () {
	if ( ! solaris_get_option( 'qty_stepper_enabled' ) ) {
		return;
	}
	echo '<button type="button" class="qty-step qty-step--minus" aria-label="' . esc_attr__( 'Decrease quantity', 'solaris' ) . '">&minus;</button>';
} );
add_action( 'woocommerce_after_quantity_input_field', function () {
	if ( ! solaris_get_option( 'qty_stepper_enabled' ) ) {
		return;
	}
	echo '<button type="button" class="qty-step qty-step--plus" aria-label="' . esc_attr__( 'Increase quantity', 'solaris' ) . '">+</button>';
} );

/**
 * Prepend a cart icon to every loop "Add to cart" link's visible text,
 * right after its opening <a ...> tag.
 */
add_filter( 'woocommerce_loop_add_to_cart_link', function ( $html ) {
	$tag_end = strpos( $html, '>' );
	if ( false === $tag_end ) {
		return $html;
	}
	return substr_replace( $html, '>' . solaris_icon( 'cart' ), $tag_end, 1 );
}, 10, 1 );

/**
 * Fade-in-on-scroll for every product card, wherever a product loop
 * renders — shop/category archives, homepage featured products, related/
 * upsell products, and the AJAX search modal (which has no is_shop()/
 * is_product_taxonomy() context of its own since it runs through
 * admin-ajax.php, not a real front-end query). Reuses the same .reveal
 * mechanism (assets/js/main.js IntersectionObserver) as the homepage
 * sections, added via post_class so no template override is needed. Each
 * card also gets a reveal-stagger-1..4 class cycling across the row
 * (matching the 4-column desktop grid) so cards animate in with increasing
 * delay/duration left-to-right instead of all snapping in at once — see
 * the .reveal-stagger-* rules in woocommerce.css.
 */
add_filter( 'post_class', function ( $classes, $class, $post_id ) {
	static $stagger_index = 0;

	// is_admin() is true for admin-ajax.php requests too — our own search
	// modal renders public-facing card HTML through exactly that endpoint,
	// so only bail on a real wp-admin screen, not every AJAX call.
	if ( ( is_admin() && ! wp_doing_ajax() ) || ! is_array( $classes ) ) {
		return $classes;
	}
	if ( 'product' === get_post_type( $post_id ) ) {
		$classes[] = 'reveal';
		$classes[] = 'reveal-stagger-' . ( ( $stagger_index % 4 ) + 1 );
		$stagger_index++;
	}
	return $classes;
}, 10, 3 );

function solaris_no_products_found() {
	echo '<div class="empty-state">';
	echo solaris_icon( 'search', array( 'class' => '' ) );
	echo '<h2>' . esc_html__( 'No solar products found', 'solaris' ) . '</h2>';
	echo '<p>' . esc_html__( 'Try another search or browse our popular categories.', 'solaris' ) . '</p>';
	echo '<div class="hero-actions"><a class="button" href="' . esc_url( solaris_shop_url() ) . '">' . esc_html__( 'Browse All Products', 'solaris' ) . '</a></div>';
	echo '</div>';
}
add_action( 'woocommerce_no_products_found', 'solaris_no_products_found' );

add_filter( 'wc_empty_cart_message', function () {
	return '<div class="empty-state">' . solaris_icon( 'cart' ) .
		'<h2>' . esc_html__( 'Your cart is empty', 'solaris' ) . '</h2>' .
		'<p>' . esc_html__( 'Looks like you haven\'t added any solar equipment yet.', 'solaris' ) . '</p>' .
		'<div class="hero-actions"><a class="button" href="' . esc_url( solaris_shop_url() ) . '">' . esc_html__( 'Shop Solar Products', 'solaris' ) . '</a></div></div>';
} );

/**
 * Move the description/reviews tabs into the summary column itself, right
 * after the "Category: ..." meta line — core WC renders them below the
 * whole gallery+summary row instead (woocommerce_after_single_product_summary,
 * priority 10). The summary column grid-stretches to match the (usually
 * taller) gallery column, so left where WC puts them, that extra height
 * was just dead empty space under the meta line; the tabs are real content
 * that fits there. woocommerce_single_product_summary's own hooks run
 * title(5)/rating+price(10)/excerpt(20)/add-to-cart(30)/meta(40)/sharing(50)
 * — 45 lands right after meta, before sharing.
 */
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_output_product_data_tabs', 45 );

/**
 * Keep the header cart-count badge live after an AJAX add-to-cart without a
 * full page reload, using WooCommerce's own fragments mechanism.
 */
add_filter( 'woocommerce_add_to_cart_fragments', function ( $fragments ) {
	ob_start();
	?>
	<span class="cart-count" data-cart-count><?php echo esc_html( WC()->cart->get_cart_contents_count() ); ?></span>
	<?php
	$fragments['[data-cart-count]'] = ob_get_clean();
	return $fragments;
} );

/**
 * WooCommerce's native "Featured" toggle sits as a small checkbox above the
 * Product Data tabs (next to the product type dropdown), not inside any
 * tab — easy to miss. This adds a second checkbox inside the General tab,
 * reading from and writing to that exact same underlying flag
 * (WC_Product::is_featured()/set_featured()), so it's not a separate/new
 * piece of data — just a more visible place to toggle the one that already
 * exists. WooCommerce's own product-save handler already verifies the
 * nonce/capability before firing woocommerce_process_product_meta, so
 * there's nothing extra to check here.
 */
add_action( 'woocommerce_product_options_general_product_data', function () {
	global $post;
	$product = wc_get_product( $post->ID );
	woocommerce_wp_checkbox(
		array(
			'id'          => '_solaris_is_featured_general',
			'label'       => __( 'Is Featured?', 'solaris' ),
			'description' => __( 'Shows this product in the homepage Featured Products section. Same setting as the "Featured" checkbox above the Product data tabs.', 'solaris' ),
			'value'       => $product && $product->is_featured() ? 'yes' : 'no',
		)
	);
} );

/*
 * Priority 20 (WooCommerce's own WC_Meta_Box_Product_Data::save() runs at
 * the default 10): this must run strictly AFTER it, not just "later by
 * registration order" — that save() call sets its own 'featured' prop from
 * whether the *native* "_featured" checkbox key is present in $_POST at
 * all, so if it ran after us it would silently stomp our value right back
 * to whatever the native (hidden, above-the-tabs) checkbox happened to be.
 */
add_action( 'woocommerce_process_product_meta', function ( $post_id ) {
	$product = wc_get_product( $post_id );
	if ( ! $product ) {
		return;
	}
	$is_featured = isset( $_POST['_solaris_is_featured_general'] ) && 'yes' === $_POST['_solaris_is_featured_general']; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	$product->set_featured( $is_featured );
	$product->save();
}, 20 );

/**
 * "Default Selected Qty" — Product Data → Inventory. Needs to
 * stay visible/editable regardless of whether "Track stock quantity for
 * this product" is checked — that's exactly the state where DSQ matters
 * most (it's the ONLY thing setting the default then, no stock number to
 * fall back to) — so it can't live inside core's own
 * woocommerce_product_options_stock_fields, which sits inside the ".stock_
 * fields" box wc-admin's own JS hides whenever that checkbox is unchecked
 * (and never renders at all if stock management is off store-wide). Hooked
 * to woocommerce_product_options_stock_status instead — the one inventory
 * hook core fires unconditionally, after both branches of that checkbox's
 * if/else. Purely a frontend default: which quantity the loop cards/Quick
 * View/single-product page pre-fill, not a real stock control. See the
 * woocommerce_quantity_input_args filter below for how it's actually used.
 */
add_action( 'woocommerce_product_options_stock_status', function () {
	global $post;
	woocommerce_wp_text_input(
		array(
			'id'                => '_solaris_dsq',
			'label'             => __( 'Default Selected Qty', 'solaris' ),
			'desc_tip'          => true,
			'description'       => __( 'Quantity the frontend (product list, Quick View, product page) pre-fills the quantity field with. If stock is tracked and the available stock is lower than this, the stock quantity is used instead.', 'solaris' ),
			'type'              => 'number',
			'custom_attributes' => array( 'step' => '1', 'min' => '1' ),
			'value'             => get_post_meta( $post->ID, '_solaris_dsq', true ) ?: 1,
		)
	);
} );
add_action( 'woocommerce_process_product_meta', function ( $post_id ) {
	$dsq = isset( $_POST['_solaris_dsq'] ) ? absint( wp_unslash( $_POST['_solaris_dsq'] ) ) : 1; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	update_post_meta( $post_id, '_solaris_dsq', max( 1, $dsq ) );
} );

/**
 * Applies DSQ wherever a quantity input renders. Loop cards
 * (solaris_loop_quantity_input above), Quick View, and the single-product
 * page all render their quantity field through core's own
 * woocommerce_quantity_input() → wc_get_quantity_input_args(), which fires
 * this exact filter as its last step — one hook covers all three call
 * sites, no need to touch each one individually.
 */
add_filter( 'woocommerce_quantity_input_args', function ( $args, $product ) {
	if ( ! $product instanceof WC_Product ) {
		return $args;
	}
	$dsq = absint( $product->get_meta( '_solaris_dsq', true ) );
	if ( ! $dsq ) {
		$dsq = 1;
	}
	if ( $product->managing_stock() ) {
		$stock = $product->get_stock_quantity();
		if ( null !== $stock && $stock < $dsq ) {
			$dsq = max( 0, $stock );
		}
	}
	$args['input_value'] = $dsq;
	return $args;
}, 10, 2 );

/* ---------------------------------------------------------- Misc styling */

/**
 * The "View cart" link (.added_to_cart) that appears next to a loop card's
 * Add to cart button after a successful AJAX add is created entirely
 * client-side by WooCommerce's own add-to-cart.js (AddToCartHandler.
 * updateButton()) — there's no server-rendered markup or PHP hook to gate
 * it on. A body class + CSS display:none (woocommerce.css) is the only
 * clean way to toggle it without touching/duplicating that core script.
 */
add_filter( 'body_class', function ( $classes ) {
	if ( ! solaris_get_option( 'show_view_cart_link' ) ) {
		$classes[] = 'hide-view-cart-link';
	}
	return $classes;
} );

add_filter( 'woocommerce_sale_flash', function ( $html, $post, $product ) {
	return '<span class="onsale">' . esc_html__( 'Sale', 'solaris' ) . '</span>';
}, 10, 3 );

add_filter( 'woocommerce_breadcrumb_defaults', function ( $defaults ) {
	$defaults['delimiter'] = '<span class="woocommerce-breadcrumb__sep" aria-hidden="true">' . solaris_icon( 'chevron-down', array( 'class' => 'woocommerce-breadcrumb__sep-icon' ) ) . '</span>';
	$defaults['home']      = __( 'Home', 'solaris' );
	$defaults['before']    = '<span class="woocommerce-breadcrumb__crumb">';
	$defaults['after']     = '</span>';
	return $defaults;
} );

/**
 * WC_Breadcrumb::prepend_shop_page() only inserts a "Shop" crumb when the
 * product permalink structure literally nests products under the shop
 * page's own slug (e.g. product base "/shop/product/%product%") — see
 * woocommerce/includes/class-wc-breadcrumb.php. This store's product URLs
 * are plain "/product/%product%" with no "/shop/" segment, so that check
 * always fails and "Shop" never appears on product/category/tag
 * breadcrumbs. Filtering the final crumb array (rather than that private
 * method) inserts it unconditionally right after Home.
 */
add_filter( 'woocommerce_get_breadcrumb', function ( $crumbs ) {
	if ( ! is_product() && ! is_product_category() && ! is_product_tag() ) {
		return $crumbs;
	}

	$shop_page_id = wc_get_page_id( 'shop' );
	$shop_link    = $shop_page_id > 0 ? get_permalink( $shop_page_id ) : get_post_type_archive_link( 'product' );
	$shop_title   = $shop_page_id > 0 ? get_the_title( $shop_page_id ) : __( 'Shop', 'solaris' );

	foreach ( $crumbs as $crumb ) {
		if ( isset( $crumb[1] ) && $crumb[1] === $shop_link ) {
			return $crumbs; // Already present.
		}
	}

	array_splice( $crumbs, 1, 0, array( array( $shop_title, $shop_link ) ) );
	return $crumbs;
} );

/**
 * Homepage "Featured Products" query, driven by the Customizer source picker.
 * Always uses wc_get_products()/WC_Product_Query so results respect stock,
 * visibility and catalog rules exactly like the rest of the store.
 */
function solaris_get_homepage_products() {
	$source = solaris_get_option( 'featured_products_source' );
	$count  = max( 1, absint( solaris_get_option( 'featured_products_count' ) ) );

	$args = array(
		'status'  => 'publish',
		'limit'   => $count,
		'orderby' => 'date',
		'order'   => 'DESC',
	);

	if ( 'featured' === $source ) {
		$args['featured'] = true;
	} elseif ( 'best-selling' === $source ) {
		$args['orderby']  = 'meta_value_num';
		$args['meta_key']  = 'total_sales'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
		$args['order']     = 'DESC';
	}

	$products = wc_get_products( $args );

	if ( 'featured' === $source && empty( $products ) ) {
		unset( $args['featured'] );
		$args['orderby'] = 'date';
		$products        = wc_get_products( $args );
	}

	return $products;
}

/* -------------------------------------------------------------- Admin UX */

/**
 * Prev/Next icon buttons on the product edit screen, next to "Add new
 * product" — jump straight to editing the adjacent product without going
 * back through the products list. "Adjacent" here means by publish date
 * (via WordPress core's own get_previous_post()/get_next_post() — the same
 * mechanism front-end single-post navigation uses, just reused here since
 * it already correctly restricts to same-post-type), which also matches
 * the products list's own default sort order.
 */
add_action( 'admin_enqueue_scripts', function ( $hook ) {
	if ( 'post.php' !== $hook ) {
		return;
	}
	global $post;
	if ( ! $post || 'product' !== $post->post_type ) {
		return;
	}

	$prev = get_previous_post();
	$next = get_next_post();
	if ( ! $prev && ! $next ) {
		return;
	}

	wp_enqueue_script(
		'solaris-admin-product-nav',
		SOLARIS_URI . '/assets/js/admin-product-nav.js',
		array(),
		solaris_asset_version( '/assets/js/admin-product-nav.js' ),
		true
	);
	wp_localize_script(
		'solaris-admin-product-nav',
		'solarisProductNav',
		array(
			'prevUrl'   => $prev ? get_edit_post_link( $prev->ID, 'raw' ) : '',
			'prevTitle' => $prev ? $prev->post_title : '',
			'nextUrl'   => $next ? get_edit_post_link( $next->ID, 'raw' ) : '',
			'nextTitle' => $next ? $next->post_title : '',
		)
	);
	wp_enqueue_style(
		'solaris-admin-product-nav',
		SOLARIS_URI . '/assets/css/admin-product-nav.css',
		array(),
		solaris_asset_version( '/assets/css/admin-product-nav.css' )
	);
} );
