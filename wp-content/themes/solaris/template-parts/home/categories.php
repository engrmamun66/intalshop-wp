<?php
/**
 * Real WooCommerce product-category cards (top-level, image, live product
 * count) — never fabricated category data.
 *
 * @package Solaris
 */
if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

$categories = get_terms(
	array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => false,
		'parent'     => 0,
		'number'     => 8,
		'exclude'    => array( get_option( 'default_product_cat' ) ),
	)
);

if ( empty( $categories ) || is_wp_error( $categories ) ) {
	return;
}
?>
<section class="section bg-surface" id="categories">
	<div class="container">
		<div class="section-header is-centered">
			<p class="eyebrow"><?php esc_html_e( 'Browse By Category', 'solaris' ); ?></p>
			<h2><?php esc_html_e( 'Everything You Need For Solar', 'solaris' ); ?></h2>
		</div>
		<div class="grid grid-4">
			<?php foreach ( $categories as $category ) : ?>
				<?php
				$thumbnail_id = get_term_meta( $category->term_id, 'thumbnail_id', true );
				$image_url    = $thumbnail_id ? wp_get_attachment_image_url( $thumbnail_id, 'solaris-category-card' ) : '';
				?>
				<a class="category-card reveal" href="<?php echo esc_url( get_term_link( $category ) ); ?>">
					<div class="category-card__media">
						<?php if ( $image_url ) : ?>
							<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $category->name ); ?>" loading="lazy" width="480" height="360" />
						<?php else : ?>
							<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:var(--gradient-light);color:var(--color-accent);">
								<?php echo solaris_icon( 'sun', array( 'class' => 'icon-lg' ) ); ?>
							</div>
						<?php endif; ?>
						<span class="category-card__count"><?php echo esc_html( $category->count ); ?> <?php esc_html_e( 'products', 'solaris' ); ?></span>
					</div>
					<div class="category-card__body">
						<h3><?php echo esc_html( $category->name ); ?></h3>
						<?php if ( $category->description ) : ?>
							<p><?php echo esc_html( wp_trim_words( $category->description, 12 ) ); ?></p>
						<?php endif; ?>
						<span class="btn--ghost"><?php esc_html_e( 'Shop Now', 'solaris' ); ?></span>
					</div>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
