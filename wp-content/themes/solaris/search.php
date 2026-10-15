<?php
/**
 * Search results template. WooCommerce product results share this template
 * (WooCommerce filters ul.products markup automatically for product hits).
 *
 * @package Solaris
 */
get_header();
?>
<main id="main" class="container section" style="padding-top:32px">
	<header class="section-header">
		<h1>
			<?php
			printf(
				/* translators: %s: search query. */
				esc_html__( 'Search results for: %s', 'solaris' ),
				'<span>' . esc_html( get_search_query() ) . '</span>'
			);
			?>
		</h1>
	</header>

	<?php if ( have_posts() ) : ?>
		<?php if ( class_exists( 'WooCommerce' ) && 'product' === get_query_var( 'post_type' ) ) : ?>
			<ul class="products">
				<?php while ( have_posts() ) : the_post(); ?>
					<?php wc_get_template_part( 'content', 'product' ); ?>
				<?php endwhile; ?>
			</ul>
		<?php else : ?>
			<div class="grid grid-2">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<article <?php post_class(); ?>>
						<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<?php solaris_excerpt(); ?>
					</article>
				<?php endwhile; ?>
			</div>
		<?php endif; ?>
		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<div class="empty-state">
			<?php echo solaris_icon( 'search' ); ?>
			<h2><?php esc_html_e( 'No results found', 'solaris' ); ?></h2>
			<p><?php esc_html_e( 'Try another search or browse our popular categories.', 'solaris' ); ?></p>
			<div class="hero-actions"><a class="button" href="<?php echo esc_url( solaris_shop_url() ); ?>"><?php esc_html_e( 'Browse All Products', 'solaris' ); ?></a></div>
		</div>
	<?php endif; ?>
</main>
<?php get_footer(); ?>
