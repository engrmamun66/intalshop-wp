/**
 * Checkout page only. The Checkout block's "Terms" block and the Place
 * Order button's row (.wc-block-checkout__terms / .wc-block-checkout__actions_row,
 * the latter nested inside .wc-block-checkout__actions) render async via
 * React — no PHP template hook reaches inside that tree, so this waits for
 * both to exist, moves them together into one sticky bottom bar, and
 * inserts a "Continue Shopping" link right before the Place Order button.
 *
 * Deliberately moves .wc-block-checkout__actions_row itself, NOT its parent
 * .wc-block-checkout__actions — that parent also contains WC's own mobile
 * order-summary "fill" (shown only under a 700px container width, a
 * collapsed cart summary WC Blocks portals in there for narrow screens).
 * Moving the whole parent into a fixed bottom bar would have dragged that
 * entire order summary into the bar too, on mobile only. Leaving the parent
 * in place and only pulling out the button row keeps that summary exactly
 * where WC intends it — inline, scrollable — while the bar stays just the
 * terms text + buttons everywhere.
 *
 * Also relabels the two address blocks' headings — see
 * relabelAddressHeadings() below for why, and for the real-world tradeoff
 * that relabel accepts.
 */
( function () {
	'use strict';

	var data = window.solarisCheckoutActions || {};

	function buildBar() {
		if ( document.querySelector( '.checkout-sticky-bar' ) ) {
			return true;
		}

		var terms = document.querySelector( '.wc-block-checkout__terms' );
		var actionsRow = document.querySelector( '.wc-block-checkout__actions .wc-block-checkout__actions_row' );

		if ( ! terms || ! actionsRow || ! terms.parentNode ) {
			return false;
		}

		var continueLink = document.createElement( 'a' );
		continueLink.className = 'button btn--outline checkout-sticky-bar__continue';
		continueLink.href = data.continueShoppingUrl || '/';
		continueLink.textContent = data.continueShoppingText || 'Continue Shopping';
		actionsRow.insertBefore( continueLink, actionsRow.firstChild );

		var bar = document.createElement( 'div' );
		bar.className = 'checkout-sticky-bar';
		terms.parentNode.insertBefore( bar, terms );
		bar.appendChild( terms );
		bar.appendChild( actionsRow );

		return true;
	}

	/**
	 * The store wants the block WooCommerce always renders (its own
	 * "shipping address", never conditional) to read as "Billing address"
	 * and come first, and the block WC only renders when the "same as
	 * billing" checkbox is unchecked (its own "billing address") to read
	 * as "Shipping address". This ONLY swaps the visible heading text —
	 * WooCommerce's own field internally is still "shipping_address" for
	 * whichever block is always-shown, "billing_address" for the
	 * conditional one, unchanged. Accepted, explicit tradeoff: if a
	 * customer ever unchecks the box and types a different delivery
	 * address into the box now labeled "Shipping address", that value
	 * lands in WC's internal billing field, not the shipping field
	 * fulfillment/shipping-label tooling actually reads — the order still
	 * physically ships to whatever's in the first ("Billing address")
	 * box. Re-applied every tick since these blocks re-render as their
	 * own fields are typed into.
	 */
	function relabelOne( blockSelector, fromText, toText ) {
		var block = document.querySelector( blockSelector );
		if ( ! block ) {
			return;
		}
		[ '.wc-block-components-checkout-step__title', 'legend.screen-reader-text' ].forEach( function ( selector ) {
			var el = block.querySelector( selector );
			if ( el && el.textContent.trim() === fromText ) {
				el.textContent = toText;
			}
		} );
	}

	function relabelAddressHeadings() {
		relabelOne( '.wp-block-woocommerce-checkout-shipping-address-block', 'Shipping address', 'Billing address' );
		relabelOne( '.wp-block-woocommerce-checkout-billing-address-block', 'Billing address', 'Shipping address' );
	}

	buildBar();
	relabelAddressHeadings();

	new MutationObserver( function () {
		buildBar();
		relabelAddressHeadings();
	} ).observe( document.documentElement, { childList: true, subtree: true } );
}() );
