<?php
/**
 * Search toggle, account, cart and header CTA. Reused for both the desktop
 * header and the mobile off-canvas panel.
 *
 * @package Solaris
 */
$show_woo = class_exists( 'WooCommerce' );
?>
<button type="button" class="header-icon-link" aria-haspopup="dialog" aria-expanded="false" aria-controls="search-modal" aria-label="<?php esc_attr_e( 'Search products', 'solaris' ); ?>" data-search-modal-open>
	<?php echo solaris_icon( 'search' ); ?>
</button>

<?php if ( $show_woo && solaris_get_option( 'show_account_icon' ) ) : ?>
	<a class="header-icon-link" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" aria-label="<?php esc_attr_e( 'My Account', 'solaris' ); ?>">
		<?php echo solaris_icon( 'user' ); ?>
	</a>
<?php endif; ?>

<?php
if ( $show_woo && solaris_get_option( 'show_cart_icon' ) ) :
	// When a cart-panel mode is active (Customizer: Product Cards → Cart
	// Count From Header — Select Action), JS intercepts this click and
	// opens the panel instead of navigating — href="#" rather than the
	// real cart URL communicates
	// that (no dead link flash, no ?add-to-cart-style stray navigation if
	// JS fails to bind in time). "Go to Cart Page" (the default) keeps the
	// real URL since nothing intercepts the click there.
	$solaris_cart_href = 'page' === solaris_get_option( 'cart_click_action' ) ? wc_get_cart_url() : '#';
	?>
	<a class="header-icon-link" href="<?php echo esc_url( $solaris_cart_href ); ?>" aria-label="<?php esc_attr_e( 'View cart', 'solaris' ); ?>">
		<?php echo solaris_icon( 'cart' ); ?>
		<span class="cart-count" data-cart-count><?php echo esc_html( WC()->cart ? WC()->cart->get_cart_contents_count() : 0 ); ?></span>
	</a>
<?php endif; ?>

<?php
$cta_text = solaris_get_option( 'header_cta_text' );
if ( $cta_text ) :
	?>
	<a class="button btn--sm" href="<?php echo esc_url( solaris_cta_url( 'header_cta_url' ) ); ?>"><?php echo esc_html( $cta_text ); ?></a>
<?php endif; ?>
