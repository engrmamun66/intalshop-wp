<?php
/**
 * Custom Customizer control: a hero-slides repeater. WordPress ships no
 * native multi-image/repeater control, so this is a genuine
 * WP_Customize_Control subclass — the admin UI (add/remove/reorder/active
 * toggle) lives in assets/js/customizer-hero-slides.js, this class just
 * renders the container markup and a hidden input holding the JSON value.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Customize_Control' ) ) {
	return;
}

class Solaris_Hero_Slides_Control extends WP_Customize_Control {
	public $type = 'solaris_hero_slides';

	public function enqueue() {
		wp_enqueue_media();
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script(
			'solaris-customizer-hero-slides',
			SOLARIS_URI . '/assets/js/customizer-hero-slides.js',
			array( 'jquery', 'jquery-ui-sortable', 'customize-controls' ),
			solaris_asset_version( '/assets/js/customizer-hero-slides.js' ),
			true
		);
	}

	public function render_content() {
		?>
		<label>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php if ( $this->description ) : ?>
				<span class="description customize-control-description"><?php echo wp_kses_post( $this->description ); ?></span>
			<?php endif; ?>
		</label>
		<div class="solaris-hero-slides" data-setting="<?php echo esc_attr( $this->id ); ?>">
			<ul class="solaris-hero-slides__list"></ul>
			<button type="button" class="button solaris-hero-slides__add"><?php esc_html_e( 'Add Slide(s)', 'solaris' ); ?></button>
			<input
				type="hidden"
				class="solaris-hero-slides__value"
				<?php $this->link(); ?>
				value="<?php echo esc_attr( $this->value() ); ?>"
			/>
		</div>
		<?php
	}
}

/**
 * FAQ items repeater — question/answer text pairs, add/remove/reorder.
 * Same "one hidden JSON input, JS keeps it in sync" shape as
 * Solaris_Hero_Slides_Control above, but simpler: no media picker, and
 * both fields are short enough to stay always-visible instead of needing
 * a collapsed/expanded details toggle.
 */
class Solaris_Faq_Items_Control extends WP_Customize_Control {
	public $type = 'solaris_faq_items';

	public function enqueue() {
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script(
			'solaris-customizer-faq-items',
			SOLARIS_URI . '/assets/js/customizer-faq-items.js',
			array( 'jquery', 'jquery-ui-sortable', 'customize-controls' ),
			solaris_asset_version( '/assets/js/customizer-faq-items.js' ),
			true
		);
	}

	public function render_content() {
		?>
		<label>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php if ( $this->description ) : ?>
				<span class="description customize-control-description"><?php echo wp_kses_post( $this->description ); ?></span>
			<?php endif; ?>
		</label>
		<div class="solaris-faq-items" data-setting="<?php echo esc_attr( $this->id ); ?>">
			<ul class="solaris-faq-items__list"></ul>
			<button type="button" class="button solaris-faq-items__add"><?php esc_html_e( 'Add Question', 'solaris' ); ?></button>
			<input
				type="hidden"
				class="solaris-faq-items__value"
				<?php $this->link(); ?>
				value="<?php echo esc_attr( $this->value() ); ?>"
			/>
		</div>
		<?php
	}
}

/**
 * Two number inputs side by side, sharing one control — for a "from/to"
 * range (e.g. Fighter-Z's random next-travel delay) where two separate
 * add_control() calls would each render as their own full-width row and
 * stack vertically. Bound to two independently-registered settings passed
 * as this control's 'settings' => array( 'min' => ..., 'max' => ... ), the
 * native WP_Customize_Control way to link more than one setting to a single
 * control (value()/get_link() both take that setting key as their arg).
 */
class Solaris_Range_Control extends WP_Customize_Control {
	public $type = 'solaris_range';

	public $min_label = '';
	public $max_label = '';

	// Not $input_attrs / input_attrs() — WP_Customize_Control already
	// declares both a public property and a public method by that exact
	// name (for its own single-input min/max/step rendering); redeclaring
	// the method as private here is a fatal "must be public" error, since
	// PHP won't let a subclass narrow a parent method's visibility.
	public $range_input_attrs = array();

	public function render_content() {
		?>
		<label>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php if ( $this->description ) : ?>
				<span class="description customize-control-description"><?php echo wp_kses_post( $this->description ); ?></span>
			<?php endif; ?>
		</label>
		<div class="solaris-range-control">
			<label class="solaris-range-control__field">
				<span class="solaris-range-control__label"><?php echo esc_html( $this->min_label ?: __( 'From', 'solaris' ) ); ?></span>
				<input
					type="number"
					<?php $this->render_range_input_attrs( 'min' ); ?>
					<?php $this->link( 'min' ); ?>
					value="<?php echo esc_attr( $this->value( 'min' ) ); ?>"
				/>
			</label>
			<label class="solaris-range-control__field">
				<span class="solaris-range-control__label"><?php echo esc_html( $this->max_label ?: __( 'To', 'solaris' ) ); ?></span>
				<input
					type="number"
					<?php $this->render_range_input_attrs( 'max' ); ?>
					<?php $this->link( 'max' ); ?>
					value="<?php echo esc_attr( $this->value( 'max' ) ); ?>"
				/>
			</label>
		</div>
		<?php
	}

