<?php
/**
 * @package Solaris
 */
?>
	<footer id="colophon" class="site-footer">
		<?php
		$solaris_wave_style = solaris_get_option( 'footer_wave_style' );
		if ( solaris_get_option( 'footer_wave_enabled' ) ) :
			$solaris_wave_classes = array(
				'footer-wave',
				'footer-wave--' . $solaris_wave_style,
				'footer-wave--align-' . solaris_get_option( 'footer_wave_align' ),
				'footer-wave--size-' . solaris_get_option( 'footer_wave_size_mode' ),
			);
			?>
			<div class="<?php echo esc_attr( implode( ' ', $solaris_wave_classes ) ); ?>" aria-hidden="true">
				<?php get_template_part( 'template-parts/footer/wave-' . $solaris_wave_style ); ?>
			</div>
		<?php endif; ?>
		<div class="container">
			<div class="footer-widgets">
				<div class="footer-brand">
					<?php
					$footer_logo = solaris_get_option( 'footer_logo' );
					if ( $footer_logo ) {
						echo '<img src="' . esc_url( $footer_logo ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '" />';
					} elseif ( has_custom_logo() ) {
						the_custom_logo();
					} else {
						echo '<strong class="site-title">' . esc_html( get_bloginfo( 'name' ) ) . '</strong>';
					}
					$description = get_bloginfo( 'description' );
					?>
					<p><?php echo $description ? esc_html( $description ) : esc_html__( 'Reliable solar panels, inverters, batteries and accessories for homes and businesses.', 'solaris' ); ?></p>
					<?php
					$socials = solaris_social_links();
					if ( $socials ) :
						?>
						<div class="footer-social">
							<?php foreach ( $socials as $network => $url ) : ?>
								<a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( ucfirst( $network ) ); ?>">
									<?php echo solaris_icon( $network ); ?>
								</a>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
				<div><?php dynamic_sidebar( 'footer-widget-1' ); ?></div>
				<div><?php dynamic_sidebar( 'footer-widget-2' ); ?></div>
				<div><?php dynamic_sidebar( 'footer-widget-3' ); ?></div>
			</div>
		</div>

		<div class="container">
			<div class="footer-bottom">
				<p><?php echo wp_kses_post( solaris_copyright_text() ); ?></p>
				<?php if ( has_nav_menu( 'legal' ) ) : ?>
					<ul>
						<?php solaris_nav_menu( 'legal', array( 'container' => false, 'items_wrap' => '%3$s' ) ); ?>
					</ul>
				<?php endif; ?>
			</div>
		</div>
	</footer>

	<button type="button" id="back-to-top" class="back-to-top" aria-label="<?php esc_attr_e( 'Back to top', 'solaris' ); ?>">
		<?php echo solaris_icon( 'arrow-up' ); ?>
	</button>

<?php wp_footer(); ?>
</body>
</html>
