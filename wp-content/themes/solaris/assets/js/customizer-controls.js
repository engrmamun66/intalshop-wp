/**
 * Runs in the Customizer's admin controls panel (not the live-preview
 * iframe — see customizer-preview.js for that). WordPress's native "range"
 * control renders a bare <input type="range"> with no visible current
 * value, so this adds one next to every range slider in the panel and
 * keeps it in sync as the slider moves.
 */
( function () {
	'use strict';

	function addValueDisplay( input ) {
		if ( input.dataset.solarisValueBound ) {
			return;
		}
		input.dataset.solarisValueBound = 'true';

		var output = document.createElement( 'span' );
		output.className = 'solaris-range-value';
		output.textContent = input.value;
		input.insertAdjacentElement( 'afterend', output );

		input.addEventListener( 'input', function () {
			output.textContent = input.value;
		} );
	}

	function scan( root ) {
		root.querySelectorAll( '.customize-control-range input[type="range"]' ).forEach( addValueDisplay );
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		scan( document );

		// The Customizer renders/expands sections dynamically as panels are
		// opened, so keep watching for range inputs added after page load.
		var observer = new MutationObserver( function ( mutations ) {
			mutations.forEach( function ( mutation ) {
				mutation.addedNodes.forEach( function ( node ) {
					if ( node.nodeType !== 1 ) {
						return;
					}
					if ( node.matches && node.matches( '.customize-control-range input[type="range"]' ) ) {
						addValueDisplay( node );
					} else if ( node.querySelectorAll ) {
						scan( node );
					}
				} );
			} );
		} );
		observer.observe( document.body, { childList: true, subtree: true } );
	} );
} )();
