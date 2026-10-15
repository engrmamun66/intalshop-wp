/**
 * Solaris theme frontend behavior: mobile nav, sticky header, full-screen
 * search modal, FAQ accordion, scroll-reveal. Vanilla JS, no framework, no
 * dependency on WooCommerce's own scripts.
 */
( function () {
	'use strict';

	/*
	 * Shared between initFlyToCart and initCartPanel: how many fly-to-cart
	 * clones are currently mid-flight (a counter, not a flag, since
	 * "Allow Multiple for Multi Quantity?" can launch several at once for
	 * one add-to-cart). Lets the cart panel's "open after add to cart"
	 * (Customizer: Product Cards → After Add to Cart, Open Minicart?) wait
	 * for every flight to actually finish before opening, instead of
	 * popping up mid-animation — the AJAX add-to-cart request it would
	 * otherwise open on typically resolves well before the ~0.7s flight(s)
	 * do. Stays permanently 0 (never blocks) when fly-to-cart itself is off.
	 */
	var flyToCartActiveCount = 0;

	document.addEventListener( 'DOMContentLoaded', function () {
		initMobileNav();
		initStickyHeader();
		initHeroSlider();
		initSearchModal();
		initWishlist();
		initFaqAccordion();
		initScrollReveal();
		initBackToTop();
		initLoopQuantity();
		initQtySteppers();
		initShopCategoriesToggle();
		initShopLoading();
		initFlyToCart();
		initCartPanel();
		initSingleProductAjaxAddToCart();
		initQuickView();
	} );

	/**
	 * Hero slider: crossfades between .hero-slide layers, autoplays, and
	 * responds to the .hero-dots buttons. Only present in the DOM at all
	 * when the Customizer has 2+ active Hero Slides configured (see
	 * template-parts/home/hero.php) — with 0 or 1 slide the hero is just a
	 * plain static background image and this finds nothing to do.
	 */
	function initHeroSlider() {
		var slider = document.querySelector( '[data-hero-slider]' );
		if ( ! slider ) {
			return;
		}
		var slides = Array.prototype.slice.call( slider.querySelectorAll( '.hero-slide' ) );
		var dots = Array.prototype.slice.call( document.querySelectorAll( '[data-hero-dot]' ) );
		var contents = Array.prototype.slice.call( document.querySelectorAll( '[data-hero-content]' ) );
		if ( slides.length < 2 ) {
			return;
		}
		var current = 0;
		var intervalId = null;
		var AUTOPLAY_MS = 6000;

		function goTo( index ) {
			slides[ current ].classList.remove( 'is-active' );
			contents[ current ] && contents[ current ].classList.remove( 'is-active' );
			dots[ current ] && dots[ current ].classList.remove( 'is-active' );
			dots[ current ] && dots[ current ].setAttribute( 'aria-selected', 'false' );

			current = ( index + slides.length ) % slides.length;

			slides[ current ].classList.add( 'is-active' );
			contents[ current ] && contents[ current ].classList.add( 'is-active' );
			dots[ current ] && dots[ current ].classList.add( 'is-active' );
			dots[ current ] && dots[ current ].setAttribute( 'aria-selected', 'true' );
		}

		function startAutoplay() {
			intervalId = window.setInterval( function () {
				goTo( current + 1 );
			}, AUTOPLAY_MS );
		}

		function stopAutoplay() {
			window.clearInterval( intervalId );
		}

		dots.forEach( function ( dot, index ) {
			dot.addEventListener( 'click', function () {
				stopAutoplay();
				goTo( index );
				startAutoplay(); // Restart the countdown so a manual pick doesn't get instantly overridden.
			} );
		} );

		startAutoplay();
	}

	/**
	 * Favorite/wishlist toggle. No account system or backend for it — state
	 * lives entirely in the visitor's own browser (localStorage) keyed by
	 * product ID, so it works for guests, survives reloads, and needs no
	 * server round-trip. Called again after the search modal swaps in new
	 * AJAX-loaded product cards, since those buttons start out unbound.
	 */
	function initWishlist() {
		var STORAGE_KEY = 'solarisWishlist';

		function getWishlist() {
			try {
				return JSON.parse( window.localStorage.getItem( STORAGE_KEY ) ) || [];
			} catch ( e ) {
				return [];
			}
		}
		function setWishlist( ids ) {
			try {
				window.localStorage.setItem( STORAGE_KEY, JSON.stringify( ids ) );
			} catch ( e ) {
				// Storage unavailable (private browsing, quota, etc.) — toggle still
				// works visually for the current page view, just won't persist.
			}
		}
		function refresh( btn ) {
			var id = btn.getAttribute( 'data-wishlist-toggle' );
			var isActive = -1 !== getWishlist().indexOf( id );
			btn.classList.toggle( 'is-active', isActive );
			btn.setAttribute( 'aria-pressed', isActive ? 'true' : 'false' );
		}

		document.querySelectorAll( '[data-wishlist-toggle]' ).forEach( function ( btn ) {
			refresh( btn );
			if ( btn.dataset.wishlistBound ) {
				return;
			}
			btn.dataset.wishlistBound = 'true';
			btn.addEventListener( 'click', function ( e ) {
				e.preventDefault();
				var id = btn.getAttribute( 'data-wishlist-toggle' );
				var list = getWishlist();
				var index = list.indexOf( id );
				if ( -1 === index ) {
					list.push( id );
				} else {
					list.splice( index, 1 );
				}
				setWishlist( list );
				refresh( btn );
			} );
		} );
	}

	function initMobileNav() {
		var toggle = document.querySelector( '.menu-toggle' );
		var panel = document.getElementById( 'mobile-nav' );
		if ( ! toggle || ! panel ) {
			return;
		}
		var closeBtn = panel.querySelector( '.mobile-nav__close' );

		function open() {
			panel.setAttribute( 'data-open', 'true' );
			panel.setAttribute( 'aria-hidden', 'false' );
			toggle.setAttribute( 'aria-expanded', 'true' );
			document.body.classList.add( 'mobile-nav-open' );
			var firstLink = panel.querySelector( 'a, button' );
			if ( firstLink ) {
				firstLink.focus();
			}
		}
		function close() {
			panel.setAttribute( 'data-open', 'false' );
			panel.setAttribute( 'aria-hidden', 'true' );
			toggle.setAttribute( 'aria-expanded', 'false' );
			document.body.classList.remove( 'mobile-nav-open' );
			toggle.focus();
		}

		toggle.addEventListener( 'click', function () {
			var isOpen = panel.getAttribute( 'data-open' ) === 'true';
			isOpen ? close() : open();
		} );
		if ( closeBtn ) {
			closeBtn.addEventListener( 'click', close );
		}
		panel.addEventListener( 'keydown', function ( e ) {
			if ( e.key === 'Escape' ) {
				close();
			}
		} );
		panel.querySelectorAll( 'a' ).forEach( function ( link ) {
			link.addEventListener( 'click', close );
		} );
	}

	function initStickyHeader() {
		var header = document.getElementById( 'masthead' );
		if ( ! header || header.dataset.sticky !== 'true' ) {
			return;
		}
		var threshold = 60;
		function onScroll() {
			if ( window.scrollY > threshold ) {
				header.classList.add( 'is-stuck' );
			} else {
				header.classList.remove( 'is-stuck' );
			}
		}
		window.addEventListener( 'scroll', onScroll, { passive: true } );
		onScroll();
	}

	function initSearchModal() {
		var modal = document.getElementById( 'search-modal' );
		if ( ! modal || typeof solarisData === 'undefined' ) {
			return;
		}
		var openButtons = document.querySelectorAll( '[data-search-modal-open]' );
		var closeButtons = modal.querySelectorAll( '[data-search-modal-close]' );
		var input = document.getElementById( 'search-modal-input' );
		var sortSelect = document.getElementById( 'search-modal-sort' );
		var results = document.getElementById( 'search-modal-results' );
		var countEl = document.getElementById( 'search-modal-count' );
		var lastFocused = null;
		var debounceTimer = null;
		var activeRequest = null;

		function fetchResults() {
			if ( activeRequest ) {
				activeRequest.abort();
			}
			var controller = new AbortController();
			activeRequest = controller;

			results.setAttribute( 'aria-busy', 'true' );

			var body = new URLSearchParams( {
				action: 'solaris_search_products',
				nonce: solarisData.searchNonce,
				term: input.value,
				orderby: sortSelect.value,
			} );

			fetch( solarisData.ajaxUrl, {
				method: 'POST',
				body: body,
				signal: controller.signal,
			} )
				.then( function ( response ) {
					return response.json();
				} )
				.then( function ( response ) {
					if ( ! response || ! response.success ) {
						return;
					}
					results.innerHTML = response.data.html;
					countEl.textContent = response.data.count
						? response.data.count + ' ' + ( 1 === response.data.count ? 'product' : 'products' )
						: '';
					initWishlist(); // Re-bind favorite buttons on the freshly-inserted cards.
					observeReveals( results ); // Same reason: newly-inserted cards aren't observed yet.
					initLoopQuantity(); // Same reason: newly-inserted cards' qty inputs aren't bound yet.
				} )
				.catch( function ( error ) {
					if ( 'AbortError' !== error.name ) {
						results.innerHTML = '';
					}
				} )
				.finally( function () {
					results.removeAttribute( 'aria-busy' );
				} );
		}

		function open( trigger ) {
			lastFocused = trigger || document.activeElement;
			modal.setAttribute( 'data-open', 'true' );
			modal.setAttribute( 'aria-hidden', 'false' );
			document.body.classList.add( 'search-modal-open' );
			document.body.classList.remove( 'mobile-nav-open' );
			var mobileNav = document.getElementById( 'mobile-nav' );
			if ( mobileNav ) {
				mobileNav.setAttribute( 'data-open', 'false' );
				mobileNav.setAttribute( 'aria-hidden', 'true' );
			}
			openButtons.forEach( function ( btn ) {
				btn.setAttribute( 'aria-expanded', 'true' );
			} );
			window.setTimeout( function () {
				input.focus();
			}, 50 );
			fetchResults();
		}

		function close() {
			modal.setAttribute( 'data-open', 'false' );
			modal.setAttribute( 'aria-hidden', 'true' );
			document.body.classList.remove( 'search-modal-open' );
			openButtons.forEach( function ( btn ) {
				btn.setAttribute( 'aria-expanded', 'false' );
			} );
			if ( lastFocused && typeof lastFocused.focus === 'function' ) {
				lastFocused.focus();
			}
		}

		openButtons.forEach( function ( btn ) {
			btn.addEventListener( 'click', function () {
				open( btn );
			} );
		} );
		closeButtons.forEach( function ( btn ) {
			btn.addEventListener( 'click', close );
		} );
		document.addEventListener( 'keydown', function ( e ) {
			if ( 'Escape' === e.key && 'true' === modal.getAttribute( 'data-open' ) ) {
				close();
			}
		} );
		input.addEventListener( 'input', function () {
			window.clearTimeout( debounceTimer );
			debounceTimer = window.setTimeout( fetchResults, 350 );
		} );
		sortSelect.addEventListener( 'change', fetchResults );
	}

	function initFaqAccordion() {
		document.querySelectorAll( '.faq-item' ).forEach( function ( item ) {
			var question = item.querySelector( '.faq-item__question' );
			var answer = item.querySelector( '.faq-item__answer' );
			if ( ! question || ! answer ) {
				return;
			}
			question.addEventListener( 'click', function () {
				var isOpen = item.getAttribute( 'data-open' ) === 'true';
				item.setAttribute( 'data-open', isOpen ? 'false' : 'true' );
				question.setAttribute( 'aria-expanded', isOpen ? 'false' : 'true' );
				answer.style.maxHeight = isOpen ? null : answer.scrollHeight + 'px';
			} );
		} );
	}

	/*
	 * A single shared IntersectionObserver, created once and reused by every
	 * call to observeReveals() below — not recreated per call.
	 */
	var revealObserver = null;
	var revealDisabled = false;

	function getRevealObserver() {
		if ( revealObserver || revealDisabled ) {
			return revealObserver;
		}
		if ( ! ( 'IntersectionObserver' in window ) ) {
			revealDisabled = true;
			return null;
		}
		revealObserver = new IntersectionObserver(
			function ( entries ) {
				entries.forEach( function ( entry ) {
					if ( entry.isIntersecting ) {
						entry.target.classList.add( 'is-visible' );
						revealObserver.unobserve( entry.target );
					}
				} );
			},
			{ threshold: 0.15 }
		);
		return revealObserver;
	}

	/**
	 * Registers every not-yet-seen .reveal element within `root` for the
	 * scroll-in animation. Called once at page load for the initial DOM, and
	 * again after the search modal injects new AJAX-loaded product cards —
	 * elements that don't exist yet when the page first loads never get
	 * registered otherwise, and stay permanently stuck at opacity:0 (that's
	 * exactly what was happening inside the search modal).
	 */
	function observeReveals( root ) {
		var items = ( root || document ).querySelectorAll( '.reveal:not(.is-visible)' );
		if ( ! items.length ) {
			return;
		}
		var observer = getRevealObserver();
		if ( ! observer ) {
			items.forEach( function ( item ) {
				item.classList.add( 'is-visible' );
			} );
			return;
		}
		items.forEach( function ( item ) {
			observer.observe( item );
		} );
	}

	function initScrollReveal() {
		observeReveals( document );
	}

	/**
	 * Global "back to top" button (markup in footer.php, present on every
	 * page). Shown once the visitor has scrolled past one viewport height.
	 */
	function initBackToTop() {
		var button = document.getElementById( 'back-to-top' );
		if ( ! button ) {
			return;
		}
		var threshold = window.innerHeight;

		function onScroll() {
			button.classList.toggle( 'is-visible', window.scrollY > threshold );
		}

		button.addEventListener( 'click', function () {
			window.scrollTo( {
				top: 0,
				behavior: 'smooth',
			} );
		} );

		window.addEventListener( 'scroll', onScroll, { passive: true } );
		onScroll();
	}

	/**
	 * Loop-card quantity input (inc/woocommerce.php, solaris_loop_quantity_input).
	 * Two problems to solve, not one:
	 *
	 * 1. It isn't inside a <form> — the AJAX "Add to cart" button reads its
	 *    quantity from its own data-quantity attribute (wc-add-to-cart.js does
	 *    `$thisbutton.data( 'quantity' ) || 1`), so keep that attribute synced.
	 *
	 * 2. WooCommerce's own hook order (content-product.php) only closes the
	 *    card's whole-image/title <a> at woocommerce_after_shop_loop_item
	 *    (priority 5) — one step AFTER woocommerce_after_shop_loop_item_title,
	 *    where price/quantity render. So .loop-qty sits INSIDE that <a>, same
	 *    as the wishlist button above it. A click anywhere in the number
	 *    input (typing, or its native spin buttons) still navigates to the
	 *    product page unless stopped — and stopPropagation() alone does NOT
	 *    stop it: an <a>'s "follow this link" is a default action gated on
	 *    event.defaultPrevented, evaluated against the click's original
	 *    target chain independent of whether propagation reached the
	 *    anchor's own listeners. Only preventDefault() cancels it (same
	 *    mechanism initWishlist's button relies on above). Confirmed this
	 *    doesn't interfere with the input's own typing/spin-button behavior.
	 *
	 * Bound per-card (like initWishlist above), not delegated on document,
	 * and re-run after the AJAX search modal injects new cards (see
	 * initSearchModal) so freshly-inserted cards' inputs get the same guard.
	 */
	function initLoopQuantity() {
		document.querySelectorAll( '.loop-qty' ).forEach( function ( wrap ) {
			if ( wrap.dataset.qtyBound ) {
				return;
			}
			wrap.dataset.qtyBound = 'true';

			wrap.addEventListener( 'click', function ( e ) {
				e.preventDefault();
			} );

			var input = wrap.querySelector( 'input.qty' );
			if ( ! input ) {
				return;
			}

			// assets/css/tooltip.css [tooltip] attr — set on the wrapper, not
			// the <input> itself: form controls are replaced elements, so
			// browsers never render ::before/::after generated content on
			// an <input> directly.
			wrap.setAttribute( 'tooltip', 'Quantity' );

			// Rendered as type="text" (inc/woocommerce.php,
			// solaris_loop_quantity_input_type) so this, not the browser's
			// number-input validation, is what actually keeps it digits-only
			// — 'input' fires for both typed keystrokes and pastes, so one
			// listener covers both.
			function sanitizeDigits() {
				var digits = input.value.replace( /[^0-9]/g, '' );
				if ( digits !== input.value ) {
					input.value = digits;
				}
			}

			function sync() {
				var card = wrap.closest( 'li.product, .product' );
				var button = card && card.querySelector( '.add_to_cart_button' );
				if ( ! button ) {
					return;
				}
				var min = parseInt( input.min, 10 );
				if ( isNaN( min ) ) {
					min = 1;
				}
				var value = parseInt( input.value, 10 );
				if ( isNaN( value ) || value < min ) {
					value = min;
				}
				button.setAttribute( 'data-quantity', value );
			}

			input.addEventListener( 'input', function () {
				sanitizeDigits();
				sync();
			} );
			input.addEventListener( 'change', function () {
				sanitizeDigits();
				sync();
			} );
			// The input's own starting value can already be > 1 — e.g.
			// "Default Selected Qty" (inc/woocommerce.php) — but the
			// add-to-cart link's data-quantity is rendered independently by
			// core WC, always starting at 1, and previously only got synced
			// by the listeners above, which need a user-driven change to
			// ever fire. Without this, clicking "Add to cart" without first
			// touching the quantity field always added 1, silently
			// ignoring whatever default was actually showing.
			sync();
		} );
	}

	/**
	 * +/- stepper buttons (inc/woocommerce.php, woocommerce_before/after_
	 * quantity_input_field) beside every quantity input site-wide — loop
	 * cards, Quick View, single product. Delegated on document.body instead
	 * of bound per-element, so it needs no re-bind step and already covers
	 * content injected later (search modal cards, the Quick View modal).
	 */
	function initQtySteppers() {
		document.body.addEventListener( 'click', function ( e ) {
			var button = e.target.closest( '.qty-step' );
			if ( ! button ) {
				return;
			}
			var wrap  = button.closest( '.quantity' );
			var input = wrap && wrap.querySelector( 'input.qty' );
			if ( ! input || input.readOnly || 'hidden' === input.type ) {
				return;
			}

			var step = parseFloat( input.step ) || 1;
			var min  = parseFloat( input.min );
			var max  = parseFloat( input.max );
			var value = parseFloat( input.value );
			if ( isNaN( value ) ) {
				value = isNaN( min ) ? 0 : min;
			}

			value += button.classList.contains( 'qty-step--plus' ) ? step : -step;
			if ( ! isNaN( min ) ) {
				value = Math.max( min, value );
			}
			if ( ! isNaN( max ) ) {
				value = Math.min( max, value );
			}

			input.value = value;
			// Native events, not jQuery .trigger() — every existing listener
			// on this input (initLoopQuantity's sync() above, WC's own
			// single-product/Quick View change handlers) is bound via
			// addEventListener/jQuery .on(), both of which fire on a real
			// dispatched Event same as a user-driven change.
			input.dispatchEvent( new Event( 'input', { bubbles: true } ) );
			input.dispatchEvent( new Event( 'change', { bubbles: true } ) );
		} );
	}

	/**
	 * Category sidebar (Customizer: Products List) collapse/expand — only
	 * visually a toggle below 900px (assets/css/woocommerce.css); above
	 * that width the list is unconditionally shown regardless of this
	 * class/attribute, so this can bind unconditionally with no viewport
	 * check of its own.
	 */
	function initShopCategoriesToggle() {
		var toggle = document.querySelector( '.shop-categories__title' );
		var nav = document.querySelector( '.shop-categories' );
		if ( ! toggle || ! nav ) {
			return;
		}
		toggle.addEventListener( 'click', function () {
			var expanded = 'true' === toggle.getAttribute( 'aria-expanded' );
			toggle.setAttribute( 'aria-expanded', String( ! expanded ) );
			nav.classList.toggle( 'is-expanded', ! expanded );
		} );
	}

	/**
	 * "Lazy Load"/"Load More" (Customizer: Products List → Products Loading
	 * Mode) — inc/ajax-shop-loading.php returns the next page of the shop
	 * archive as raw <li> product-card fragments, appended straight into the
	 * existing ul.products. Re-runs the same per-card init functions
	 * initSearchModal already relies on for the same reason: freshly
	 * inserted cards start with none of their own behavior bound yet.
	 * Only present at all (the .shop-loading control div) when Customizer:
	 * Products List → Products Loading Mode isn't "Pagination" — WC's own
	 * pagination is left completely alone in that case.
	 */
	function initShopLoading() {
		var control = document.querySelector( '.shop-loading' );
		var productsList = document.querySelector( '.shop-content ul.products' );
		if ( ! control || ! productsList || control.dataset.shopLoadingBound ) {
			return;
		}
		control.dataset.shopLoadingBound = 'true';

		var mode      = control.dataset.mode;
		var loading   = false;
		var observer  = null;

		function loadNextPage() {
			if ( loading ) {
				return;
			}
			var nextPage = parseInt( control.dataset.page, 10 ) + 1;
			var maxPages = parseInt( control.dataset.maxPages, 10 );
			if ( nextPage > maxPages ) {
				return;
			}

			loading = true;
			control.setAttribute( 'data-loading', 'true' );

			var body = new URLSearchParams( {
				action:  'solaris_load_shop_products',
				nonce:   control.dataset.nonce,
				page:    nextPage,
				orderby: control.dataset.orderby || '',
				tax:     control.dataset.tax || '',
				term:    control.dataset.term || '',
			} );

			fetch( solarisData.ajaxUrl, { method: 'POST', body: body } )
				.then( function ( response ) { return response.json(); } )
				.then( function ( response ) {
					if ( ! response || ! response.success || ! response.data ) {
						return;
					}
					productsList.insertAdjacentHTML( 'beforeend', response.data.html );
					control.dataset.page = response.data.page;
					initWishlist(); // Re-bind favorite buttons on the freshly-inserted cards.
					observeReveals( productsList ); // Same reason: newly-inserted cards aren't observed yet.
					initLoopQuantity(); // Same reason: newly-inserted cards' qty inputs aren't bound yet.

					if ( ! response.data.hasMore ) {
						if ( observer ) {
							observer.disconnect();
						}
						control.remove();
						return;
					}

					/*
					 * IntersectionObserver only re-fires on a visibility
					 * *crossing* — if a short/single-row append doesn't push
					 * the sentinel back out of the viewport (e.g. a low
					 * "Products Per Page" on a tall screen), it stays
					 * continuously intersecting and would otherwise never
					 * trigger again on its own. Re-check post-layout and
					 * keep loading while it's still in view.
					 */
					if ( observer ) {
						requestAnimationFrame( function () {
							if ( control.getBoundingClientRect().top < window.innerHeight ) {
								loadNextPage();
							}
						} );
					}
				} )
				.catch( function () {
					// Network error / non-JSON response — nothing to recover
					// beyond resetting the loading state below; the button
					// (Load More) or sentinel (Lazy Load) simply stays put
					// for the shopper to retry.
				} )
				.finally( function () {
					loading = false;
					control.removeAttribute( 'data-loading' );
				} );
		}

		if ( 'load_more' === mode ) {
			var button = control.querySelector( '.shop-loading__button' );
			if ( button ) {
				button.addEventListener( 'click', loadNextPage );
			}
		} else if ( 'lazy_load' === mode ) {
			observer = new IntersectionObserver( function ( entries ) {
				if ( entries[ 0 ].isIntersecting ) {
					loadNextPage();
				}
			} );
			observer.observe( control );
		}
	}

	/**
	 * "Fly to cart": on Add to cart, clone the card's product image, pin the
	 * clone at the image's own on-screen position/size (position:fixed, so
	 * scrolling doesn't move it mid-flight), then animate that clone toward
	 * the header cart icon while shrinking/fading it out. Purely decorative
	 * — the real cart update already happens via WooCommerce's own AJAX +
	 * fragments (inc/woocommerce.php); this never waits on that request, so
	 * the flight timing is identical whether the add ultimately succeeds or
	 * not. Delegated on document.body (not queried/bound once) so it also
	 * covers cards injected later by the AJAX search modal. Gated on the
	 * Customizer's "Show Animated Add to Cart?" toggle (Product Cards
	 * section), passed through as solarisData.flyToCartEnabled.
	 *
	 * "Allow Multiple for Multi Quantity?" (solarisData.flyToCartMultiEnabled)
	 * launches one clone per unit of quantity instead of always just one —
	 * quantity comes from the card's own data-quantity attribute (already
	 * kept in sync with the loop-qty input by initLoopQuantity above) —
	 * see launchFlyToCartFlights() below, shared with the single-product
	 * page's own add-to-cart (initSingleProductAjaxAddToCart further down).
	 */
	function initFlyToCart() {
		if ( typeof solarisData === 'undefined' || ! solarisData.flyToCartEnabled ) {
			return;
		}
		document.body.addEventListener( 'click', function ( e ) {
			var button = e.target.closest && e.target.closest( '.add_to_cart_button.ajax_add_to_cart' );
			if ( ! button ) {
				return;
			}
			var card = button.closest( 'li.product, .product' );
			var image = card && card.querySelector( '.woocommerce-loop-product__link img' );
			var cartIcon = getVisibleCartIcon();
			var quantity = parseInt( button.dataset.quantity, 10 ) || 1;
			launchFlyToCartFlights( image, cartIcon, quantity );
		} );
	}

	/**
	 * Shared by the shop-loop click handler above and the single-product
	 * page's AJAX add-to-cart (initSingleProductAjaxAddToCart) — launches
	 * one clone, or (with "Allow Multiple for Multi Quantity?" on) one per
	 * unit of quantity, capped at MAX_FLY_TO_CART_CLONES so an unusually
	 * large quantity doesn't spawn a wall of clones. Each launch starts
	 * from the same source-image position (not spread out) and is
	 * staggered by time only, reading as a cascade of clones one after
	 * another. No-ops if fly-to-cart itself is off, or if either element
	 * is missing.
	 */
	function launchFlyToCartFlights( image, cartIcon, quantity ) {
		if ( ! solarisData.flyToCartEnabled || ! image || ! cartIcon ) {
			return;
		}
		var MAX_FLY_TO_CART_CLONES = 10;
		var FLY_TO_CART_STAGGER_MS = 100;
		var flightCount = solarisData.flyToCartMultiEnabled ? Math.min( quantity, MAX_FLY_TO_CART_CLONES ) : 1;
		for ( var i = 0; i < flightCount; i++ ) {
			window.setTimeout( flyToCart.bind( null, image, cartIcon ), i * FLY_TO_CART_STAGGER_MS );
		}
	}

	function getVisibleCartIcon() {
		// header-actions.php is reused for both the desktop header and the
		// mobile off-canvas nav, so there can be two [data-cart-count] icons
		// in the DOM at once. The closed off-canvas one isn't display:none
		// (position:fixed; inset:0; transform:translateY(-100%) — see
		// .mobile-nav in header.css), so its rect still has real width/
		// height; only its position is off-screen. Checking that the rect
		// actually falls within the viewport (not just non-zero size) is
		// what tells the two apart.
		var links = document.querySelectorAll( '.header-icon-link' );
		for ( var i = 0; i < links.length; i++ ) {
			if ( ! links[ i ].querySelector( '[data-cart-count]' ) ) {
				continue;
			}
			var rect = links[ i ].getBoundingClientRect();
			var inViewport = rect.width > 0 && rect.height > 0 &&
				rect.top >= 0 && rect.top < window.innerHeight &&
				rect.left >= 0 && rect.left < window.innerWidth;
			if ( inViewport ) {
				return links[ i ];
			}
		}
		return null;
	}

	function flyToCart( image, cartIcon ) {
		flyToCartActiveCount++;

		var startRect = image.getBoundingClientRect();
		var endRect = cartIcon.getBoundingClientRect();

		// Multi-quantity clones (launchFlyToCartFlights) all start from the
		// exact same position — the product image itself — and are
		// staggered only in TIME (launchFlyToCartFlights' setTimeout delay
		// per index), not spatially offset. Each one flies the same path a
		// moment after the last, reading as a cascade of clones rather than
		// scattering them across the source image.
		var startLeft = startRect.left;
		var startTop = startRect.top;

		var clone = image.cloneNode( false );
		// Lazy-loaded images (assets/js is not what swaps src here — a
		// separate lazy-load script does) may not have swapped in the real
		// image yet if this fires early; fall back to its data-src so the
		// clone doesn't fly a blank placeholder.
		if ( image.dataset.src && /^data:/.test( image.currentSrc || image.src || '' ) ) {
			clone.src = image.dataset.src;
		}
		clone.removeAttribute( 'srcset' );
		clone.removeAttribute( 'sizes' );
		clone.className = 'fly-to-cart-clone';
		clone.style.width = startRect.width + 'px';
		clone.style.height = startRect.height + 'px';
		clone.style.left = startLeft + 'px';
		clone.style.top = startTop + 'px';
		// The clone's own border/background/shadow chrome (CSS, driven by
		// the "Cloned Image Background/Box Shadow" Customizer controls)
		// otherwise pops in instantly the moment this element exists — a
		// visible flash a frame before any motion starts. Overriding it
		// to invisible here, then clearing the override in the same rAF
		// the flight itself starts (below), makes it ease in instead,
		// via the border-color/background-color/box-shadow transitions
		// already on .fly-to-cart-clone.
		clone.style.borderColor = 'transparent';
		clone.style.backgroundColor = 'transparent';
		clone.style.boxShadow = 'none';
		document.body.appendChild( clone );

		var endX = endRect.left + endRect.width / 2 - ( startLeft + startRect.width / 2 );
		var endY = endRect.top + endRect.height / 2 - ( startTop + startRect.height / 2 );

		// Two rAFs: the first lets the browser paint the clone at its start
		// position (just appended); only then does changing transform/opacity
		// have a "from" state to transition away from instead of jumping.
		requestAnimationFrame( function () {
			requestAnimationFrame( function () {
				clone.style.transform = 'translate(' + endX + 'px, ' + endY + 'px) scale(0.1)';
				clone.style.opacity = '0.15';
				clone.style.borderColor = '';
				clone.style.backgroundColor = '';
				clone.style.boxShadow = '';
			} );
		} );

		clone.addEventListener( 'transitionend', function ( e ) {
			// .fly-to-cart-clone now transitions 5 properties (transform,
			// opacity, border-color, background-color, box-shadow) — this
			// fires once per property, not once total, and the 3 chrome-
			// fade-in ones finish at 0.2s, long before the 0.7s flight
			// itself (transform) does. Without this check, the FIRST one to
			// finish (border-color, at 0.2s) removed the clone while it was
			// still near its starting position — the flight cut short and
			// restarting almost immediately for the next click, reading as
			// broken/glitchy rather than a full smooth flight to the icon.
			if ( 'transform' !== e.propertyName ) {
				return;
			}
			clone.remove();
			bumpCartCount();
			flyToCartActiveCount--;
			// Only fire once ALL clones from this (possibly multi-quantity)
			// add-to-cart have actually landed, not just the first one.
			if ( flyToCartActiveCount <= 0 ) {
				flyToCartActiveCount = 0;
				document.dispatchEvent( new Event( 'solaris:fly-to-cart-done' ) );
			}
		} );
	}

	function bumpCartCount() {
		var badge = document.querySelector( '[data-cart-count]' );
		if ( ! badge ) {
			return;
		}
		// Restart the animation even if a previous bump is still mid-flight
		// (rapid successive add-to-carts) — remove, force reflow, re-add.
		badge.classList.remove( 'is-bumping' );
		void badge.offsetWidth;
		badge.classList.add( 'is-bumping' );
	}

	/**
	 * Header cart icon click target (Customizer: Product Cards → Cart Count
	 * From Header — Select Action). template-parts/components/cart-panel.php only
	 * renders #cart-panel when that setting isn't "Go to Cart Page" — when
	 * it's missing, this is a no-op and the icon's own href navigates
	 * normally, exactly matching the pre-feature behavior. The panel's own
	 * content (assets/css/cart-panel.css positions it per mode) is
	 * WooCommerce's own mini-cart markup, kept live by wc-cart-fragments
	 * (inc/enqueue.php) — nothing here touches cart contents.
	 *
	 * Body scroll is locked for every mode except "popup" — that one is a
	 * small flyout near the icon, not a full-screen takeover, so the rest
	 * of the page should stay usable/scrollable behind it.
	 */
	function initCartPanel() {
		var panel = document.getElementById( 'cart-panel' );
		if ( ! panel ) {
			return;
		}
		var triggers = [];
		document.querySelectorAll( '.header-icon-link' ).forEach( function ( link ) {
			if ( link.querySelector( '[data-cart-count]' ) ) {
				triggers.push( link );
			}
		} );
		var closeButtons = panel.querySelectorAll( '[data-cart-panel-close]' );
		var lastFocused = null;

		function open( trigger ) {
			lastFocused = trigger || document.activeElement;
			/*
			 * Popup mode's box carries a CSS arrow (cart-panel.css) pointing
			 * at whichever header cart icon is actually on screen — desktop
			 * and mobile-nav both render one (see getVisibleCartIcon), and
			 * this can also open with no trigger element at all (the
			 * add-to-cart auto-open path below calls open() with none), so
			 * it's computed fresh here rather than from the click target.
			 * --cart-panel-arrow-x is relative to the box's OWN left edge
			 * (its containing block for that ::before), not the viewport —
			 * getBoundingClientRect() on both and subtracting gives that.
			 */
			if ( 'popup' === panel.dataset.cartPanelMode ) {
				var icon = getVisibleCartIcon();
				var box = panel.querySelector( '.cart-panel__box' );
				if ( icon && box ) {
					var iconRect = icon.getBoundingClientRect();
					var boxRect = box.getBoundingClientRect();
					panel.style.setProperty( '--cart-panel-arrow-x', ( iconRect.left + iconRect.width / 2 - boxRect.left ) + 'px' );
				}
			}
			panel.setAttribute( 'data-open', 'true' );
			panel.setAttribute( 'aria-hidden', 'false' );
			if ( 'popup' !== panel.dataset.cartPanelMode ) {
				document.body.classList.add( 'cart-panel-open' );
			}
		}

		function close() {
			panel.setAttribute( 'data-open', 'false' );
			panel.setAttribute( 'aria-hidden', 'true' );
			document.body.classList.remove( 'cart-panel-open' );
			if ( lastFocused && typeof lastFocused.focus === 'function' ) {
				lastFocused.focus();
			}
		}

		triggers.forEach( function ( trigger ) {
			trigger.addEventListener( 'click', function ( e ) {
				e.preventDefault();
				open( trigger );
			} );
		} );
		closeButtons.forEach( function ( btn ) {
			btn.addEventListener( 'click', close );
		} );
		document.addEventListener( 'keydown', function ( e ) {
			if ( 'Escape' === e.key && 'true' === panel.getAttribute( 'data-open' ) ) {
				close();
			}
		} );

		/*
		 * Customizer: Product Cards → "After Add to Cart, Open Minicart?"
		 * (default off). WooCommerce's add-to-cart.js fires this on
		 * document.body via jQuery's own event system after a successful
		 * AJAX add + fragment refresh — NOT a native DOM event, so a plain
		 * addEventListener never sees it (confirmed: only jQuery's own
		 * .on() catches it). jQuery is guaranteed present here since
		 * wc-add-to-cart.js itself depends on it.
		 *
		 * If fly-to-cart clone(s) are currently traveling
		 * (flyToCartActiveCount > 0 — possibly several at once, see
		 * "Allow Multiple for Multi Quantity?"), the AJAX request they're
		 * decoupled from typically resolves well before that ~0.7s
		 * animation does — opening immediately here would pop the panel up
		 * mid-flight, competing with the image(s) still visibly traveling
		 * toward the icon it's about to open. Wait for whichever finishes
		 * last instead.
		 */
		if ( solarisData.openCartPanelOnAddToCart && window.jQuery ) {
			var addToCartDone = false;
			window.jQuery( document.body ).on( 'added_to_cart', function () {
				if ( flyToCartActiveCount > 0 ) {
					addToCartDone = true;
				} else {
					open();
				}
			} );
			document.addEventListener( 'solaris:fly-to-cart-done', function () {
				if ( addToCartDone ) {
					open();
					addToCartDone = false;
				}
			} );
		}
	}

	/**
	 * Replaces each element matching a fragment selector with that
	 * fragment's HTML — the exact "apply AJAX add-to-cart fragments" step
	 * WooCommerce's own wc-add-to-cart.js does via jQuery for shop-loop
	 * buttons, reimplemented in plain DOM for initSingleProductAjaxAddToCart
	 * below (main.js otherwise has no jQuery dependency of its own). Used
	 * for both the .cart-count header badge and the cart panel's own
	 * .widget_shopping_cart_content (inc/woocommerce.php /
	 * template-parts/components/cart-panel.php).
	 */
	function applyFragments( fragments ) {
		if ( ! fragments ) {
			return;
		}
		Object.keys( fragments ).forEach( function ( selector ) {
			document.querySelectorAll( selector ).forEach( function ( el ) {
				var wrapper = document.createElement( 'div' );
				wrapper.innerHTML = fragments[ selector ];
				if ( wrapper.firstElementChild ) {
					el.replaceWith( wrapper.firstElementChild );
				}
			} );
		} );
	}

	/**
	 * Single-product page's own "Add to cart" — a real <form class="cart">
	 * submit, since WooCommerce core only AJAX-ifies shop-loop buttons, not
	 * this form. Finds the page's own form.cart and its gallery image, then
	 * hands off to bindAjaxAddToCartForm (shared with initQuickView, whose
	 * form is injected via AJAX rather than already on the page).
	 */
	function initSingleProductAjaxAddToCart() {
		var form = document.querySelector( 'form.cart' );
		if ( ! form ) {
			return;
		}
		bindAjaxAddToCartForm( form, function () {
			// .flex-active-slide is the gallery's flexslider marking whichever
			// image the shopper actually has selected/visible — without it
			// this always grabbed the first (default) image regardless of
			// which thumbnail was picked. Falls back to the first image if
			// the slider hasn't initialized for some reason.
			return document.querySelector( '.woocommerce-product-gallery__image.flex-active-slide img' )
				|| document.querySelector( '.woocommerce-product-gallery__image img' );
		} );
	}

	/**
	 * POSTs a product "form.cart" submit to WC_AJAX::add_to_cart()'s own
	 * endpoint (solarisData.wcAjaxAddToCartUrl, localized in inc/enqueue.php)
	 * instead of letting it submit/reload normally — the exact same handler
	 * the shop-loop's ajax_add_to_cart links already use, so the server-side
	 * logic (stock/purchasability validation, the cart itself) is 100% the
	 * same core code path, not reimplemented here. Wires up the same
	 * fly-to-cart animation + fragment refresh the loop gets. Shared by
	 * initSingleProductAjaxAddToCart (the real single-product page) and
	 * initQuickView (the same form, injected into the Quick View modal).
	 *
	 * @param {HTMLFormElement} form        The form.cart to bind.
	 * @param {Function}        resolveImage Called at submit time (so a
	 *   quick-view form bound before its own gallery image exists still
	 *   resolves it correctly) — returns the <img> to fly to the cart icon.
	 *
	 * Deliberately narrow: only handles the common "one product, one
	 * quantity" case (simple products, and variable products once a
	 * variation is selected — WooCommerce writes its id into the form's own
	 * hidden variation_id field). Grouped products render several quantity
	 * inputs (one per sub-product) and don't fit that shape, and external/
	 * affiliate products render no form.cart at all — both are left to
	 * their normal (non-AJAX) behavior rather than guessing at a fit.
	 */
	function bindAjaxAddToCartForm( form, resolveImage ) {
		if ( typeof solarisData === 'undefined' || ! solarisData.wcAjaxAddToCartUrl ) {
			return;
		}
		var quantityInputs = form.querySelectorAll( 'input.qty' );
		var addToCartField = form.querySelector( '[name="add-to-cart"]' );
		if ( ! addToCartField || 1 !== quantityInputs.length ) {
			return;
		}

		form.addEventListener( 'submit', function ( e ) {
			var variationInput = form.querySelector( 'input[name="variation_id"]' );
			var variationId = variationInput ? parseInt( variationInput.value, 10 ) : 0;
			var productId = variationId > 0 ? variationId : parseInt( addToCartField.value, 10 );
			if ( ! productId ) {
				return; // Unexpected shape — let it submit normally rather than silently eat the click.
			}
			e.preventDefault();

			var submitButton = form.querySelector( 'button[type="submit"], button[name="add-to-cart"]' );
			if ( submitButton ) {
				submitButton.classList.add( 'loading' );
				submitButton.disabled = true;
			}

			var quantity = parseInt( quantityInputs[ 0 ].value, 10 ) || 1;
			var image = resolveImage();
			var cartIcon = getVisibleCartIcon();

			// Same as the shop-loop's own click handler: fires immediately,
			// decoupled from the request below — the flight is decorative and
			// shouldn't wait on (or get cancelled by) network latency.
			launchFlyToCartFlights( image, cartIcon, quantity );

			fetch( solarisData.wcAjaxAddToCartUrl, {
				method: 'POST',
				body: new URLSearchParams( { product_id: productId, quantity: quantity } ),
			} )
				.then( function ( response ) { return response.json(); } )
				.then( function ( response ) {
					if ( ! response || response.error ) {
						if ( response && response.product_url ) {
							window.location = response.product_url; // Same redirect-to-show-notices core itself uses on failure.
						} else {
							form.submit();
						}
						return;
					}
					applyFragments( response.fragments );
					if ( window.jQuery ) {
						// Same event WooCommerce's own add-to-cart.js fires for
						// loop buttons — lets initCartPanel's "open after add to
						// cart" (and anything else listening for it) react here too.
						window.jQuery( document.body ).trigger( 'added_to_cart', [ response.fragments, response.cart_hash ] );
					}
				} )
				.catch( function () {
					form.submit(); // Network hiccup — don't silently eat the add-to-cart.
				} )
				.finally( function () {
					if ( submitButton ) {
						submitButton.classList.remove( 'loading' );
						submitButton.disabled = false;
					}
				} );
		} );
	}

	/**
	 * Product-card "Quick View" (Customizer: Product Cards → Show Quick View
	 * Button). Click on [data-quick-view] fetches that product's markup from
	 * inc/ajax-quick-view.php and injects it into the modal template-parts/
	 * components/quick-view-modal.php prints once in header.php — same
	 * open/close mechanics (data-open, backdrop, Escape) as initSearchModal/
	 * initCartPanel above. The injected form.cart is bound through the same
	 * bindAjaxAddToCartForm() the real single-product page uses, so adding
	 * to cart from Quick View behaves identically (no reload, fly-to-cart,
	 * fragment refresh) — it's the same form, just arrived via AJAX instead
	 * of already being on the page.
	 */
	function initQuickView() {
		var modal = document.getElementById( 'quick-view-modal' );
		if ( ! modal || typeof solarisData === 'undefined' ) {
			return;
		}
		var closeButtons = modal.querySelectorAll( '[data-quick-view-close]' );
		var body = document.getElementById( 'quick-view-modal-body' );
		var lastFocused = null;
		var activeRequest = null;

		function open( trigger ) {
			lastFocused = trigger || document.activeElement;
			modal.setAttribute( 'data-open', 'true' );
			modal.setAttribute( 'aria-hidden', 'false' );
			document.body.classList.add( 'quick-view-open' );
		}

		function close() {
			modal.setAttribute( 'data-open', 'false' );
			modal.setAttribute( 'aria-hidden', 'true' );
			document.body.classList.remove( 'quick-view-open' );
			if ( activeRequest ) {
				activeRequest.abort();
			}
			if ( lastFocused && typeof lastFocused.focus === 'function' ) {
				lastFocused.focus();
			}
		}

		document.body.addEventListener( 'click', function ( e ) {
			var trigger = e.target.closest && e.target.closest( '[data-quick-view]' );
			if ( ! trigger ) {
				return;
			}
			e.preventDefault();

			var productId = trigger.getAttribute( 'data-quick-view' );
			open( trigger );
			body.innerHTML = '<div class="quick-view-modal__spinner" aria-hidden="true"></div>';

			if ( activeRequest ) {
				activeRequest.abort();
			}
			var controller = new AbortController();
			activeRequest = controller;

			fetch( solarisData.ajaxUrl, {
				method: 'POST',
				body: new URLSearchParams( {
					action: 'solaris_quick_view',
					nonce: solarisData.quickViewNonce,
					product_id: productId,
				} ),
				signal: controller.signal,
			} )
				.then( function ( response ) { return response.json(); } )
				.then( function ( response ) {
					if ( ! response || ! response.success ) {
						body.innerHTML = '<p class="quick-view-product__error">' + ( solarisData.i18nQuickViewError || 'Sorry, this product could not be loaded.' ) + '</p>';
						return;
					}
					body.innerHTML = response.data.html;
					var form = body.querySelector( 'form.cart' );
					if ( form ) {
						bindAjaxAddToCartForm( form, function () {
							return body.querySelector( '.quick-view-product__gallery img' );
						} );
					}
				} )
				.catch( function ( error ) {
					if ( 'AbortError' !== error.name ) {
						body.innerHTML = '';
					}
				} );
		} );

		closeButtons.forEach( function ( btn ) {
			btn.addEventListener( 'click', close );
		} );
		document.addEventListener( 'keydown', function ( e ) {
			if ( 'Escape' === e.key && 'true' === modal.getAttribute( 'data-open' ) ) {
				close();
			}
		} );
	}
} )();
