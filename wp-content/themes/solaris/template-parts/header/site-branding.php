<?php
/**
 * Logo / site title, left side of the header.
 *
 * @package Solaris
 */
?>
<div class="site-branding">
	<?php
	if ( has_custom_logo() ) {
		the_custom_logo();
	} else {
		?>
		<p class="site-title">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>
		</p>
		<?php
	}
	?>
</div>
