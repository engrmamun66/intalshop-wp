<?php
/**
 * Single post template — solar blog articles.
 *
 * @package Solaris
 */
get_header();

while ( have_posts() ) :
	the_post();

	$categories   = get_the_category();
	$primary_cat  = $categories ? $categories[0] : null;
	$word_count   = str_word_count( wp_strip_all_tags( get_the_content() ) );
	$reading_mins = max( 1, (int) ceil( $word_count / 200 ) );
	$thumb_url    = has_post_thumbnail() ? get_the_post_thumbnail_url( get_the_ID(), 'full' ) : '';
	?>
	<main id="main">
	<article <?php post_class( 'solaris-page blog-post' ); ?>>
		<header
			class="blog-hero bg-dark"
			<?php echo $thumb_url ? 'style="background-image:url(' . esc_url( $thumb_url ) . ')"' : ''; ?>
		>
			<div class="blog-hero__overlay"></div>
			<div class="container blog-hero__inner">
				<div class="blog-hero__breadcrumb"><?php solaris_breadcrumbs(); ?></div>
				<?php if ( $primary_cat ) : ?>
					<a class="eyebrow" href="<?php echo esc_url( get_category_link( $primary_cat ) ); ?>">
						<?php echo esc_html( $primary_cat->name ); ?>
					</a>
				<?php endif; ?>
				<h1 class="blog-hero__title"><?php the_title(); ?></h1>
				<div class="blog-hero__meta">
					<span><?php echo esc_html( get_the_date() ); ?></span>
					<span class="blog-hero__meta-sep" aria-hidden="true">&bull;</span>
					<span><?php echo solaris_icon( 'clock', array( 'class' => 'blog-hero__meta-icon' ) ); ?><?php echo esc_html( $reading_mins ); ?> min read</span>
				</div>
			</div>
		</header>

		<div class="container section blog-post__body">
			<div class="solaris-page__content blog-content">
				<?php the_content(); ?>
			</div>
			<?php
			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}
			?>
		</div>
	</article>

	<?php
	if ( $primary_cat ) {
		$related = new WP_Query(
			array(
				'post_type'           => 'post',
				'post_status'         => 'publish',
				'posts_per_page'      => 3,
				'post__not_in'        => array( get_the_ID() ),
				'category__in'        => array( $primary_cat->term_id ),
				'ignore_sticky_posts' => true,
			)
		);
		if ( $related->have_posts() ) :
			?>
			<div class="container section blog-related">
				<header class="section-header">
					<p class="eyebrow"><?php esc_html_e( 'Keep Reading', 'solaris' ); ?></p>
					<h2><?php esc_html_e( 'More from the blog', 'solaris' ); ?></h2>
				</header>
				<div class="grid grid-3">
					<?php
					while ( $related->have_posts() ) :
						$related->the_post();
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
			</div>
			<?php
		endif;
		wp_reset_postdata();
	}
	?>
	</main>
<?php endwhile; ?>
<?php get_footer(); ?>
