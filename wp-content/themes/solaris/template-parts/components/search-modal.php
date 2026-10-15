<?php
/**
 * Full-screen product search modal. Populated entirely via AJAX
 * (inc/ajax-search.php) — the initial "all products" listing loads the
 * moment the modal opens, then re-fetches on every keystroke (debounced)
 * and whenever the sort order changes.
 *
 * @package Solaris
 */
if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}
?>
<div class="search-modal" id="search-modal" data-open="false" aria-hidden="true">
	<div class="search-modal__backdrop" data-search-modal-close></div>
	<div class="search-modal__panel" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Search products', 'solaris' ); ?>">
		<div class="search-modal__bar">
			<div class="search-modal__field">
				<?php echo solaris_icon( 'search' ); ?>
				<label for="search-modal-input" class="screen-reader-text"><?php esc_html_e( 'Search products', 'solaris' ); ?></label>
				<input type="search" id="search-modal-input" placeholder="<?php esc_attr_e( 'Search products…', 'solaris' ); ?>" autocomplete="off" />
			</div>
			<label for="search-modal-sort" class="screen-reader-text"><?php esc_html_e( 'Sort by', 'solaris' ); ?></label>
			<select id="search-modal-sort">
				<option value="date"><?php esc_html_e( 'Latest', 'solaris' ); ?></option>
				<option value="menu_order"><?php esc_html_e( 'Default sorting', 'solaris' ); ?></option>
				<option value="price"><?php esc_html_e( 'Price: Low to High', 'solaris' ); ?></option>
				<option value="price-desc"><?php esc_html_e( 'Price: High to Low', 'solaris' ); ?></option>
				<option value="popularity"><?php esc_html_e( 'Best Selling', 'solaris' ); ?></option>
				<option value="rating"><?php esc_html_e( 'Top Rated', 'solaris' ); ?></option>
			</select>
			<button type="button" class="search-modal__close" data-search-modal-close aria-label="<?php esc_attr_e( 'Close search', 'solaris' ); ?>">
				<?php echo solaris_icon( 'close' ); ?>
			</button>
		</div>
		<div class="search-modal__meta">
			<span id="search-modal-count"></span>
		</div>
		<div class="search-modal__results" id="search-modal-results" aria-live="polite">
			<div class="search-modal__spinner" aria-hidden="true"></div>
		</div>
	</div>
</div>
