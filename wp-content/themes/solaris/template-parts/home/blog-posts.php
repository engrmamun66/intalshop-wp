<?php
/**
 * Latest blog posts, filtered by the categories picked in Customizer →
 * Blog Posts Section. Post count, heading, and the "Show All" button
 * (text + URL) are all Customizer-driven — see inc/customizer.php and
 * solaris_get_homepage_posts() / solaris_get_homepage_blog_url().
 *
 * @package Solaris
 */
if ( ! solaris_get_option( 'home_blog_enabled' ) ) {
	return;
}

$blog_query = solaris_get_homepage_posts();
if ( ! $blog_query->have_posts() ) {
	return;
}

$eyebrow     = solaris_get_option( 'home_blog_eyebrow' );
$heading     = solaris_get_option( 'home_blog_heading' );
$button_text = solaris_get_option( 'home_blog_button_text' );
?>
<section class="section home-blog-section" id="blog-posts">
	<div class="container">
		<div class="section-header is-centered">
			<?php if ( $eyebrow ) : ?>
				<p class="eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
			<?php endif; ?>
			<?php if ( $heading ) : ?>
				<h2><?php echo esc_html( $heading ); ?></h2>
			<?php endif; ?>
		</div>
		<div class="grid grid-3">
			<?php
			while ( $blog_query->have_posts() ) :
				$blog_query->the_post();
				?>
				<?php get_template_part( 'template-parts/components/post-card' ); ?>
			<?php endwhile; ?>
			<?php wp_reset_postdata(); ?>
		</div>
		<?php if ( solaris_get_option( 'home_blog_show_button' ) && $button_text ) : ?>
			<div class="hero-actions" style="justify-content:center;margin-top:40px;">
				<a class="button btn--outline" href="<?php echo esc_url( solaris_get_homepage_blog_url() ); ?>"><?php echo esc_html( $button_text ); ?></a>
			</div>
		<?php endif; ?>
	</div>
</section>
