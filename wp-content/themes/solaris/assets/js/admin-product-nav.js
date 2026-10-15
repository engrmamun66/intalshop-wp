/**
 * Injects Prev/Next product-edit icon buttons next to the "Add new
 * product" button on the product edit screen. Only enqueued there (see
 * inc/woocommerce.php) — solarisProductNav is localized with the actual
 * adjacent-product edit URLs computed server-side.
 */
( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {
		if ( 'undefined' === typeof solarisProductNav ) {
			return;
		}
		var titleAction = document.querySelector( '.page-title-action' );
		if ( ! titleAction ) {
			return;
		}

		function makeButton( url, title, dashicon, label ) {
			var btn = document.createElement( 'a' );
			btn.className = 'solaris-product-nav__btn dashicons-before ' + dashicon;
			btn.href = url || '#';
			btn.setAttribute( 'aria-label', label );
			if ( url ) {
				btn.title = title;
			} else {
				btn.title = label;
				btn.classList.add( 'is-disabled' );
				btn.setAttribute( 'aria-disabled', 'true' );
				btn.addEventListener( 'click', function ( e ) {
					e.preventDefault();
				} );
			}
			return btn;
		}

		var nav = document.createElement( 'span' );
		nav.className = 'solaris-product-nav';
		nav.appendChild( makeButton( solarisProductNav.prevUrl, solarisProductNav.prevTitle, 'dashicons-arrow-left-alt2', 'Edit previous product' ) );
		nav.appendChild( makeButton( solarisProductNav.nextUrl, solarisProductNav.nextTitle, 'dashicons-arrow-right-alt2', 'Edit next product' ) );

		titleAction.insertAdjacentElement( 'afterend', nav );
	} );
} )();
