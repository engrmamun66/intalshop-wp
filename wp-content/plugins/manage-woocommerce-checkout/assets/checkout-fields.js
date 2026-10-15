( function () {
	'use strict';

	var settings = window.mwcCheckoutFields || {};
	var overrides = settings.overrides || {};

	var fallbackValues = {
		first_name: '-',
		last_name: '-',
		company: '',
		address_1: '-',
		address_2: '',
		city: '-',
		postcode: '',
		state: '',
		country: '',
		email: 'hidden@example.com',
		phone: '',
	};

	function isHidden( type, key ) {
		return !! (
			overrides[ type ] &&
			overrides[ type ][ key ] &&
			overrides[ type ][ key ].hidden
		);
	}

	function getFieldControl( type, key ) {
		var dashed = type + '-' + key;
		var underscored = type + '_' + key;
		return document.querySelector(
			[
				'[name="' + dashed + '"]',
				'[id="' + dashed + '"]',
				'[name="' + underscored + '"]',
				'[id="' + underscored + '"]',
			].join( ',' )
		);
	}

	function getEmailControl() {
		return document.querySelector(
			'[name="email"],[id="email"],[name="billing_email"],[id="billing_email"],input[type="email"]'
		);
	}

	function getWrapper( control ) {
		if ( ! control ) {
			return null;
		}

		return control.closest(
			[
				'.wc-block-components-text-input',
				'.wc-block-components-select-input',
				'.wc-block-components-combobox',
				'.form-row',
				'p',
				'div',
			].join( ',' )
		);
	}

	function getVisibleValue( type, key ) {
		var control = getFieldControl( type, key );
		return control && control.value ? control.value.trim() : '';
	}

	function getFallbackValue( type, key ) {
		if ( 'last_name' === key ) {
			return getVisibleValue( type, 'first_name' ) || fallbackValues.last_name;
		}

		// hasOwnProperty, not "|| '-'" — country/state/company/address_2 are
		// deliberately '' in fallbackValues (submit genuinely blank, not a
		// filler dash). "|| '-'" treats that intentional '' as "unset" and
		// substitutes '-', which setControlValue() then force-writes into
		// the country/state <select> — an invalid value that fails
		// server-side country/state validation at checkout.
		return Object.prototype.hasOwnProperty.call( fallbackValues, key ) ? fallbackValues[ key ] : '-';
	}

	function setControlValue( control, value ) {
		if ( ! control || ! value || control.value === value ) {
			return;
		}

		var descriptor = Object.getOwnPropertyDescriptor(
			control instanceof window.HTMLSelectElement ? window.HTMLSelectElement.prototype : window.HTMLInputElement.prototype,
			'value'
		);

		if ( descriptor && descriptor.set ) {
			descriptor.set.call( control, value );
		} else {
			control.value = value;
		}

		control.dispatchEvent( new Event( 'input', { bubbles: true } ) );
		control.dispatchEvent( new Event( 'change', { bubbles: true } ) );
	}

	function hideControl( control ) {
		var wrapper = getWrapper( control );
		if ( wrapper ) {
			wrapper.classList.add( 'mwc-checkout-field-hidden' );
		} else if ( control ) {
			control.classList.add( 'mwc-checkout-field-hidden' );
		}

		if ( control ) {
			control.setAttribute( 'aria-hidden', 'true' );
			control.setAttribute( 'tabindex', '-1' );
		}
	}

	function applyField( type, key ) {
		var control = 'billing' === type && 'email' === key ? getEmailControl() : getFieldControl( type, key );
		var fallback = getFallbackValue( type, key );

		if ( ! isHidden( type, key ) ) {
			return;
		}

		hideControl( control );
		setControlValue( control, fallback );
	}

	var SAME_ADDRESS_OLD_TEXT = 'Use same address for billing';
	var SAME_ADDRESS_NEW_TEXT = 'Delivery address same as billing';

	// Returns the "<span class="wc-block-components-checkbox__label">" text
	// node itself — NOT the outer "<label for="...">" that wraps both this
	// span and the checkbox <input> as siblings. Callers that need the
	// input must go up via .closest('label') first; querySelector() on
	// this span's own (empty) subtree will always return null.
	function findSameAddressLabelSpan() {
		return Array.prototype.filter.call(
			document.querySelectorAll( '.wc-block-components-checkbox__label' ),
			function ( span ) {
				var text = span.textContent.trim();
				return text === SAME_ADDRESS_OLD_TEXT || text === SAME_ADDRESS_NEW_TEXT;
			}
		)[ 0 ] || null;
	}

	/**
	 * "Use same address for billing" is a hardcoded string inside WC Blocks'
	 * compiled checkout JS — no PHP filter or wp.i18n override reaches it,
	 * so relabeling means finding the rendered text and replacing it, same
	 * as every other Blocks-checkout workaround in this file. Matched by
	 * its current exact text (not just the shared checkbox-label class,
	 * which other checkboxes like "Add a note to your order" reuse too) so
	 * only this one checkbox is ever touched, and it's idempotent once renamed.
	 */
	function relabelSameAddressCheckbox() {
		var span = findSameAddressLabelSpan();
		if ( span && span.textContent.trim() === SAME_ADDRESS_OLD_TEXT ) {
			span.textContent = SAME_ADDRESS_NEW_TEXT;
		}
	}

	/**
	 * Defaults the checkbox to checked on initial load only — WooCommerce
	 * can render it unchecked from stale session/cart address data even on
	 * a fresh page view. Deliberately one-shot (guarded by
	 * sameAddressChecked below), not re-applied on every settle: the
	 * Shipping address section this checkbox reveals only renders when
	 * it's unchecked, so fighting every uncheck would make that section
	 * permanently unreachable. A one-time default plus respecting whatever
	 * the visitor (or this file's own hidden-field fallbacks) does
	 * afterward is the right balance.
	 */
	var sameAddressChecked = false;
	function forceSameAddressChecked() {
		if ( sameAddressChecked ) {
			return;
		}

		var span = findSameAddressLabelSpan();
		var wrapperLabel = span ? span.closest( 'label' ) : null;
		var input = wrapperLabel ? wrapperLabel.querySelector( 'input[type="checkbox"]' ) : null;

		if ( ! input ) {
			return;
		}

		if ( ! input.checked ) {
			input.click();
		}

		sameAddressChecked = true;
	}

	/**
	 * The Address block collapses into a read-only summary card (with an
	 * "Edit" button) once WooCommerce considers the address "complete" —
	 * intentional core behavior, but unwanted here: the store should always
	 * show the live editable form. Auto-clicking "Edit" the instant it
	 * appears flips the block's own internal state back to edit mode, the
	 * same as if the visitor had clicked it themselves.
	 */
	function collapseAddressSummaries() {
		document.querySelectorAll( '.wc-block-components-address-card__edit' ).forEach( function ( button ) {
			button.click();
		} );
	}

	function applySettings() {
		Object.keys( overrides ).forEach( function ( type ) {
			Object.keys( overrides[ type ] || {} ).forEach( function ( key ) {
				applyField( type, key );
			} );
		} );

		collapseAddressSummaries();
		relabelSameAddressCheckbox();
		forceSameAddressChecked();
	}

	// Re-run when first_name changes — last_name's fallback mirrors it
	// (getFallbackValue), so a hidden last_name needs to pick up any edit.
	document.addEventListener( 'input', function ( event ) {
		if ( event.target && event.target.matches( 'input[name$="first_name"],input[id$="first_name"],input[name$="-first_name"],input[id$="-first_name"]' ) ) {
			applySettings();
		}
	} );

	document.addEventListener( 'DOMContentLoaded', applySettings );
	window.addEventListener( 'load', applySettings );

	new MutationObserver( applySettings ).observe( document.documentElement, {
		childList: true,
		subtree: true,
	} );
}() );
