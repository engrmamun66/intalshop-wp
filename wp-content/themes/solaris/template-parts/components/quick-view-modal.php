<?php
/**
 * Product-card "Quick View" modal shell (Customizer: Product Cards → Show
 * Quick View Button). Empty until a [data-quick-view] button is clicked —
 * main.js (initQuickView) fetches that product's markup from
 * inc/ajax-quick-view.php and injects it into #quick-view-modal-body.
 * Same open/close mechanics (data-open, backdrop, Escape) as search-modal.php
 * and cart-panel.php elsewhere in this theme.
 *
 * @package Solaris
 */
if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}
if ( ! solaris_get_option( 'quick_view_enabled' ) ) {
	return;
}
?>
<div class="quick-view-modal" id="quick-view-modal" data-open="false" aria-hidden="true">
	<div class="quick-view-modal__backdrop" data-quick-view-close></div>
	<div class="quick-view-modal__panel" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Quick view', 'solaris' ); ?>">
		<button type="button" class="quick-view-modal__close" data-quick-view-close aria-label="<?php esc_attr_e( 'Close quick view', 'solaris' ); ?>">
			<?php echo solaris_icon( 'close' ); ?>
		</button>
		<div class="quick-view-modal__body" id="quick-view-modal-body">
			<div class="quick-view-modal__spinner" aria-hidden="true"></div>
		</div>
	</div>
</div>
