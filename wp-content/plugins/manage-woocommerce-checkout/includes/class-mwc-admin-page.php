<?php
/**
 * wp-admin settings screen: WooCommerce > Manage Checkout.
 *
 * @package ManageWooCommerceCheckout
 */

defined( 'ABSPATH' ) || exit;

class MWC_Admin_Page {

	const PAGE_SLUG = 'mwc-checkout';

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action( 'admin_init', array( $this, 'maybe_handle_save' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	public function add_menu() {
		add_submenu_page(
			'woocommerce',
			__( 'Manage Checkout', 'manage-woocommerce-checkout' ),
			__( 'Manage Checkout', 'manage-woocommerce-checkout' ),
			'manage_woocommerce',
			self::PAGE_SLUG,
			array( $this, 'render_page' )
		);
	}

	public function enqueue_assets( $hook ) {
		if ( 'woocommerce_page_' . self::PAGE_SLUG !== $hook ) {
			return;
		}
		wp_enqueue_style( 'mwc-admin', MWC_URL . 'assets/admin.css', array(), MWC_VERSION );
		wp_enqueue_script( 'mwc-admin', MWC_URL . 'assets/admin.js', array(), MWC_VERSION, true );
	}

	private function current_tab() {
		$tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'core';
		return in_array( $tab, array( 'core', 'custom', 'order_screen' ), true ) ? $tab : 'core';
	}

	/** Default labels, matching WooCommerce's own — for display only. */
	private function default_admin_field_labels() {
		return array(
			'first_name' => __( 'First name', 'manage-woocommerce-checkout' ),
			'last_name'  => __( 'Last name', 'manage-woocommerce-checkout' ),
			'company'    => __( 'Company', 'manage-woocommerce-checkout' ),
			'address_1'  => __( 'Address line 1', 'manage-woocommerce-checkout' ),
			'address_2'  => __( 'Address line 2', 'manage-woocommerce-checkout' ),
			'city'       => __( 'City', 'manage-woocommerce-checkout' ),
			'postcode'   => __( 'Postcode / ZIP', 'manage-woocommerce-checkout' ),
			'country'    => __( 'Country / Region', 'manage-woocommerce-checkout' ),
			'state'      => __( 'State / County', 'manage-woocommerce-checkout' ),
			'email'      => __( 'Email address', 'manage-woocommerce-checkout' ),
			'phone'      => __( 'Phone', 'manage-woocommerce-checkout' ),
		);
	}

	/* ------------------------------------------------------------ Saving */

	public function maybe_handle_save() {
		if ( ! isset( $_POST['mwc_action'] ) || ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		if ( 'save_core_fields' === $_POST['mwc_action'] ) {
			$this->save_core_fields();
		} elseif ( 'save_custom_fields' === $_POST['mwc_action'] ) {
			$this->save_custom_fields();
		} elseif ( 'save_order_screen_fields' === $_POST['mwc_action'] ) {
			$this->save_order_screen_fields();
		} else {
			return;
		}
	}

	private function save_core_fields() {
		check_admin_referer( 'mwc_save_core_fields' );

		foreach ( array_keys( MWC_Core_Fields::OPTION_MAP ) as $field_key ) {
			$value = isset( $_POST[ $field_key ] ) ? sanitize_key( wp_unslash( $_POST[ $field_key ] ) ) : 'optional';
			MWC_Core_Fields::save_visibility( $field_key, $value );
		}

		$this->redirect_with_notice( 'core' );
	}

	private function save_custom_fields() {
		check_admin_referer( 'mwc_save_custom_fields' );

		$labels       = wp_unslash( $_POST['label'] ?? array() );
		$slugs        = wp_unslash( $_POST['slug'] ?? array() );
		$locations    = wp_unslash( $_POST['location'] ?? array() );
		$types        = wp_unslash( $_POST['type'] ?? array() );
		$required     = wp_unslash( $_POST['required'] ?? array() );
		$placeholders = wp_unslash( $_POST['placeholder'] ?? array() );
		$options_raw  = wp_unslash( $_POST['options_raw'] ?? array() );
		$deleted      = wp_unslash( $_POST['deleted'] ?? array() );

		$existing_slugs = array();
		$fields         = array();

		foreach ( $labels as $i => $label ) {
			$label = sanitize_text_field( $label );
			if ( '' === trim( $label ) || ! empty( $deleted[ $i ] ) ) {
				continue; // Blank label (an empty trailing repeater row) or explicitly removed.
			}

			$slug = sanitize_title( $slugs[ $i ] ?? '' );
			if ( '' === $slug ) {
				$slug = MWC_Custom_Fields::slugify( $label );
			}
			// Guard against two rows landing on the same slug (e.g. two
			// fields both labelled "Notes") — a duplicate id would make
			// woocommerce_register_additional_checkout_field() reject the
			// second one, so disambiguate here instead of losing it silently.
			$unique_slug = $slug;
			$suffix      = 2;
			while ( in_array( $unique_slug, $existing_slugs, true ) ) {
				$unique_slug = $slug . '-' . $suffix;
				++$suffix;
			}
			$existing_slugs[] = $unique_slug;

			$fields[] = array(
				'slug'        => $unique_slug,
				'label'       => $label,
				'location'    => in_array( $locations[ $i ] ?? '', array( 'contact', 'address', 'order' ), true ) ? $locations[ $i ] : 'contact',
				'type'        => in_array( $types[ $i ] ?? '', array( 'text', 'select', 'checkbox' ), true ) ? $types[ $i ] : 'text',
				'required'    => ! empty( $required[ $i ] ),
				'placeholder' => sanitize_text_field( $placeholders[ $i ] ?? '' ),
				'options_raw' => sanitize_textarea_field( $options_raw[ $i ] ?? '' ),
			);
		}

		MWC_Custom_Fields::save_fields( $fields );
		$this->redirect_with_notice( 'custom' );
	}

	private function save_order_screen_fields() {
		check_admin_referer( 'mwc_save_order_screen_fields' );

		$posted    = wp_unslash( $_POST['order_field'] ?? array() );
		$overrides = array();

		foreach ( MWC_Admin_Order_Fields::FIELD_KEYS as $type => $keys ) {
			foreach ( $keys as $key ) {
				$row = $posted[ $type ][ $key ] ?? array();
				$overrides[ $type ][ $key ] = array(
					'label'    => sanitize_text_field( $row['label'] ?? '' ),
					'hidden'   => ! empty( $row['hidden'] ),
					'priority' => ( isset( $row['priority'] ) && '' !== $row['priority'] ) ? (int) $row['priority'] : '',
				);
			}
		}

		MWC_Admin_Order_Fields::save_overrides( $overrides );
		$this->redirect_with_notice( 'order_screen' );
	}

	private function redirect_with_notice( $tab ) {
		wp_safe_redirect( add_query_arg( array( 'page' => self::PAGE_SLUG, 'tab' => $tab, 'mwc-saved' => '1' ), admin_url( 'admin.php' ) ) );
		exit;
	}

	/* ----------------------------------------------------------- Render */

	public function render_page() {
		$tab = $this->current_tab();
		?>
		<div class="wrap mwc-wrap">
			<h1><?php esc_html_e( 'Manage Checkout', 'manage-woocommerce-checkout' ); ?></h1>
			<p class="description">
				<?php esc_html_e( 'Controls the WooCommerce Checkout block\'s Contact and Address fields. To reorder or remove whole sections (Express Payment, Order Notes, etc.), edit the Checkout page directly in the block editor — that\'s a native WordPress capability this plugin intentionally leaves alone.', 'manage-woocommerce-checkout' ); ?>
			</p>

			<?php if ( isset( $_GET['mwc-saved'] ) ) : ?>
				<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Saved.', 'manage-woocommerce-checkout' ); ?></p></div>
			<?php endif; ?>

			<?php
			$custom_fields = MWC_Custom_Fields::instance();
			if ( ! empty( $custom_fields->registration_errors ) ) :
				?>
				<div class="notice notice-error">
					<p><strong><?php esc_html_e( 'Some custom fields could not be registered:', 'manage-woocommerce-checkout' ); ?></strong></p>
					<ul>
						<?php foreach ( $custom_fields->registration_errors as $error ) : ?>
							<li><?php echo esc_html( $error ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<h2 class="nav-tab-wrapper">
				<a href="<?php echo esc_url( add_query_arg( array( 'page' => self::PAGE_SLUG, 'tab' => 'core' ), admin_url( 'admin.php' ) ) ); ?>" class="nav-tab <?php echo 'core' === $tab ? 'nav-tab-active' : ''; ?>">
					<?php esc_html_e( 'Core Fields', 'manage-woocommerce-checkout' ); ?>
				</a>
				<a href="<?php echo esc_url( add_query_arg( array( 'page' => self::PAGE_SLUG, 'tab' => 'custom' ), admin_url( 'admin.php' ) ) ); ?>" class="nav-tab <?php echo 'custom' === $tab ? 'nav-tab-active' : ''; ?>">
					<?php esc_html_e( 'Custom Fields', 'manage-woocommerce-checkout' ); ?>
				</a>
				<a href="<?php echo esc_url( add_query_arg( array( 'page' => self::PAGE_SLUG, 'tab' => 'order_screen' ), admin_url( 'admin.php' ) ) ); ?>" class="nav-tab <?php echo 'order_screen' === $tab ? 'nav-tab-active' : ''; ?>">
					<?php esc_html_e( 'Checkout & Order Fields', 'manage-woocommerce-checkout' ); ?>
				</a>
			</h2>

			<?php if ( 'core' === $tab ) : ?>
				<?php $this->render_core_tab(); ?>
			<?php elseif ( 'custom' === $tab ) : ?>
				<?php $this->render_custom_tab(); ?>
			<?php else : ?>
				<?php $this->render_order_screen_tab(); ?>
			<?php endif; ?>
		</div>
		<?php
	}

	private function render_core_tab() {
		$labels = array(
			'company'   => __( 'Company Name', 'manage-woocommerce-checkout' ),
			'address_2' => __( 'Address Line 2 (Apartment, suite, etc.)', 'manage-woocommerce-checkout' ),
			'phone'     => __( 'Phone', 'manage-woocommerce-checkout' ),
		);
		?>
		<form method="post" class="mwc-form">
			<?php wp_nonce_field( 'mwc_save_core_fields' ); ?>
			<input type="hidden" name="mwc_action" value="save_core_fields" />
			<table class="form-table" role="presentation">
				<tbody>
					<?php foreach ( $labels as $field_key => $label ) : ?>
						<tr>
							<th scope="row"><?php echo esc_html( $label ); ?></th>
							<td>
								<select name="<?php echo esc_attr( $field_key ); ?>">
									<?php $current = MWC_Core_Fields::get_visibility( $field_key ); ?>
									<option value="hidden" <?php selected( $current, 'hidden' ); ?>><?php esc_html_e( 'Hidden', 'manage-woocommerce-checkout' ); ?></option>
									<option value="optional" <?php selected( $current, 'optional' ); ?>><?php esc_html_e( 'Optional', 'manage-woocommerce-checkout' ); ?></option>
									<option value="required" <?php selected( $current, 'required' ); ?>><?php esc_html_e( 'Required', 'manage-woocommerce-checkout' ); ?></option>
								</select>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<?php submit_button( __( 'Save Core Fields', 'manage-woocommerce-checkout' ) ); ?>
		</form>
		<?php
	}

	private function render_custom_tab() {
		$fields = MWC_Custom_Fields::get_fields();
		?>
		<form method="post" class="mwc-form" id="mwc-custom-fields-form">
			<?php wp_nonce_field( 'mwc_save_custom_fields' ); ?>
			<input type="hidden" name="mwc_action" value="save_custom_fields" />

			<table class="widefat mwc-fields-table" id="mwc-fields-table">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Label', 'manage-woocommerce-checkout' ); ?></th>
						<th><?php esc_html_e( 'Section', 'manage-woocommerce-checkout' ); ?></th>
						<th><?php esc_html_e( 'Type', 'manage-woocommerce-checkout' ); ?></th>
						<th><?php esc_html_e( 'Required', 'manage-woocommerce-checkout' ); ?></th>
						<th><?php esc_html_e( 'Placeholder', 'manage-woocommerce-checkout' ); ?></th>
						<th><?php esc_html_e( 'Select Options (one "value|Label" per line)', 'manage-woocommerce-checkout' ); ?></th>
						<th></th>
					</tr>
				</thead>
				<tbody id="mwc-fields-tbody">
					<?php foreach ( $fields as $field ) : ?>
						<?php $this->render_field_row( $field ); ?>
					<?php endforeach; ?>
				</tbody>
			</table>

			<p><button type="button" class="button" id="mwc-add-field"><?php esc_html_e( '+ Add Field', 'manage-woocommerce-checkout' ); ?></button></p>

			<?php submit_button( __( 'Save Custom Fields', 'manage-woocommerce-checkout' ) ); ?>
		</form>

		<template id="mwc-field-row-template">
			<?php $this->render_field_row( array() ); ?>
		</template>
		<?php
	}

	private function render_field_row( $field ) {
		$slug        = $field['slug'] ?? '';
		$label       = $field['label'] ?? '';
		$location    = $field['location'] ?? 'contact';
		$type        = $field['type'] ?? 'text';
		$required    = ! empty( $field['required'] );
		$placeholder = $field['placeholder'] ?? '';
		$options_raw = $field['options_raw'] ?? '';
		?>
		<tr class="mwc-field-row">
			<td>
				<input type="hidden" name="slug[]" value="<?php echo esc_attr( $slug ); ?>" />
				<input type="hidden" name="deleted[]" class="mwc-deleted" value="" />
				<input type="text" name="label[]" value="<?php echo esc_attr( $label ); ?>" class="regular-text" placeholder="<?php esc_attr_e( 'e.g. Gift message', 'manage-woocommerce-checkout' ); ?>" />
			</td>
			<td>
				<select name="location[]">
					<option value="contact" <?php selected( $location, 'contact' ); ?>><?php esc_html_e( 'Contact information', 'manage-woocommerce-checkout' ); ?></option>
					<option value="address" <?php selected( $location, 'address' ); ?>><?php esc_html_e( 'Address', 'manage-woocommerce-checkout' ); ?></option>
					<option value="order" <?php selected( $location, 'order' ); ?>><?php esc_html_e( 'Order (near order notes)', 'manage-woocommerce-checkout' ); ?></option>
				</select>
			</td>
			<td>
				<select name="type[]" class="mwc-field-type">
					<option value="text" <?php selected( $type, 'text' ); ?>><?php esc_html_e( 'Text', 'manage-woocommerce-checkout' ); ?></option>
					<option value="select" <?php selected( $type, 'select' ); ?>><?php esc_html_e( 'Select', 'manage-woocommerce-checkout' ); ?></option>
					<option value="checkbox" <?php selected( $type, 'checkbox' ); ?>><?php esc_html_e( 'Checkbox', 'manage-woocommerce-checkout' ); ?></option>
				</select>
			</td>
			<td>
				<input type="checkbox" name="required[]" value="1" <?php checked( $required ); ?> />
			</td>
			<td>
				<input type="text" name="placeholder[]" value="<?php echo esc_attr( $placeholder ); ?>" class="regular-text" />
			</td>
			<td>
				<textarea name="options_raw[]" rows="2" class="mwc-options-raw" placeholder="yes|Yes&#10;no|No"><?php echo esc_textarea( $options_raw ); ?></textarea>
			</td>
			<td>
				<button type="button" class="button-link-delete mwc-remove-field"><?php esc_html_e( 'Remove', 'manage-woocommerce-checkout' ); ?></button>
			</td>
		</tr>
		<?php
	}

	private function render_order_screen_tab() {
		$default_labels = $this->default_admin_field_labels();
		$overrides      = MWC_Admin_Order_Fields::get_overrides();
		?>
		<p class="description">
			<?php esc_html_e( 'These controls affect the classic shortcode checkout and the Billing/Shipping address boxes on Orders > [order]. On the WooCommerce Checkout block, shared address fields are hidden only when the same field is hidden for both Billing and Shipping; Country/Region and Email address cannot be hidden there.', 'manage-woocommerce-checkout' ); ?>
		</p>
		<form method="post" class="mwc-form">
			<?php wp_nonce_field( 'mwc_save_order_screen_fields' ); ?>
			<input type="hidden" name="mwc_action" value="save_order_screen_fields" />

			<?php foreach ( MWC_Admin_Order_Fields::FIELD_KEYS as $type => $keys ) : ?>
				<h2><?php echo 'billing' === $type ? esc_html__( 'Billing', 'manage-woocommerce-checkout' ) : esc_html__( 'Shipping', 'manage-woocommerce-checkout' ); ?></h2>
				<table class="widefat mwc-fields-table">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Field', 'manage-woocommerce-checkout' ); ?></th>
							<th><?php esc_html_e( 'Label', 'manage-woocommerce-checkout' ); ?></th>
							<th><?php esc_html_e( 'Is Hidden?', 'manage-woocommerce-checkout' ); ?></th>
							<th><?php esc_html_e( 'Position', 'manage-woocommerce-checkout' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $keys as $key ) : ?>
							<?php $row = $overrides[ $type ][ $key ] ?? array(); ?>
							<tr>
								<th scope="row"><?php echo esc_html( $default_labels[ $key ] ); ?></th>
								<td>
									<input type="text" name="order_field[<?php echo esc_attr( $type ); ?>][<?php echo esc_attr( $key ); ?>][label]" value="<?php echo esc_attr( $row['label'] ?? '' ); ?>" class="regular-text" placeholder="<?php echo esc_attr( $default_labels[ $key ] ); ?>" />
								</td>
								<td>
									<input type="checkbox" name="order_field[<?php echo esc_attr( $type ); ?>][<?php echo esc_attr( $key ); ?>][hidden]" value="1" <?php checked( ! empty( $row['hidden'] ) ); ?> />
								</td>
								<td>
									<input type="number" name="order_field[<?php echo esc_attr( $type ); ?>][<?php echo esc_attr( $key ); ?>][priority]" value="<?php echo esc_attr( $row['priority'] ?? '' ); ?>" class="small-text" placeholder="<?php echo esc_attr( ( array_search( $key, $keys, true ) + 1 ) * 10 ); ?>" />
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endforeach; ?>

			<?php submit_button( __( 'Save Checkout & Order Fields', 'manage-woocommerce-checkout' ) ); ?>
		</form>
		<?php
	}
}
