<?php
/**
 * Fallback archive template for non-product taxonomies/post types
 * (WooCommerce ships its own archive-product.php for the shop).
 *
 * @package Solaris
 */
get_header();
?>
<main id="main" class="container section" style="padding-top:32px">
	<?php solaris_breadcrumbs(); ?>
	<header class="section-header">
		<h1><?php the_archive_title(); ?></h1>
		<?php the_archive_description( '<div class="text-small">', '</div>' ); ?>
	</header>

	<?php if ( have_posts() ) : ?>
		<div class="grid grid-3">
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<?php get_template_part( 'template-parts/components/post-card' ); ?>
			<?php endwhile; ?>
		</div>
		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<div class="empty-state">
			<?php echo solaris_icon( 'sun' ); ?>
			<h2><?php esc_html_e( 'Nothing here yet', 'solaris' ); ?></h2>
		</div>
	<?php endif; ?>
</main>
<?php get_footer(); ?>