	private function render_range_input_attrs( $key ) {
		$attrs = $this->range_input_attrs[ $key ] ?? array();
		foreach ( $attrs as $attr => $value ) {
			echo esc_attr( $attr ) . '="' . esc_attr( $value ) . '" ';
		}
	}
}

/**
 * Checkbox list of every published page, plus a synthetic "Home / Front
 * Page" entry (id -1 — the front page isn't always a real page in
 * get_pages(), e.g. when Settings > Reading shows latest posts instead of a
 * static page). Value is a JSON array of the checked IDs, kept in sync by
 * assets/js/customizer-page-checklist.js into one hidden input, the same
 * "checkboxes drive one hidden JSON field" shape as the hero-slides control.
 */
class Solaris_Page_Checklist_Control extends WP_Customize_Control {
	public $type = 'solaris_page_checklist';

	public function enqueue() {
		wp_enqueue_script(
			'solaris-customizer-page-checklist',
			SOLARIS_URI . '/assets/js/customizer-page-checklist.js',
			array( 'customize-controls' ),
			solaris_asset_version( '/assets/js/customizer-page-checklist.js' ),
			true
		);
	}

	public function render_content() {
		$pages    = get_pages( array( 'sort_column' => 'menu_order', 'sort_order' => 'ASC' ) );
		$selected = json_decode( $this->value(), true );
		if ( ! is_array( $selected ) ) {
			$selected = array();
		}
		?>
		<label>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php if ( $this->description ) : ?>
				<span class="description customize-control-description"><?php echo wp_kses_post( $this->description ); ?></span>
			<?php endif; ?>
		</label>
		<div class="solaris-page-checklist" data-setting="<?php echo esc_attr( $this->id ); ?>">
			<label class="solaris-page-checklist__item">
				<input type="checkbox" value="-1" <?php checked( in_array( -1, $selected, true ) ); ?> />
				<?php esc_html_e( 'Home / Front Page', 'solaris' ); ?>
			</label>
			<?php foreach ( $pages as $page ) : ?>
				<label class="solaris-page-checklist__item">
					<input type="checkbox" value="<?php echo esc_attr( $page->ID ); ?>" <?php checked( in_array( $page->ID, $selected, true ) ); ?> />
					<?php echo esc_html( $page->post_title ); ?>
				</label>
			<?php endforeach; ?>
			<input
				type="hidden"
				class="solaris-page-checklist__value"
				<?php $this->link(); ?>
				value="<?php echo esc_attr( $this->value() ); ?>"
			/>
		</div>
		<?php
	}
}

/**
 * Checkbox list of post categories (homepage blog section). Same markup and
 * hidden-JSON-input shape as Solaris_Page_Checklist_Control, so it reuses
 * assets/js/customizer-page-checklist.js and its styles unchanged.
 */
class Solaris_Category_Checklist_Control extends WP_Customize_Control {
	public $type = 'solaris_category_checklist';

	public function enqueue() {
		wp_enqueue_script(
			'solaris-customizer-page-checklist',
			SOLARIS_URI . '/assets/js/customizer-page-checklist.js',
			array( 'customize-controls' ),
			solaris_asset_version( '/assets/js/customizer-page-checklist.js' ),
			true
		);
	}

	public function render_content() {
		$categories = get_categories( array( 'hide_empty' => false ) );
		$selected   = json_decode( $this->value(), true );
		if ( ! is_array( $selected ) ) {
			$selected = array();
		}
		?>
		<label>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php if ( $this->description ) : ?>
				<span class="description customize-control-description"><?php echo wp_kses_post( $this->description ); ?></span>
			<?php endif; ?>
		</label>
		<div class="solaris-page-checklist" data-setting="<?php echo esc_attr( $this->id ); ?>">
			<?php foreach ( $categories as $category ) : ?>
				<label class="solaris-page-checklist__item">
					<input type="checkbox" value="<?php echo esc_attr( $category->term_id ); ?>" <?php checked( in_array( $category->term_id, $selected, true ) ); ?> />
					<?php echo esc_html( $category->name ); ?> <span class="description">(<?php echo esc_html( $category->count ); ?>)</span>
				</label>
			<?php endforeach; ?>
			<input
				type="hidden"
				class="solaris-page-checklist__value"
				<?php $this->link(); ?>
				value="<?php echo esc_attr( $this->value() ); ?>"
			/>
		</div>
		<?php
	}
}
