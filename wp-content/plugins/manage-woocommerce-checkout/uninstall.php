<?php
/**
 * Runs on plugin deletion (not deactivation) via wp-admin.
 *
 * Only removes what THIS plugin created. The three core-field visibility
 * options are WooCommerce's own (woocommerce_checkout_company_field etc.) —
 * deleting this plugin should not silently reset checkout behavior
 * WooCommerce itself owns.
 *
 * @package ManageWooCommerceCheckout
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

delete_option( 'mwc_custom_fields' );
delete_option( 'mwc_admin_order_fields' );
