/**
 * Live Customizer preview for postMessage-transport settings (colors, hero
 * copy, overlay opacity). Falls back to a full refresh for everything else,
 * so this file only needs to handle the fast-path subset.
 */
( function ( wp ) {
	if ( ! wp || ! wp.customize ) {
		return;
	}

	var colorVarMap = {
		color_primary: '--color-primary',
		color_secondary: '--color-secondary',
		color_accent: '--color-accent',
		color_text: '--color-text',
		color_heading: '--color-heading',
		color_background: '--color-background',
		header_background: '--color-header-bg',
		header_text_color: '--color-header-text',
		footer_background: '--color-footer-bg',
		footer_text_color: '--color-footer-text',
		footer_heading_color: '--color-footer-heading',
		footer_accent_color: '--color-footer-accent',
		button_text_color: '--color-button-text',
		btn_gradient_start: '--color-btn-gradient-start',
		btn_gradient_mid: '--color-btn-gradient-mid',
		btn_gradient_end: '--color-btn-gradient-end',
	};

	Object.keys( colorVarMap ).forEach( function ( settingId ) {
		wp.customize( settingId, function ( value ) {
			value.bind( function ( newVal ) {
				document.documentElement.style.setProperty( colorVarMap[ settingId ], newVal );
			} );
		} );
	} );

	wp.customize( 'hero_overlay_opacity', function ( value ) {
		value.bind( function ( newVal ) {
			document.documentElement.style.setProperty( '--hero-overlay', newVal );
		} );
	} );

	var footerLogoSizeMap = {
		footer_logo_width: '--footer-logo-width',
		footer_logo_height: '--footer-logo-height',
	};
	Object.keys( footerLogoSizeMap ).forEach( function ( settingId ) {
		wp.customize( settingId, function ( value ) {
			value.bind( function ( newVal ) {
				document.documentElement.style.setProperty( footerLogoSizeMap[ settingId ], newVal + 'px' );
			} );
		} );
	} );

	var textFields = {
		hero_eyebrow: '.hero .eyebrow',
		hero_heading: '.hero h1',
		hero_description: '.hero .hero-lead',
		hero_primary_text: '.hero .hero-actions .button:not(.btn--outline)',
		hero_secondary_text: '.hero .hero-actions .btn--outline',
	};
	Object.keys( textFields ).forEach( function ( settingId ) {
		wp.customize( settingId, function ( value ) {
			value.bind( function ( newVal ) {
				var el = document.querySelector( textFields[ settingId ] );
				if ( el ) {
					el.textContent = newVal;
				}
			} );
		} );
	} );
} )( window.wp );
