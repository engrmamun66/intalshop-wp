/**
 * Admin-panel UI for the "Hero Slides" repeater control (Solaris_Hero_Slides_Control
 * in inc/customizer-controls.php). Runs inside the Customizer's controls
 * panel (customize.php), not the live-preview iframe.
 *
 * The control itself is just a hidden input holding a JSON array of
 * {id, active, eyebrow, title, subtext, button1_text, button1_url,
 * button2_text, button2_url} objects (the last seven are optional per-slide
 * text/button overrides — left blank, the theme falls back to the global
 * Hero text Customizer fields), linked to the setting via
 * WP_Customize_Control::link() — WordPress's core customize-controls.js
 * automatically watches any element with [data-customize-setting-link]
 * inside a control and pushes its value to the setting on 'change', so all
 * this file has to do is keep that hidden input's value in sync with the
 * visible slide list and fire 'change'.
 */
( function ( $ ) {
	'use strict';

	function escAttr( str ) {
		return String( str ).replace( /&/g, '&amp;' ).replace( /"/g, '&quot;' ).replace( /</g, '&lt;' ).replace( />/g, '&gt;' );
	}
	function escHtml( str ) {
		return String( str ).replace( /&/g, '&amp;' ).replace( /</g, '&lt;' ).replace( />/g, '&gt;' );
	}

	function parseSlides( raw ) {
		try {
			var parsed = JSON.parse( raw );
			return Array.isArray( parsed ) ? parsed : [];
		} catch ( e ) {
			return [];
		}
	}

	function initRepeater( container ) {
		var $container = $( container );
		var $list = $container.find( '.solaris-hero-slides__list' );
		var $input = $container.find( '.solaris-hero-slides__value' );
		var $addButton = $container.find( '.solaris-hero-slides__add' );
		var slides = parseSlides( $input.val() );
		var attachmentCache = {};

		function commit() {
			var next = [];
			$list.find( '.solaris-hero-slides__item' ).each( function () {
				var $item = $( this );
				var slide = {
					id: parseInt( $item.attr( 'data-id' ), 10 ),
					active: $item.find( '.solaris-hero-slides__active-input' ).is( ':checked' ),
				};
				$item.find( '.solaris-hero-slides__field' ).each( function () {
					slide[ $( this ).attr( 'data-field' ) ] = $( this ).val();
				} );
				next.push( slide );
			} );
			slides = next;
			$input.val( JSON.stringify( slides ) ).trigger( 'change' );
		}

		function attachmentUrl( id, callback ) {
			if ( attachmentCache[ id ] ) {
				callback( attachmentCache[ id ] );
				return;
			}
			var attachment = wp.media.attachment( id );
			attachment.fetch().done( function () {
				var sizes = attachment.get( 'sizes' ) || {};
				var url = ( sizes.thumbnail && sizes.thumbnail.url ) || attachment.get( 'url' ) || '';
				attachmentCache[ id ] = url;
				callback( url );
			} );
		}

		function renderItem( slide ) {
			var $item = $(
				'<li class="solaris-hero-slides__item" data-id="' + slide.id + '">' +
					'<div class="solaris-hero-slides__row">' +
						'<span class="solaris-hero-slides__handle" aria-hidden="true">☷</span>' +
						'<img class="solaris-hero-slides__thumb" alt="" />' +
						'<label class="solaris-hero-slides__active">' +
							'<input type="checkbox" class="solaris-hero-slides__active-input" aria-label="Active" ' + ( slide.active ? 'checked' : '' ) + ' />' +
						'</label>' +
						'<button type="button" class="button-link solaris-hero-slides__replace" aria-label="Replace image"><span class="dashicons dashicons-update" aria-hidden="true"></span></button>' +
						'<button type="button" class="button-link solaris-hero-slides__toggle-text" aria-label="Edit text"><span class="dashicons dashicons-edit" aria-hidden="true"></span></button>' +
						'<button type="button" class="button-link-delete solaris-hero-slides__remove">Remove</button>' +
					'</div>' +
					'<div class="solaris-hero-slides__details">' +
						'<label>Eyebrow / Dialog<input type="text" class="solaris-hero-slides__field" data-field="eyebrow" value="' + escAttr( slide.eyebrow || '' ) + '" placeholder="Falls back to the global Eyebrow Text if blank" /></label>' +
						'<label>Title<input type="text" class="solaris-hero-slides__field" data-field="title" value="' + escAttr( slide.title || '' ) + '" placeholder="Falls back to the global Headline if blank" /></label>' +
						'<label>Subtext<textarea class="solaris-hero-slides__field" data-field="subtext" rows="2" placeholder="Falls back to the global Supporting Description if blank">' + escHtml( slide.subtext || '' ) + '</textarea></label>' +
						'<div class="solaris-hero-slides__field-pair">' +
							'<label>Button 1 Text<input type="text" class="solaris-hero-slides__field" data-field="button1_text" value="' + escAttr( slide.button1_text || '' ) + '" /></label>' +
							'<label>Button 1 URL<input type="url" class="solaris-hero-slides__field" data-field="button1_url" value="' + escAttr( slide.button1_url || '' ) + '" /></label>' +
						'</div>' +
						'<div class="solaris-hero-slides__field-pair">' +
							'<label>Button 2 Text<input type="text" class="solaris-hero-slides__field" data-field="button2_text" value="' + escAttr( slide.button2_text || '' ) + '" /></label>' +
							'<label>Button 2 URL<input type="url" class="solaris-hero-slides__field" data-field="button2_url" value="' + escAttr( slide.button2_url || '' ) + '" /></label>' +
						'</div>' +
					'</div>' +
				'</li>'
			);
			attachmentUrl( slide.id, function ( url ) {
				$item.find( '.solaris-hero-slides__thumb' ).attr( 'src', url );
			} );
			return $item;
		}

		function render() {
			$list.empty();
			slides.forEach( function ( slide ) {
				$list.append( renderItem( slide ) );
			} );
		}

		$list.on( 'change', '.solaris-hero-slides__active-input', commit );

		$list.on( 'change', '.solaris-hero-slides__field', commit );

		$list.on( 'click', '.solaris-hero-slides__toggle-text', function () {
			$( this ).closest( '.solaris-hero-slides__item' ).find( '.solaris-hero-slides__details' ).toggleClass( 'is-open' );
		} );

		$list.on( 'click', '.solaris-hero-slides__remove', function () {
			$( this ).closest( '.solaris-hero-slides__item' ).remove();
			commit();
		} );

		$list.on( 'click', '.solaris-hero-slides__replace', function () {
			var $item = $( this ).closest( '.solaris-hero-slides__item' );
			var frame = wp.media( { title: 'Replace Slide Image', multiple: false } );
			frame.on( 'select', function () {
				var attachment = frame.state().get( 'selection' ).first().toJSON();
				$item.attr( 'data-id', attachment.id );
				var url = ( attachment.sizes && attachment.sizes.thumbnail && attachment.sizes.thumbnail.url ) || attachment.url;
				$item.find( '.solaris-hero-slides__thumb' ).attr( 'src', url );
				commit();
			} );
			frame.open();
		} );

		$addButton.on( 'click', function () {
			var frame = wp.media( { title: 'Add Hero Slide Image(s)', multiple: true } );
			frame.on( 'select', function () {
				frame.state().get( 'selection' ).each( function ( attachment ) {
					var json = attachment.toJSON();
					slides.push( { id: json.id, active: true } );
				} );
				render();
				commit();
			} );
			frame.open();
		} );

		$list.sortable( {
			handle: '.solaris-hero-slides__handle',
			axis: 'y',
			update: commit,
		} );

		render();
	}

	$( document ).ready( function () {
		$( '.solaris-hero-slides' ).each( function () {
			initRepeater( this );
		} );
	} );
} )( jQuery );
