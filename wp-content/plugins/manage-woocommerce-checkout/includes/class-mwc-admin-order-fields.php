<?php
/**
 * Per-field label/visibility/position overrides for Billing/Shipping fields on
 * the classic shortcode checkout and the wp-admin order-edit screen.
 *
 * The Checkout block exposes only limited controls for built-in fields. See
 * MWC_Core_Fields for the block-compatible Company/Address 2/Phone settings.
 *
 * @package ManageWooCommerceCheckout
 */

defined( 'ABSPATH' ) || exit;

class MWC_Admin_Order_Fields {

	const OPTION_KEY = 'mwc_admin_order_fields';

	const FIELD_KEYS = array(
		'billing'  => array( 'first_name', 'last_name', 'company', 'address_1', 'address_2', 'city', 'postcode', 'country', 'state', 'email', 'phone' ),
		'shipping' => array( 'first_name', 'last_name', 'company', 'address_1', 'address_2', 'city', 'postcode', 'country', 'state', 'phone' ),
	);

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_filter( 'woocommerce_checkout_fields', array( $this, 'filter_checkout_fields' ), 20 );
		add_filter( 'woocommerce_get_country_locale_default', array( $this, 'filter_block_address_locale' ), 20 );
		add_filter( 'woocommerce_get_country_locale_base', array( $this, 'filter_block_address_locale' ), 20 );
		add_filter( 'woocommerce_admin_billing_fields', array( $this, 'filter_billing_fields' ) );
		add_filter( 'woocommerce_admin_shipping_fields', array( $this, 'filter_shipping_fields' ) );
	}

	public static function get_overrides() {
		$overrides = get_option( self::OPTION_KEY, array() );
		return is_array( $overrides ) ? $overrides : array();
	}

	public static function get_frontend_overrides() {
		$saved    = self::get_overrides();
		$frontend = array();

		foreach ( self::FIELD_KEYS as $type => $keys ) {
			foreach ( $keys as $key ) {
				$frontend[ $type ][ $key ] = array(
					'hidden' => ! empty( $saved[ $type ][ $key ]['hidden'] ),
				);
			}
		}

		return $frontend;
	}

	public static function save_overrides( $overrides ) {
		update_option( self::OPTION_KEY, $overrides );
	}

	public function filter_billing_fields( $fields ) {
		return $this->apply_overrides( 'billing', $fields );
	}

	public function filter_shipping_fields( $fields ) {
		return $this->apply_overrides( 'shipping', $fields );
	}

	public function filter_checkout_fields( $fields ) {
		foreach ( array( 'billing', 'shipping' ) as $type ) {
			if ( empty( $fields[ $type ] ) || ! is_array( $fields[ $type ] ) ) {
				continue;
			}

			$fields[ $type ] = $this->apply_checkout_overrides( $type, $fields[ $type ] );
		}

		return $fields;
	}

	private function apply_checkout_overrides( $type, $fields ) {
		$overrides = self::get_overrides()[ $type ] ?? array();

		foreach ( self::FIELD_KEYS[ $type ] as $key ) {
			$checkout_key = $type . '_' . $key;

			if ( ! isset( $fields[ $checkout_key ] ) ) {
				continue;
			}

			$override = $overrides[ $key ] ?? array();

			if ( ! empty( $override['hidden'] ) ) {
				unset( $fields[ $checkout_key ] );
				continue;
			}

			if ( isset( $override['label'] ) && '' !== trim( $override['label'] ) ) {
				$fields[ $checkout_key ]['label'] = sanitize_text_field( $override['label'] );
			}

			if ( isset( $override['priority'] ) && '' !== $override['priority'] ) {
				$fields[ $checkout_key ]['priority'] = (int) $override['priority'];
			}
		}

		return $fields;
	}

	public function filter_block_address_locale( $locale ) {
		$overrides = self::get_overrides();

		foreach ( $this->get_shared_block_address_keys() as $key ) {
			if ( empty( $locale[ $key ] ) || ! is_array( $locale[ $key ] ) ) {
				continue;
			}

			$billing_override  = $overrides['billing'][ $key ] ?? array();
			$shipping_override = $overrides['shipping'][ $key ] ?? array();

			if ( ! empty( $billing_override['hidden'] ) && ! empty( $shipping_override['hidden'] ) ) {
				$locale[ $key ]['hidden']   = true;
				$locale[ $key ]['required'] = false;
				continue;
			}

			$label = $shipping_override['label'] ?? $billing_override['label'] ?? '';
			if ( '' !== trim( (string) $label ) ) {
				$locale[ $key ]['label'] = sanitize_text_field( $label );
			}

			$priority = $shipping_override['priority'] ?? $billing_override['priority'] ?? '';
			if ( '' !== $priority ) {
				$locale[ $key ]['priority'] = (int) $priority;
			}
		}

		return $locale;
	}

	private function get_shared_block_address_keys() {
		return array( 'first_name', 'last_name', 'company', 'address_1', 'address_2', 'city', 'postcode', 'state', 'phone' );
	}

	private function apply_overrides( $type, $fields ) {
		$overrides  = self::get_overrides()[ $type ] ?? array();
		$priorities = array();
		$index      = 0;

		foreach ( array_keys( $fields ) as $key ) {
			$index     += 10;
			$override   = $overrides[ $key ] ?? array();

			if ( ! empty( $override['hidden'] ) ) {
				unset( $fields[ $key ] );
				continue;
			}

			if ( isset( $override['label'] ) && '' !== trim( $override['label'] ) ) {
				$fields[ $key ]['label'] = sanitize_text_field( $override['label'] );
			}

			$priorities[ $key ] = ( isset( $override['priority'] ) && '' !== $override['priority'] )
				? (int) $override['priority']
				: $index;
		}

		uksort(
			$fields,
			function ( $a, $b ) use ( $priorities ) {
				return ( $priorities[ $a ] ?? 0 ) <=> ( $priorities[ $b ] ?? 0 );
			}
		);

		return $fields;
	}
}
