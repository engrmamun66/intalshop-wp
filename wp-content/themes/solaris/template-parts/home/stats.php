<?php
/**
 * Trust / statistics strip. Values are Customizer-editable placeholders —
 * flagged clearly so they get swapped for verified figures before launch.
 *
 * @package Solaris
 */
?>
<section class="stats-section">
	<div class="container">
		<div class="stats-grid">
			<?php for ( $i = 1; $i <= 4; $i++ ) : ?>
				<div class="stat-item reveal">
					<div class="stat-value"><?php echo esc_html( solaris_get_option( "stat_{$i}_value" ) ); ?></div>
					<div class="stat-label"><?php echo esc_html( solaris_get_option( "stat_{$i}_label" ) ); ?></div>
				</div>
			<?php endfor; ?>
		</div>
	</div>
</section>
