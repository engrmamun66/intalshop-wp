<?php
/**
 * Template Name: Blog
 * Also auto-applies to any page with the slug "blog" (the site's Posts page).
 *
 * The main query on a Page is the page itself, not the post archive, so
 * this runs its own WP_Query for published posts rather than relying on
 * have_posts()/the_post() against the global query.
 *
 * @package Solaris
 */
get_header();

$paged = max( 1, get_query_var( 'paged' ), get_query_var( 'page' ) );

$blog_query = new WP_Query(
	array(
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => 9,
		'paged'          => $paged,
	)
);
?>
<main id="main" class="container section" style="padding-top:32px">
	<?php solaris_breadcrumbs(); ?>
	<header class="section-header">
		<h1><?php the_title(); ?></h1>
	</header>

	<?php if ( $blog_query->have_posts() ) : ?>
		<div class="grid grid-3">
			<?php
			while ( $blog_query->have_posts() ) :
				$blog_query->the_post();
				?>
				<article <?php post_class( 'feature-card' ); ?>>
					<?php if ( has_post_thumbnail() ) : ?>
						<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'medium_large' ); ?></a>
					<?php endif; ?>
					<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
					<?php solaris_excerpt( 16 ); ?>
				</article>
			<?php endwhile; ?>
		</div>
		<nav class="blog-pagination" aria-label="<?php esc_attr_e( 'Blog pages', 'solaris' ); ?>">
			<?php
			echo paginate_links(
				array(
					'total'     => $blog_query->max_num_pages,
					'current'   => $paged,
					'base'      => get_pagenum_link( 1 ) . '%_%',
					'format'    => 'page/%#%/',
					'prev_text' => solaris_icon( 'arrow-right', array( 'class' => 'blog-pagination__arrow blog-pagination__arrow--prev' ) ),
					'next_text' => solaris_icon( 'arrow-right', array( 'class' => 'blog-pagination__arrow' ) ),
				)
			);
			?>
		</nav>
	<?php else : ?>
		<div class="empty-state">
			<?php echo solaris_icon( 'sun' ); ?>
			<h2><?php esc_html_e( 'Nothing here yet', 'solaris' ); ?></h2>
			<p><?php esc_html_e( 'Check back soon for solar tips, guides, and news.', 'solaris' ); ?></p>
		</div>
	<?php endif; ?>
	<?php wp_reset_postdata(); ?>
</main>
<?php get_footer(); ?>
