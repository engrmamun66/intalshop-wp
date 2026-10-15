<?php
/**
 * Core checkout field visibility (Company, Address Line 2, Phone).
 *
 * These three fields are controlled by WooCommerce itself via three plain
 * options — @see Automattic\WooCommerce\Blocks\Utils\CartCheckoutUtils — that
 * both the Checkout block and the legacy shortcode checkout already read.
 * There is no custom filter logic here on purpose: writing directly to the
 * options WooCommerce already reads is the correct, forward-compatible way
 * to do this (no fragile duplicate field-schema logic to keep in sync).
 *
 * @package ManageWooCommerceCheckout
 */

defined( 'ABSPATH' ) || exit;

class MWC_Core_Fields {

	const OPTION_MAP = array(
		'company'   => 'woocommerce_checkout_company_field',
		'address_2' => 'woocommerce_checkout_address_2_field',
		'phone'     => 'woocommerce_checkout_phone_field',
	);

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public static function get_visibility( $field_key ) {
		if ( ! isset( self::OPTION_MAP[ $field_key ] ) ) {
			return 'optional';
		}
		$value = get_option( self::OPTION_MAP[ $field_key ] );
		return in_array( $value, array( 'hidden', 'optional', 'required' ), true ) ? $value : 'optional';
	}

	public static function save_visibility( $field_key, $value ) {
		if ( ! isset( self::OPTION_MAP[ $field_key ] ) ) {
			return;
		}
		if ( ! in_array( $value, array( 'hidden', 'optional', 'required' ), true ) ) {
			return;
		}
		update_option( self::OPTION_MAP[ $field_key ], $value );
	}
}
