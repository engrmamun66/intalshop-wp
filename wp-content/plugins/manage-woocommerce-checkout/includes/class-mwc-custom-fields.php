<?php
/**
 * Admin-defined custom checkout fields, registered with WooCommerce's
 * official Additional Checkout Fields API so they render correctly in the
 * Checkout block (Contact/Address/Order sections) and persist to order meta
 * the same way any other extension's checkout field would.
 *
 * @package ManageWooCommerceCheckout
 */

defined( 'ABSPATH' ) || exit;

class MWC_Custom_Fields {

	const OPTION_KEY = 'mwc_custom_fields';
	const ID_PREFIX  = 'mwc/';

	private static $instance = null;

	/** @var array Registration failures, for the admin page to surface. Not persisted — a request-lifetime notice only. */
	public $registration_errors = array();

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'woocommerce_init', array( $this, 'register_fields' ) );
	}

	public static function get_fields() {
		$fields = get_option( self::OPTION_KEY, array() );
		return is_array( $fields ) ? $fields : array();
	}

	public static function save_fields( $fields ) {
		update_option( self::OPTION_KEY, $fields );
	}

	/**
	 * "Subscribe to our newsletter" -> "subscribe-to-our-newsletter". Falls
	 * back to a short random slug if the label sanitizes away to nothing
	 * (e.g. a label that's only punctuation/emoji).
	 */
	public static function slugify( $label ) {
		$slug = sanitize_title( $label );
		return $slug ? $slug : 'field-' . substr( md5( $label . microtime() ), 0, 8 );
	}

	/**
	 * Registers every stored custom field with WooCommerce. Each field is
	 * wrapped in its own try/catch — one misconfigured field (e.g. a
	 * duplicate id left over from a renamed slug) must never take down the
	 * whole checkout for every customer.
	 */
	public function register_fields() {
		$this->registration_errors = array();

		foreach ( self::get_fields() as $field ) {
			try {
				$this->register_one_field( $field );
			} catch ( \Throwable $e ) {
				$this->registration_errors[] = sprintf(
					/* translators: 1: field label, 2: error message. */
					__( '"%1$s": %2$s', 'manage-woocommerce-checkout' ),
					$field['label'] ?? $field['slug'] ?? '?',
					$e->getMessage()
				);
			}
		}
	}

	private function register_one_field( $field ) {
		$slug = $field['slug'] ?? '';
		if ( ! $slug ) {
			return;
		}

		$type = in_array( $field['type'] ?? 'text', array( 'text', 'select', 'checkbox' ), true ) ? $field['type'] : 'text';

		$args = array(
			'id'                => self::ID_PREFIX . $slug,
			'label'             => sanitize_text_field( $field['label'] ?? $slug ),
			'location'          => in_array( $field['location'] ?? 'contact', array( 'contact', 'address', 'order' ), true ) ? $field['location'] : 'contact',
			'type'              => $type,
			'required'          => ! empty( $field['required'] ),
			'sanitize_callback' => array( $this, 'sanitize_field_value' ),
		);

		if ( ! empty( $field['placeholder'] ) ) {
			$args['placeholder'] = sanitize_text_field( $field['placeholder'] );
		}

		if ( 'select' === $type ) {
			$args['options'] = $this->parse_select_options( $field['options_raw'] ?? '' );
			if ( empty( $args['options'] ) ) {
				// A select field with no options can't be rendered — skip
				// registering it rather than let WooCommerce reject it.
				throw new \Exception( __( 'select field has no options — add at least one "value|Label" line.', 'manage-woocommerce-checkout' ) );
			}
		}

		woocommerce_register_additional_checkout_field( $args );
	}

	/**
	 * "yes|Yes\nno|No" -> [['value'=>'yes','label'=>'Yes'], ['value'=>'no','label'=>'No']].
	 * A line with no "|" uses the same text for both value and label.
	 */
	private function parse_select_options( $raw ) {
		$options = array();
		foreach ( preg_split( '/[\r\n]+/', (string) $raw ) as $line ) {
			$line = trim( $line );
			if ( '' === $line ) {
				continue;
			}
			if ( false !== strpos( $line, '|' ) ) {
				list( $value, $label ) = array_map( 'trim', explode( '|', $line, 2 ) );
			} else {
				$value = $label = $line;
			}
			if ( '' === $value ) {
				continue;
			}
			$options[] = array( 'value' => sanitize_text_field( $value ), 'label' => sanitize_text_field( $label ) );
		}
		return $options;
	}

	/**
	 * Default sanitizer for every custom field's submitted value — WC's own
	 * default_sanitize_callback returns raw input untouched, which isn't
	 * safe to trust into order meta / admin screens / emails unescaped.
	 */
	public function sanitize_field_value( $value, $field ) {
		if ( 'checkbox' === ( $field['type'] ?? 'text' ) ) {
			return (bool) $value;
		}
		return sanitize_text_field( (string) $value );
	}
}
