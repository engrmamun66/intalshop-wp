<?php
/**
 * "Gentle Wave" footer animation style — a single smooth SVG wave path,
 * doubled in width so translateX(-100%) of its own bounding box loops
 * seamlessly. The calmest of the three styles.
 *
 * @package Solaris
 */
?>
<svg class="footer-wave__svg" viewBox="0 0 1200 60" preserveAspectRatio="none">
	<path class="footer-wave__gentle-path" d="M0,30 C300,60 300,0 600,30 C900,60 900,0 1200,30 C1500,60 1500,0 1800,30 C2100,60 2100,0 2400,30 V60 H0 Z" />
</svg>
