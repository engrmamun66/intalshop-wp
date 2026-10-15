/**
 * Admin-panel UI for the "FAQ Items" repeater control (Solaris_Faq_Items_Control
 * in inc/customizer-controls.php). Runs inside the Customizer's controls
 * panel (customize.php), not the live-preview iframe.
 *
 * The control itself is just a hidden input holding a JSON array of
 * {question, answer} objects, linked to the setting via
 * WP_Customize_Control::link() — WordPress's core customize-controls.js
 * automatically watches any element with [data-customize-setting-link]
 * inside a control and pushes its value to the setting on 'change', so all
 * this file has to do is keep that hidden input's value in sync with the
 * visible question list and fire 'change'. Modeled directly on
 * customizer-hero-slides.js, minus the media-picker parts this doesn't need.
 */
( function ( $ ) {
	'use strict';

	function escAttr( str ) {
		return String( str ).replace( /&/g, '&amp;' ).replace( /"/g, '&quot;' ).replace( /</g, '&lt;' ).replace( />/g, '&gt;' );
	}
	function escHtml( str ) {
		return String( str ).replace( /&/g, '&amp;' ).replace( /</g, '&lt;' ).replace( />/g, '&gt;' );
	}

	function parseItems( raw ) {
		try {
			var parsed = JSON.parse( raw );
			return Array.isArray( parsed ) ? parsed : [];
		} catch ( e ) {
			return [];
		}
	}

	function initRepeater( container ) {
		var $container = $( container );
		var $list = $container.find( '.solaris-faq-items__list' );
		var $input = $container.find( '.solaris-faq-items__value' );
		var $addButton = $container.find( '.solaris-faq-items__add' );
		var items = parseItems( $input.val() );

		function commit() {
			var next = [];
			$list.find( '.solaris-faq-items__item' ).each( function () {
				var $item = $( this );
				next.push( {
					question: $item.find( '.solaris-faq-items__question' ).val(),
					answer: $item.find( '.solaris-faq-items__answer' ).val(),
				} );
			} );
			items = next;
			$input.val( JSON.stringify( items ) ).trigger( 'change' );
		}

		function renderItem( item ) {
			return $(
				'<li class="solaris-faq-items__item">' +
					'<div class="solaris-faq-items__row">' +
						'<span class="solaris-faq-items__handle" aria-hidden="true">☷</span>' +
						'<input type="text" class="solaris-faq-items__question" placeholder="Question" value="' + escAttr( item.question || '' ) + '" />' +
						'<button type="button" class="button-link-delete solaris-faq-items__remove">Remove</button>' +
					'</div>' +
					'<textarea class="solaris-faq-items__answer" rows="2" placeholder="Answer">' + escHtml( item.answer || '' ) + '</textarea>' +
				'</li>'
			);
		}

		function render() {
			$list.empty();
			items.forEach( function ( item ) {
				$list.append( renderItem( item ) );
			} );
		}

		$list.on( 'change', '.solaris-faq-items__question, .solaris-faq-items__answer', commit );

		$list.on( 'click', '.solaris-faq-items__remove', function () {
			$( this ).closest( '.solaris-faq-items__item' ).remove();
			commit();
		} );

		$addButton.on( 'click', function () {
			items.push( { question: '', answer: '' } );
			render();
			commit();
			$list.find( '.solaris-faq-items__item' ).last().find( '.solaris-faq-items__question' ).trigger( 'focus' );
		} );

		$list.sortable( {
			handle: '.solaris-faq-items__handle',
			axis: 'y',
			update: commit,
		} );

		render();
	}

	$( document ).ready( function () {
		$( '.solaris-faq-items' ).each( function () {
			initRepeater( this );
		} );
	} );
} )( jQuery );
