<?php
/**
 * Header cart icon click target (Customizer: Product Cards → Cart Count
 * From Header — Select Action). Only rendered when that setting isn't the default "Go to
 * Cart Page" — the header cart icon's own href already handles that case,
 * nothing to override. Cart contents route entirely through WooCommerce's
 * own woocommerce_mini_cart() (the same template the core Cart widget uses)
 * wrapped in the exact ".widget_shopping_cart_content" markup WooCommerce's
 * AJAX fragments (add/remove-from-cart) already know to refresh — see
 * inc/enqueue.php for the wc-cart-fragments enqueue that wires that up.
 *
 * @package Solaris
 */
if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}
$solaris_cart_action = solaris_get_option( 'cart_click_action' );
if ( 'page' === $solaris_cart_action ) {
	return;
}
?>
<?php
$solaris_cart_panel_classes = 'cart-panel';
if ( solaris_get_option( 'sticky_cart_buttons' ) ) {
	$solaris_cart_panel_classes .= ' cart-panel--sticky-buttons';
}
?>
<div class="<?php echo esc_attr( $solaris_cart_panel_classes ); ?>" id="cart-panel" data-open="false" data-cart-panel-mode="<?php echo esc_attr( $solaris_cart_action ); ?>" aria-hidden="true">
	<div class="cart-panel__backdrop" data-cart-panel-close></div>
	<div class="cart-panel__box" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Your cart', 'solaris' ); ?>">
		<div class="cart-panel__header">
			<h2><?php esc_html_e( 'Your Cart', 'solaris' ); ?></h2>
			<button type="button" class="cart-panel__close" data-cart-panel-close aria-label="<?php esc_attr_e( 'Close cart', 'solaris' ); ?>">
				<?php echo solaris_icon( 'close' ); ?>
			</button>
		</div>
		<div class="cart-panel__body">
			<div class="widget_shopping_cart_content">
				<?php woocommerce_mini_cart(); ?>
			</div>
		</div>
	</div>
</div>
