<?php
/**
 * @package Solaris
 */
get_header();
?>
<main id="main" class="container section" style="padding-top:32px;min-height:60vh;">
	<div class="empty-state">
		<?php echo solaris_icon( 'sun' ); ?>
		<h1><?php esc_html_e( 'Looks like this page drifted off the grid.', 'solaris' ); ?></h1>
		<p><?php esc_html_e( "The page you're looking for doesn't exist or has moved. Let's get you back on track.", 'solaris' ); ?></p>
		<div class="hero-actions">
			<a class="button" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back Home', 'solaris' ); ?></a>
			<a class="button btn--outline" href="<?php echo esc_url( solaris_shop_url() ); ?>"><?php esc_html_e( 'Shop Solar Products', 'solaris' ); ?></a>
		</div>
		<form role="search" method="get" style="max-width:360px;margin:32px auto 0;" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<label for="404-search" class="screen-reader-text"><?php esc_html_e( 'Search', 'solaris' ); ?></label>
			<input type="search" id="404-search" name="s" autocomplete="off" placeholder="<?php esc_attr_e( 'Search the site…', 'solaris' ); ?>" />
		</form>
	</div>
</main>
<?php get_footer(); ?>
