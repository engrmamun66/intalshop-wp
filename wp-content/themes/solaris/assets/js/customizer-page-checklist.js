/**
 * Admin-panel UI for the page-checklist control (Solaris_Page_Checklist_Control
 * in inc/customizer-controls.php) — used by Fighter-Z's "Specific Pages"
 * display mode. Runs inside the Customizer's controls panel (customize.php),
 * not the live-preview iframe.
 *
 * Same shape as the hero-slides repeater: a hidden input holds the real
 * JSON-array value and is linked to the setting via
 * WP_Customize_Control::link() — core customize-controls.js watches any
 * [data-customize-setting-link] element inside a control and pushes its
 * value to the setting on 'change'. This file's only job is keeping that
 * hidden input in sync with whichever checkboxes are ticked.
 */
( function ( $ ) {
	'use strict';

	function syncHiddenValue( $container ) {
		var ids = [];
		$container.find( '.solaris-page-checklist__item input[type="checkbox"]:checked' ).each( function () {
			ids.push( parseInt( this.value, 10 ) );
		} );
		$container.find( '.solaris-page-checklist__value' ).val( JSON.stringify( ids ) ).trigger( 'change' );
	}

	$( document ).on( 'change', '.solaris-page-checklist__item input[type="checkbox"]', function () {
		syncHiddenValue( $( this ).closest( '.solaris-page-checklist' ) );
	} );
} )( jQuery );
