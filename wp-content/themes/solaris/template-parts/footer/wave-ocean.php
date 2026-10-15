<?php
/**
 * "Ocean Waves" footer animation style — layered water wave with gooey
 * rising bubbles (feGaussianBlur + feColorMatrix "goo" filter fuses
 * overlapping circles into lava-lamp-style blobs). The wave path is 240
 * units wide inside a 120-wide viewBox — exactly two repeats of the same
 * shape — so animating it -100% of its own width loops seamlessly.
 *
 * @package Solaris
 */
?>
<svg class="footer-wave__svg" viewBox="0 0 120 28" preserveAspectRatio="none">
	<defs>
		<filter id="solaris-footer-goo">
			<feGaussianBlur in="SourceGraphic" stdDeviation="1" result="solaris-footer-blur" />
			<feColorMatrix in="solaris-footer-blur" type="matrix" values="
				1 0 0 0 0
				0 1 0 0 0
				0 0 1 0 0
				0 0 0 13 -9" result="solaris-footer-goo" />
		</filter>
		<path id="solaris-footer-wave-path" d="M 0,10 C 30,10 30,15 60,15 90,15 90,10 120,10 150,10 150,15 180,15 210,15 210,10 240,10 v 28 h -240 z" />
	</defs>

	<use class="footer-wave__layer footer-wave__layer--3" xlink:href="#solaris-footer-wave-path" x="0" y="-2"></use>
	<use class="footer-wave__layer footer-wave__layer--2" xlink:href="#solaris-footer-wave-path" x="0" y="0"></use>

	<g class="footer-wave__goo" filter="url(#solaris-footer-goo)" transform="translate(50,0)">
		<circle class="footer-wave__drop footer-wave__drop--1" cx="1.2" cy="5.4" r="8.8" />
		<circle class="footer-wave__drop footer-wave__drop--2" cx="5.2" cy="5.1" r="7.5" />
		<circle class="footer-wave__drop footer-wave__drop--3" cx="10.2" cy="5.3" r="9.2" />
		<circle class="footer-wave__drop footer-wave__drop--4" cx="3.2" cy="5.4" r="8.8" />
		<circle class="footer-wave__drop footer-wave__drop--5" cx="14.2" cy="5.1" r="7.5" />
		<circle class="footer-wave__drop footer-wave__drop--6" cx="17.2" cy="4.8" r="9.2" />
		<use class="footer-wave__layer footer-wave__layer--1" xlink:href="#solaris-footer-wave-path" x="-50" y="1"></use>
	</g>
</svg>
