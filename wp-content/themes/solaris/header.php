<?php
/**
 * @package Solaris
 */
$solaris_transparent = is_front_page() && solaris_get_option( 'header_transparent' );
?><!doctype html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<?php wp_head(); ?>
</head>
<body <?php body_class( $solaris_transparent ? 'solaris-transparent-header' : '' ); ?>>
<?php wp_body_open(); ?>


<?php
$solaris_header_classes = 'site-header';
$solaris_header_classes .= $solaris_transparent ? '' : ' no-transparent';
if ( solaris_get_option( 'header_bg_gradient_enabled' ) ) {
	/*
	 * Unconditional — not just "! $solaris_transparent". The gradient
	 * background-image applies to .is-stuck regardless of page (see
	 * header.css), including on the homepage once its transparent hero
	 * header becomes sticky on scroll. That state matches neither the
	 * ":not(.no-transparent):not(.is-stuck)" transparent-hero rule nor a
	 * plain ".no-transparent" — without this class there, homepage's
	 * scrolled header silently fell back to dark text on the gradient
	 * while every other page's scrolled header correctly showed white.
	 */
	$solaris_header_classes .= ' has-gradient-bg';
}
?>
<header id="masthead" class="<?php echo esc_attr( $solaris_header_classes ); ?>" data-sticky="<?php echo solaris_get_option( 'sticky_header' ) ? 'true' : 'false'; ?>">
	<div class="header-inner">
		<?php get_template_part( 'template-parts/header/site-branding' ); ?>

		<nav class="primary-navigation" aria-label="<?php esc_attr_e( 'Primary', 'solaris' ); ?>">
			<?php solaris_nav_menu( 'primary' ); ?>
		</nav>

		<div class="header-actions">
			<?php get_template_part( 'template-parts/header/header-actions' ); ?>
			<button class="menu-toggle" aria-expanded="false" aria-controls="mobile-nav" aria-label="<?php esc_attr_e( 'Open menu', 'solaris' ); ?>">
				<?php echo solaris_icon( 'menu' ); ?>
			</button>
		</div>
	</div>
</header>

<div id="mobile-nav" class="mobile-nav" data-open="false" aria-hidden="true">
	<div class="mobile-nav__header">
		<span class="site-branding__title"><?php bloginfo( 'name' ); ?></span>
		<button class="mobile-nav__close" aria-label="<?php esc_attr_e( 'Close menu', 'solaris' ); ?>">
			<?php echo solaris_icon( 'close' ); ?>
		</button>
	</div>
	<nav aria-label="<?php esc_attr_e( 'Mobile', 'solaris' ); ?>">
		<?php solaris_nav_menu( 'mobile' ); ?>
	</nav>
	<div class="header-actions">
		<?php get_template_part( 'template-parts/header/header-actions' ); ?>
	</div>
</div>

<?php get_template_part( 'template-parts/components/search-modal' ); ?>

<?php get_template_part( 'template-parts/components/cart-panel' ); ?>

<?php get_template_part( 'template-parts/components/quick-view-modal' ); ?>
