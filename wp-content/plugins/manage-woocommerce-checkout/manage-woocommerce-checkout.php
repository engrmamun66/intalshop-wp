<?php
/**
 * Main plugin file.
 *
 * @package ManageWooCommerceCheckout
 */

/**
 * Plugin Name:       Manage WooCommerce Checkout
 * Description:        Visually manage the WooCommerce Checkout block's fields — toggle Company/Address Line 2/Phone visibility & requirement, add custom fields to the Contact, Address, or Order sections, and relabel/hide/reorder fields on the admin order-edit screen. Built against the WooCommerce Blocks checkout's actual, current hooks — not the legacy woocommerce_checkout_fields filter, which the Checkout block ignores.
 * Version:           1.1.0
 * Requires PHP:      7.4
 * Requires Plugins:  woocommerce
 * Author:             Intalshop
 * License:            GPL v2 or later
 * License URI:        https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:        manage-woocommerce-checkout
 */

defined( 'ABSPATH' ) || exit;

define( 'MWC_VERSION', '1.1.0' );
define( 'MWC_DIR', plugin_dir_path( __FILE__ ) );
define( 'MWC_URL', plugin_dir_url( __FILE__ ) );

/**
 * Everything here depends on WooCommerce (specifically its Blocks package
 * for the additional-fields API) — never load on a site without it, and
 * tell the admin why nothing is happening rather than fatal-erroring.
 */
function mwc_is_woocommerce_ready() {
	return class_exists( 'WooCommerce' ) && function_exists( 'woocommerce_register_additional_checkout_field' );
}

function mwc_missing_woocommerce_notice() {
	if ( mwc_is_woocommerce_ready() ) {
		return;
	}
	echo '<div class="notice notice-error"><p>' .
		esc_html__( 'Manage WooCommerce Checkout requires WooCommerce (with the Checkout block additional-fields API) to be active.', 'manage-woocommerce-checkout' ) .
		'</p></div>';
}
add_action( 'admin_notices', 'mwc_missing_woocommerce_notice' );

function mwc_init() {
	if ( ! mwc_is_woocommerce_ready() ) {
		return;
	}
	require_once MWC_DIR . 'includes/class-mwc-core-fields.php';
	require_once MWC_DIR . 'includes/class-mwc-custom-fields.php';
	require_once MWC_DIR . 'includes/class-mwc-admin-order-fields.php';
	require_once MWC_DIR . 'includes/class-mwc-admin-page.php';

	MWC_Core_Fields::instance();
	MWC_Custom_Fields::instance();
	MWC_Admin_Order_Fields::instance();
	MWC_Admin_Page::instance();

	add_action( 'wp_enqueue_scripts', 'mwc_enqueue_checkout_assets' );
}
// after_setup_theme: late enough that WooCommerce/Blocks are loaded, early
// enough to be safely before woocommerce_init (required registration timing
// for woocommerce_register_additional_checkout_field(), and also required
// for the woocommerce_get_country_locale filter to be in place before core
// reads it while building the checkout fields schema).
add_action( 'after_setup_theme', 'mwc_init', 20 );

/**
 * Frontend workaround for Checkout block fields that WooCommerce does not
 * expose through its PHP checkout-field filters.
 */
function mwc_enqueue_checkout_assets() {
	if ( ! function_exists( 'is_checkout' ) || ! is_checkout() ) {
		return;
	}

	$script_path = MWC_DIR . 'assets/checkout-fields.js';
	$script_ver  = file_exists( $script_path ) ? (string) filemtime( $script_path ) : MWC_VERSION;

	wp_enqueue_script(
		'mwc-checkout-fields',
		MWC_URL . 'assets/checkout-fields.js',
		array( 'wp-data' ),
		$script_ver,
		true
	);

	wp_localize_script(
		'mwc-checkout-fields',
		'mwcCheckoutFields',
		array(
			'overrides' => MWC_Admin_Order_Fields::get_frontend_overrides(),
		)
	);

	wp_register_style( 'mwc-checkout-fields', false, array(), MWC_VERSION );
	wp_enqueue_style( 'mwc-checkout-fields' );
	wp_add_inline_style(
		'mwc-checkout-fields',
		'.mwc-checkout-field-hidden{display:none!important;}' .
		// Belt-and-suspenders alongside collapseAddressSummaries() in
		// checkout-fields.js, which auto-clicks this button the instant it
		// appears — this just removes any brief flash of it before that runs.
		'.wc-block-components-address-card__edit{display:none!important;}'
	);
}

/** "Settings" link on the Plugins list row, next to Deactivate. */
function mwc_plugin_action_links( $links ) {
	if ( ! mwc_is_woocommerce_ready() ) {
		return $links;
	}
	$settings_link = '<a href="' . esc_url( admin_url( 'admin.php?page=mwc-checkout' ) ) . '">' . esc_html__( 'Settings', 'manage-woocommerce-checkout' ) . '</a>';
	array_unshift( $links, $settings_link );
	return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'mwc_plugin_action_links' );
