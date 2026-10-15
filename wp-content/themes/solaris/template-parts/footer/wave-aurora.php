<?php
/**
 * "Aurora Ripple" footer animation style — pure CSS, no SVG. Three
 * differently-sized ellipses (NOT circles — width and height must differ)
 * rotate at different speeds/directions inside a clipped strip; a rotating
 * ellipse sweeps its curved edge through the clip line as it spins, which
 * is what creates the undulating motion (a symmetric circle would look
 * static). The cheapest of the three styles — no SVG parsing/filter cost.
 *
 * Wrapped in .footer-wave__aurora-wrap so Size Mode can pin this crest to
 * one edge of a full-height wrapper (Full Height mode) the same way the
 * other two styles' <svg> does, without changing how the blobs themselves
 * center within it.
 *
 * @package Solaris
 */
?>
<div class="footer-wave__aurora-wrap">
	<span class="footer-wave__aurora-blob footer-wave__aurora-blob--1"></span>
	<span class="footer-wave__aurora-blob footer-wave__aurora-blob--2"></span>
	<span class="footer-wave__aurora-blob footer-wave__aurora-blob--3"></span>
</div>
