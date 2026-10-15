<?php
/**
 * Fallback template for any request WordPress can't match to a more
 * specific template file.
 *
 * @package Solaris
 */
get_header();
?>
<main id="main" class="container section">
	<?php if ( have_posts() ) : ?>
		<div class="grid grid-2">
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<article <?php post_class(); ?>>
					<?php if ( has_post_thumbnail() ) : ?>
						<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'medium_large' ); ?></a>
					<?php endif; ?>
					<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<?php solaris_excerpt(); ?>
				</article>
			<?php endwhile; ?>
		</div>
		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<div class="empty-state">
			<?php echo solaris_icon( 'sun' ); ?>
			<h2><?php esc_html_e( 'Nothing here yet', 'solaris' ); ?></h2>
			<p><?php esc_html_e( 'Try browsing our solar product catalog instead.', 'solaris' ); ?></p>
			<div class="hero-actions"><a class="button" href="<?php echo esc_url( solaris_shop_url() ); ?>"><?php esc_html_e( 'Shop Solar Products', 'solaris' ); ?></a></div>
		</div>
	<?php endif; ?>
</main>
<?php get_footer(); ?>
