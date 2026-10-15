<?php
/**
 * Homepage: hero -> stats -> blog posts -> categories -> featured products -> why choose us
 * -> solar solutions -> promo banner -> benefits -> testimonials -> FAQ -> final CTA.
 * Each section is its own template-part so any one of them can be reordered,
 * removed, or reused elsewhere without touching this file.
 *
 * @package Solaris
 */
get_header();
?>
<main id="main">
	<?php
	get_template_part( 'template-parts/home/hero' );
	get_template_part( 'template-parts/home/stats' );
	get_template_part( 'template-parts/home/blog-posts' );
	get_template_part( 'template-parts/home/categories' );
	get_template_part( 'template-parts/home/featured-products' );
	get_template_part( 'template-parts/home/why-choose-us' );
	get_template_part( 'template-parts/home/solutions' );
	get_template_part( 'template-parts/home/promo-banner' );
	get_template_part( 'template-parts/home/benefits' );
	get_template_part( 'template-parts/home/testimonials' );
	get_template_part( 'template-parts/home/faq' );
	get_template_part( 'template-parts/home/final-cta' );
	?>
</main>
<?php get_footer(); ?>
