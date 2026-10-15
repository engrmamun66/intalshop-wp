<?php
/**
 * Blog post card (featured image, category, date, title, excerpt). Must be
 * called inside a loop — used by the homepage blog section
 * (template-parts/home/blog-posts.php) and archive.php. Styles: .post-card
 * in assets/css/home.css (loaded site-wide).
 *
 * @package Solaris
 */
$category = get_the_category();
?>
<article <?php post_class( 'post-card reveal' ); ?>>
	<a class="post-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'medium_large', array( 'loading' => 'lazy' ) ); ?>
		<?php else : ?>
			<span class="post-card__placeholder"><?php echo solaris_icon( 'sun', array( 'class' => 'icon-lg' ) ); ?></span>
		<?php endif; ?>
	</a>
	<div class="post-card__body">
		<p class="post-card__meta">
			<?php if ( $category ) : ?>
				<span class="post-card__category"><?php echo esc_html( $category[0]->name ); ?></span>
			<?php endif; ?>
			<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
		</p>
		<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<p class="post-card__excerpt"><?php solaris_excerpt( 18 ); ?></p>
		<a class="btn--ghost" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read More', 'solaris' ); ?></a>
	</div>
</article>
