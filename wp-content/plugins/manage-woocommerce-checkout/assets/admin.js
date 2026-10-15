( function () {
	'use strict';

	var tbody = document.getElementById( 'mwc-fields-tbody' );
	var template = document.getElementById( 'mwc-field-row-template' );
	var addButton = document.getElementById( 'mwc-add-field' );

	if ( ! tbody || ! template || ! addButton ) {
		return; // Not on the Custom Fields tab.
	}

	function toggleOptionsColumn( row ) {
		var type = row.querySelector( '.mwc-field-type' ).value;
		var optionsField = row.querySelector( '.mwc-options-raw' );
		optionsField.closest( 'td' ).style.display = 'select' === type ? '' : 'none';
	}

	function wireRow( row ) {
		toggleOptionsColumn( row );
		row.querySelector( '.mwc-field-type' ).addEventListener( 'change', function () {
			toggleOptionsColumn( row );
		} );
		row.querySelector( '.mwc-remove-field' ).addEventListener( 'click', function () {
			row.querySelector( '.mwc-deleted' ).value = '1';
			row.style.display = 'none';
		} );
	}

	// Existing rows rendered server-side.
	Array.prototype.forEach.call( tbody.querySelectorAll( '.mwc-field-row' ), wireRow );

	addButton.addEventListener( 'click', function () {
		var fragment = template.content.cloneNode( true );
		var row = fragment.querySelector( '.mwc-field-row' );
		tbody.appendChild( row );
		wireRow( row );
		row.querySelector( 'input[name="label[]"]' ).focus();
	} );
} )();
