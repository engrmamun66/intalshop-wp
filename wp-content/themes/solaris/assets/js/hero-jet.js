/*
 * Drives the "Fly Fighter-Z" hero decoration (see inc/template-functions.php
 * solaris_render_hero_jet() for the markup, assets/css/hero-jet.css for the
 * keyframes/visuals). Two things pure CSS can't do on its own:
 *
 * 1. A genuinely random direction EACH flyby (not just once per page load) —
 *    CSS animation-iteration-count just repeats the same keyframe, so this
 *    picks ltr/rtl fresh after every pass and re-triggers the animation.
 * 2. The engine sound — plays a real uploaded audio file (Customizer:
 *    Fighter-Z Sound) while the plane is in flight, only starting on the
 *    visitor's own click (autoplay-with-sound is blocked by browsers
 *    anyway, so a manual control is required here, not just a nice-to-have).
 *    Falls back to a synthesized engine drone (Web Audio noise + a low
 *    oscillator) when no sound file has been uploaded. Either way, position
 *    is tracked to pan/scale volume left-to-right as the jet crosses.
 */
(function () {
	function ready( fn ) {
		if ( document.readyState !== 'loading' ) {
			fn();
		} else {
			document.addEventListener( 'DOMContentLoaded', fn );
		}
	}

	ready( function () {
		// Deliberately runs regardless of prefers-reduced-motion — see the
		// note above .hero-jet-layer in hero-jet.css.
		var layer = document.querySelector( '.hero-jet-layer' );
		if ( ! layer ) {
			return;
		}
		var jet         = layer.querySelector( '.hero-jet' );
		var soundToggle = layer.querySelector( '.hero-jet-sound-toggle' );
		if ( ! jet ) {
			return;
		}

		function log() {
			var args = [ '[Fighter-Z]' ].concat( Array.prototype.slice.call( arguments ) );
			// console.log.apply( console, args );
		}

		var jetSpeed         = window.solarisHeroJet || {};
		var DURATION         = jetSpeed.duration || 5;
		var SOUND_URL        = jetSpeed.soundUrl || '';
		var MIN_PAUSE        = jetSpeed.pauseMinMs || 4000;
		var MAX_PAUSE        = jetSpeed.pauseMaxMs || 11000;
		var VOLUME_MULTIPLIER = jetSpeed.volumeMultiplier || 0.75;

		log( 'init — soundToggle found:', !! soundToggle, '| SOUND_URL:', SOUND_URL || '(none — will use synthesized engine drone)' );

		var SOUND_PREF_KEY = 'solarisFighterZSoundEnabled';

		// localStorage can throw (private browsing, disabled storage, some
		// extensions) — never let a read/write failure break the feature,
		// just fall through to the in-memory default.
		function loadSoundPreference() {
			try {
				var stored = window.localStorage.getItem( SOUND_PREF_KEY );
				if ( stored === null ) {
					return true; // Never set before — enabled by default.
				}
				return stored === '1';
			} catch ( err ) {
				log( 'localStorage read failed, defaulting to enabled:', err );
				return true;
			}
		}

		function saveSoundPreference( enabled ) {
			try {
				window.localStorage.setItem( SOUND_PREF_KEY, enabled ? '1' : '0' );
			} catch ( err ) {
				log( 'localStorage write failed (preference won\'t persist):', err );
			}
		}

		var audioCtx        = null;
		var engineNodes     = null;
		var realAudioEl     = null;
		var soundEnabled    = loadSoundPreference(); // Defaults to on — see startAudioEngine() for why actual playback still waits on a gesture.
		var isFlying        = false;
		var rafId           = null;
		var lastLoggedVisible = null;

		log( 'loaded sound preference from localStorage → soundEnabled:', soundEnabled );

		// The button is server-rendered assuming the default "on" state —
		// correct it now if the visitor had previously muted it.
		if ( soundToggle && ! soundEnabled ) {
			soundToggle.classList.remove( 'is-playing' );
			soundToggle.setAttribute( 'aria-pressed', 'false' );
			soundToggle.setAttribute( 'aria-label', 'Play fighter jet engine sound' );
		}

		function rand( min, max ) {
			return Math.random() * ( max - min ) + min;
		}

		function ensureAudio() {
			if ( audioCtx ) {
				log( 'ensureAudio: already set up, audioCtx.state =', audioCtx.state );
				return;
			}
			var Ctx = window.AudioContext || window.webkitAudioContext;
			if ( ! Ctx ) {
				log( 'ensureAudio: no AudioContext support in this browser — aborting.' );
				return;
			}
			audioCtx = new Ctx();
			log( 'ensureAudio: AudioContext created, initial state =', audioCtx.state );

			var masterGain = audioCtx.createGain();
			masterGain.gain.value = 0;
			var panner = audioCtx.createStereoPanner ? audioCtx.createStereoPanner() : null;

			if ( panner ) {
				masterGain.connect( panner );
				panner.connect( audioCtx.destination );
			} else {
				masterGain.connect( audioCtx.destination );
			}

			if ( SOUND_URL ) {
				log( 'ensureAudio: using uploaded file:', SOUND_URL );
				// No crossOrigin attribute: the uploaded file is always
				// same-origin (Media Library), and forcing CORS mode on a
				// same-origin request has no benefit — only risk, on hosts
				// that don't send an Access-Control-Allow-Origin header.
				realAudioEl = new Audio( SOUND_URL );
				realAudioEl.loop = true;
				realAudioEl.addEventListener( 'error', function () {
					// A play()-promise rejection is a DIFFERENT failure path
					// from this — this fires for network/decode failures
					// (wrong MIME, 404, corrupt file, unsupported codec),
					// which play().catch() alone would never see.
					var mediaError = realAudioEl.error;
					log(
						'AUDIO ELEMENT ERROR — code:', mediaError && mediaError.code,
						'(1=ABORTED 2=NETWORK 3=DECODE 4=SRC_NOT_SUPPORTED)',
						'| src:', realAudioEl.currentSrc
					);
				} );
				realAudioEl.addEventListener( 'canplay', function () {
					log( 'audio element fired "canplay" — file is decodable and ready.' );
				} );
				realAudioEl.addEventListener( 'loadedmetadata', function () {
					log( 'audio element fired "loadedmetadata", duration =', realAudioEl.duration );
				} );
				realAudioEl.addEventListener( 'play', function () {
					log( 'audio element fired "play" event (playback actually started).' );
				} );
				realAudioEl.addEventListener( 'pause', function () {
					log( 'audio element fired "pause" event.' );
				} );

				var source = audioCtx.createMediaElementSource( realAudioEl );
				source.connect( masterGain );
				engineNodes = { masterGain: masterGain, panner: panner };
				return;
			}

			log( 'ensureAudio: no Fighter-Z Sound uploaded — using synthesized engine drone.' );

			var bufferSize   = 2 * audioCtx.sampleRate;
			var noiseBuffer  = audioCtx.createBuffer( 1, bufferSize, audioCtx.sampleRate );
			var data         = noiseBuffer.getChannelData( 0 );
			for ( var i = 0; i < bufferSize; i++ ) {
				data[ i ] = Math.random() * 2 - 1;
			}

			var noise = audioCtx.createBufferSource();
			noise.buffer = noiseBuffer;
			noise.loop = true;

			var noiseFilter = audioCtx.createBiquadFilter();
			noiseFilter.type = 'bandpass';
			noiseFilter.frequency.value = 900;
			noiseFilter.Q.value = 0.7;

			var drone = audioCtx.createOscillator();
			drone.type = 'sawtooth';
			drone.frequency.value = 90;

			var droneGain = audioCtx.createGain();
			droneGain.gain.value = 0.15;

			noise.connect( noiseFilter );
			noiseFilter.connect( masterGain );
			drone.connect( droneGain );
			droneGain.connect( masterGain );

			noise.start();
			drone.start();

			engineNodes = { noiseFilter: noiseFilter, drone: drone, masterGain: masterGain, panner: panner };
		}

		function setEngineLevel( level, pan, pitch ) {
			if ( ! engineNodes || ! audioCtx ) {
				return;
			}
			var now = audioCtx.currentTime;
			engineNodes.masterGain.gain.cancelScheduledValues( now );
			engineNodes.masterGain.gain.linearRampToValueAtTime( level, now + 0.08 );
			if ( engineNodes.panner ) {
				engineNodes.panner.pan.linearRampToValueAtTime( pan, now + 0.08 );
			}
			// Only the synthesized fallback has pitch-bearing nodes to ramp —
			// a real uploaded audio file plays back at its own fixed pitch.
			if ( engineNodes.noiseFilter ) {
				engineNodes.noiseFilter.frequency.linearRampToValueAtTime( 700 + pitch * 500, now + 0.08 );
			}
			if ( engineNodes.drone ) {
				engineNodes.drone.frequency.linearRampToValueAtTime( 80 + pitch * 40, now + 0.08 );
			}
		}

		var FADE_OUT_LINGER_MS = 600;  // Keep playing at full volume this long past "no longer visible" before fading.
		var FADE_OUT_SECONDS   = 1.5;  // Then ease the volume down to 0 over this long, instead of cutting instantly.
		var fadeOutLingerTimer = null;
		var fadeOutPauseTimer  = null;

		function cancelFadeOut() {
			if ( fadeOutLingerTimer ) {
				clearTimeout( fadeOutLingerTimer );
				fadeOutLingerTimer = null;
			}
			if ( fadeOutPauseTimer ) {
				clearTimeout( fadeOutPauseTimer );
				fadeOutPauseTimer = null;
			}
		}

		/*
		 * Was: an instant cut the moment the jet stopped being visible/
		 * flying (setEngineLevel(0,...) ramps over a mere 80ms, plus an
		 * immediate .pause() on top of that) — audible as an abrupt drop-out
		 * right at the edge. This keeps the sound at full volume a little
		 * past that point (FADE_OUT_LINGER_MS), then eases the gain down to
		 * 0 over FADE_OUT_SECONDS, and only pauses the underlying element
		 * once that fade has actually finished.
		 */
		function fadeOutAndStop() {
			if ( fadeOutLingerTimer || fadeOutPauseTimer ) {
				return; // Already fading out — don't restart the clock.
			}
			fadeOutLingerTimer = setTimeout( function () {
				fadeOutLingerTimer = null;
				if ( engineNodes && audioCtx ) {
					var now = audioCtx.currentTime;
					engineNodes.masterGain.gain.cancelScheduledValues( now );
					engineNodes.masterGain.gain.setValueAtTime( engineNodes.masterGain.gain.value, now );
					engineNodes.masterGain.gain.linearRampToValueAtTime( 0, now + FADE_OUT_SECONDS );
				}
				fadeOutPauseTimer = setTimeout( function () {
					fadeOutPauseTimer = null;
					if ( realAudioEl && ! realAudioEl.paused ) {
						log( 'fade-out complete — pausing realAudioEl.' );
						realAudioEl.pause();
					}
				}, FADE_OUT_SECONDS * 1000 );
			}, FADE_OUT_LINGER_MS );
		}

		/*
		 * Real audio's play/pause is driven by actual on-screen visibility
		 * here, not by the coarser isFlying flag (set for the animation's
		 * whole lifetime) — the R-to-L pass travels 200px past the left
		 * edge before animationend fires (see hero-jet-fly-rtl), so relying
		 * on isFlying alone kept the sound running for a moment after the
		 * jet had already left the visible area. This checks the box's own
		 * bounding rect every frame instead, so sound starts exactly when
		 * it comes into view and stops exactly when it leaves, in either
		 * direction and regardless of that overshoot.
		 */
		function trackPosition() {
			if ( soundEnabled && isFlying ) {
				var rect    = jet.getBoundingClientRect();
				var vw      = window.innerWidth || document.documentElement.clientWidth;
				var center  = rect.left + rect.width / 2;
				var visible = rect.right > 0 && rect.left < vw;

				if ( visible !== lastLoggedVisible ) {
					log(
						'visibility changed → visible:', visible,
						'| rect.left:', Math.round( rect.left ), 'rect.right:', Math.round( rect.right ), 'viewport width:', vw
					);
					lastLoggedVisible = visible;
				}

				if ( visible ) {
					cancelFadeOut(); // Came back into view (or a fade-out hadn't finished yet) — stay at full volume.

					if ( realAudioEl && realAudioEl.paused ) {
						log( 'attempting realAudioEl.play() — audioCtx.state:', audioCtx && audioCtx.state );
						// play() returns a rejectable promise — silently
						// leaving that unhandled makes a genuine autoplay-
						// policy or decode failure look identical to "no
						// bug, just hasn't started yet," so log it instead.
						realAudioEl.play().then( function () {
							log( 'realAudioEl.play() promise resolved.' );
						} ).catch( function ( err ) {
							log( 'realAudioEl.play() promise REJECTED:', err && err.name, err && err.message );
						} );
					}

					if ( engineNodes ) {
						var t = Math.max( 0, Math.min( 1, center / vw ) );
						var edgeDistance = Math.abs( t - 0.5 ) * 2;
						setEngineLevel( VOLUME_MULTIPLIER * ( 1 - edgeDistance * 0.6 ), ( t - 0.5 ) * 2, 1 - edgeDistance );
					}
				} else {
					fadeOutAndStop();
				}
			} else {
				fadeOutAndStop();
			}
			rafId = requestAnimationFrame( trackPosition );
		}

		/*
		 * Sound defaults to "on" (soundEnabled starts true, the button
		 * renders already in its is-playing state) — but browsers still
		 * block audio-with-sound until the visitor has interacted with the
		 * page at all, no matter what the code wants. This satisfies that
		 * with the visitor's very FIRST click/tap/keypress ANYWHERE on the
		 * page, not just on the sound button itself, so in practice almost
		 * no one ever needs to find or touch that button — clicking a nav
		 * link, a product, anywhere, is enough to unlock it.
		 */
		function startAudioEngine() {
			ensureAudio();
			log( 'startAudioEngine — audioCtx.state:', audioCtx && audioCtx.state );
			if ( audioCtx && audioCtx.state === 'suspended' ) {
				audioCtx.resume().then( function () {
					log( 'audioCtx.resume() resolved — state now:', audioCtx.state );
				} ).catch( function ( err ) {
					log( 'audioCtx.resume() REJECTED:', err );
				} );
			}
			if ( ! rafId ) {
				rafId = requestAnimationFrame( trackPosition );
			}
		}

		if ( soundEnabled ) {
			var firstGestureEvents = [ 'click', 'touchstart', 'keydown' ];
			var onFirstGesture = function () {
				firstGestureEvents.forEach( function ( evt ) {
					document.removeEventListener( evt, onFirstGesture );
				} );
				if ( soundEnabled ) {
					log( 'first page interaction detected — unlocking audio.' );
					startAudioEngine();
				}
			};
			firstGestureEvents.forEach( function ( evt ) {
				document.addEventListener( evt, onFirstGesture, { passive: true } );
			} );
		}

		if ( soundToggle ) {
			log( 'sound toggle button found and listener attached.' );
			soundToggle.addEventListener( 'click', function () {
				soundEnabled = ! soundEnabled;
				saveSoundPreference( soundEnabled );
				log( 'toggle clicked → soundEnabled:', soundEnabled, '| isFlying:', isFlying );
				soundToggle.classList.toggle( 'is-playing', soundEnabled );
				soundToggle.setAttribute( 'aria-pressed', soundEnabled ? 'true' : 'false' );
				soundToggle.setAttribute(
					'aria-label',
					soundEnabled ? 'Mute fighter jet engine sound' : 'Play fighter jet engine sound'
				);

				if ( soundEnabled ) {
					startAudioEngine();
				} else {
					cancelFadeOut(); // Manual mute is immediate, not a fade — this is only stopping a leftover timer.
					if ( realAudioEl ) {
						realAudioEl.pause();
					}
					if ( engineNodes ) {
						setEngineLevel( 0, 0, 0 );
					}
					if ( rafId ) {
						cancelAnimationFrame( rafId );
						rafId = null;
					}
				}
			} );
		}

		function flyOnce() {
			var dir = Math.random() < 0.5 ? 'ltr' : 'rtl';

			jet.classList.toggle( 'is-facing-left', dir === 'rtl' );
			jet.style.animation = 'none';
			void jet.offsetWidth; // Force reflow so the next line restarts the animation from scratch.
			jet.style.animation = 'hero-jet-fly-' + dir + ' ' + DURATION + 's linear 1 forwards';

			isFlying = true;
			cancelFadeOut(); // In case the previous pass's fade-out timer is still pending.
			log( 'flight started, direction:', dir, '| duration:', DURATION, 's | soundEnabled:', soundEnabled );
			lastLoggedVisible = null; // Force a fresh visibility log for this pass.
			if ( realAudioEl ) {
				realAudioEl.currentTime = 0;
			}

			jet.addEventListener( 'animationend', function onEnd() {
				jet.removeEventListener( 'animationend', onEnd );
				isFlying = false;
				log( 'flight ended.' );
				// trackPosition()'s own isFlying check pauses realAudioEl on
				// the very next frame — no need to do it here too.
				// animation-fill-mode:forwards holds the last frame (fully
				// opaque, parked at whichever edge) until this runs — without
				// it the jet sits frozen and visible in the sky for the
				// whole pause, not just mid-flight.
				jet.style.animation = 'none';
				setTimeout( flyOnce, rand( MIN_PAUSE, MAX_PAUSE ) );
			} );
		}

		setTimeout( flyOnce, rand( 500, 3000 ) );
	} );
})();
