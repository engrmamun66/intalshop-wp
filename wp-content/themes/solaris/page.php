<?php
/**
 * Generic page template.
 *
 * @package Solaris
 */
get_header();
?>
<main id="main" class="container section" style="padding-top:32px">
	<?php solaris_breadcrumbs(); ?>
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article <?php post_class( 'solaris-page' ); ?>>
			<h1><?php the_title(); ?></h1>
			<div class="solaris-page__content">
				<?php the_content(); ?>
			</div>
		</article>
	<?php endwhile; ?>
</main>
<?php get_footer(); ?>
