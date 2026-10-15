/*
 * Global full-page preloader (assets/css/preloader.css owns the visuals).
 * Hides 2 seconds after the window's "load" event — not DOMContentLoaded,
 * which fires before images/fonts/etc. actually finish, so "ready the page"
 * here means everything has genuinely finished loading, not just the DOM.
 */
(function () {
	function hide( overlay ) {
		overlay.classList.add( 'is-hidden' );
		overlay.addEventListener( 'transitionend', function () {
			overlay.remove();
		}, { once: true } );
	}

	function onReady() {
		var overlay = document.querySelector( '.solaris-preloader' );
		if ( ! overlay ) {
			return;
		}
		setTimeout( function () {
			hide( overlay );
		}, 2000 );
	}

	if ( document.readyState === 'complete' ) {
		onReady();
	} else {
		window.addEventListener( 'load', onReady );
	}
})();
