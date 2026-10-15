<?php
/**
 * WordPress Customizer: colors, typography, header, footer, homepage content.
 * Every color/typography control maps 1:1 to a CSS custom property consumed
 * by assets/css/variables.css, so admin changes propagate through the whole
 * design system without per-component overrides.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WordPress blocks .svg uploads by default (SVG is XML and can carry a
 * <script>/onload payload, so core treats it as unsafe generally). The
 * Fighter-Z Image control below explicitly asks for SVG support, and only
 * an admin (the "edit_theme_options" capability the Customizer itself
 * requires) can use it — someone who can already upload plugin/theme files
 * has full code execution regardless, so this doesn't hand out a new
 * privilege, just a file type. Not sanitizing the SVG's contents here: that
 * would need a dedicated parser (WordPress core doesn't ship one), and is
 * out of scope for a single-purpose Customizer image field.
 */
add_filter( 'upload_mimes', function ( $mimes ) {
	$mimes['svg'] = 'image/svg+xml';
	return $mimes;
} );

function solaris_customizer_defaults() {
	return array_merge( solaris_section_overlay_defaults(), array(
		// Colors.
		'color_primary'        => '#30dd8a',
		'color_secondary'      => '#30dd8a',
		'color_accent'         => '#2bb673',
		'color_text'           => '#17231e',
		'color_heading'        => '#0b3d2e',
		'color_background'     => '#f4fbf7',
		'header_background'    => '#ffffff',
		'header_text_color'    => '#17231e',
		'footer_background'    => '#071c15',
		'footer_text_color'    => '#cfe9db',
		'footer_heading_color' => '#ffffff',
		'footer_accent_color'  => '#30dd8a',
		'button_text_color'    => '#ffffff',
		'btn_gradient_start'   => '#0ba360',
		'btn_gradient_mid'     => '#3cba92',
		'btn_gradient_end'     => '#2bb673',

		// Typography.
		'heading_font'  => 'Space Grotesk',
		'body_font'     => 'Inter',
		'base_font_size' => 16,
		'heading_scale' => 1.25,
		'line_height'   => 1.65,

		// Header.
		'header_height'        => 88,
		'header_transparent'   => true,
		'sticky_header'        => true,
		'header_cta_text'      => __( 'Shop Now', 'solaris' ),
		'header_cta_url'       => '',
		'show_cart_icon'       => true,
		'cart_click_action'    => 'page',
		'open_cart_panel_on_add_to_cart' => false,
		'sticky_cart_buttons'  => true,
		'show_account_icon'    => false,
		'header_bg_gradient_enabled' => true,
		'header_gradient_start'      => '#11a181',
		'header_gradient_end'        => '#36b88d',
		'header_gradient_fade'       => true,
		'header_gradient_angle'      => 90,

		// Footer.
		'footer_logo'               => '',
		'footer_logo_width'         => 160,
		'footer_logo_height'        => 40,
		'footer_logo_width_mobile'  => 120,
		'footer_logo_height_mobile' => 30,
		'footer_wave_enabled'       => true,
		'footer_wave_style'         => 'gentle',
		'footer_wave_color'         => '',
		'footer_wave_speed'         => 6,
		'footer_wave_align'         => 'top',
		'footer_wave_size_mode'     => 'full',
		'copyright_text'            => sprintf( __( '&copy; %s Solaris Solar. All rights reserved.', 'solaris' ), '{{year}}' ),

		// Hero.
		'hero_image'            => '',
		'hero_overflow_image_enabled' => true,
		'hero_overflow_image'   => '',
		'hero_overflow_image_effect' => 'none',
		'hero_jet_enabled'      => false,
		'hero_jet_display_mode' => 'all_pages',
		'hero_jet_pages'        => '[]',
		'hero_jet_source'       => '',
		'hero_jet_width'        => 130,
		'hero_jet_top_offset'   => 20,
		'hero_jet_speed'        => 5,
		'hero_jet_sound'        => '',
		'hero_jet_sound_icon_enabled' => true,

		// Fly Birds — a site-wide decorative bird flock.
		'fly_birds_enabled'     => false,
		'fly_birds_quantity'    => 5,
		'fly_birds_color_mode'  => 'fixed',
		'fly_birds_color'       => '#0b3d2e',
		'fly_birds_size_mode'   => 'fixed',
		'fly_birds_size'        => 30,
		'fly_birds_size_min'    => 20,
		'fly_birds_size_max'    => 45,
		'fly_birds_display_mode' => 'all_pages',
		'fly_birds_pages'       => '[]',
		'fly_birds_speed'       => 15,
		'hero_jet_thrust_length' => 40,
		'hero_jet_pause_min'    => 4,
		'hero_jet_pause_max'    => 11,
		'hero_jet_sound_mode'   => 'medium',
		'hero_slides'           => '[]',
		'hero_slider_transition' => 'fade',
		'hero_overlay_opacity'  => 0.72,
		'hero_eyebrow'          => __( 'Clean Energy, Engineered', 'solaris' ),
		'hero_heading'          => __( 'Power Your Future With Clean Solar Energy', 'solaris' ),
		'hero_description'     => __( 'Reliable solar solutions designed to reduce energy costs and build a cleaner, more sustainable future.', 'solaris' ),
		'hero_primary_text'    => __( 'Shop Solar Products', 'solaris' ),
		'hero_primary_url'     => '',
		'hero_secondary_text'  => __( 'Explore Solutions', 'solaris' ),
		'hero_secondary_url'   => '#solar-solutions',
		'hero_content_animation' => 'none',
		'hero_solar_system_enabled' => false,

		// Stats (placeholder values — replace with real figures).
		'stat_1_value' => '10+', 'stat_1_label' => __( 'Years Experience (placeholder)', 'solaris' ),
		'stat_2_value' => '5,000+', 'stat_2_label' => __( 'Solar Systems Installed (placeholder)', 'solaris' ),
		'stat_3_value' => '98%', 'stat_3_label' => __( 'Customer Satisfaction (placeholder)', 'solaris' ),
		'stat_4_value' => '25+', 'stat_4_label' => __( 'Product Categories (placeholder)', 'solaris' ),

		// Featured products ("Top Picks"). No card-level style controls here
		// on purpose — cards are genuine WooCommerce product-loop markup
		// (see template-parts/home/featured-products.php), not reimplemented.
		'featured_products_source'         => 'featured',
		'featured_products_heading'        => __( 'Featured Products', 'solaris' ),
		'featured_products_count'          => 8,
		'featured_products_eyebrow_color'  => '',
		'featured_products_title_color'    => '',
		'featured_products_bg_color'       => '',
		'featured_products_bg_image'       => '',
		'featured_products_bg_image_opacity'  => 1,
		'featured_products_bg_image_filter'   => 'none',
		'featured_products_bg_image_repeat'   => 'no-repeat',
		'featured_products_bg_image_position' => 'center',

		// Homepage blog posts. home_blog_categories is a JSON array of
		// category term IDs ('[]' = every category).
		'home_blog_enabled'     => true,
		'home_blog_eyebrow'     => __( 'From Our Blog', 'solaris' ),
		'home_blog_heading'     => __( 'Solar Insights & Guides', 'solaris' ),
		'home_blog_categories'  => '[]',
		'home_blog_count'       => 3,
		'home_blog_show_button' => true,
		'home_blog_button_text' => __( 'Show All', 'solaris' ),
		'home_blog_button_url'  => '',

		// Why Choose Us. Color fields default to '' (theme's built-in look);
		// solaris_customizer_css() falls back to the hardcoded value below
		// whenever a field is left blank.
		'why_choose_title'              => __( 'Built On Trust & Engineering', 'solaris' ),
		'why_choose_eyebrow_color'      => '',
		'why_choose_title_color'        => '',
		'why_choose_bg_color'           => '',
		'why_choose_bg_image'           => '',
		'why_choose_bg_image_opacity'   => 1,
		'why_choose_bg_image_filter'    => 'none',
		'why_choose_bg_image_repeat'    => 'no-repeat',
		'why_choose_bg_image_position'  => 'center',
		'why_choose_card_bg_color'      => '',
		'why_choose_card_border_color'  => '',
		'why_choose_card_text_color'    => '',
		'why_choose_card_icon_bg_color' => '',
		'why_choose_card_icon_color'    => '',
		'why_choose_card_hover_effect'  => 'lift',
		'why_choose_card_animation'     => 'fade-up',

		// Solar Solutions.
		'solutions_title'               => __( 'Designed For Every Use Case', 'solaris' ),
		'solutions_eyebrow_color'       => '',
		'solutions_title_color'         => '',
		'solutions_bg_color'            => '',
		'solutions_bg_image'            => '',
		'solutions_bg_image_opacity'    => 1,
		'solutions_bg_image_filter'     => 'none',
		'solutions_bg_image_repeat'     => 'no-repeat',
		'solutions_bg_image_position'   => 'center',
		'solutions_card_overlay_color'  => '',
		'solutions_card_border_color'   => '',
		'solutions_card_text_color'     => '',
		'solutions_card_icon_bg_color'  => '',
		'solutions_card_icon_color'     => '',
		'solutions_card_hover_effect'   => 'none',
		'solutions_card_animation'      => 'fade-up',

		// Customer Stories (testimonials).
		'testimonials_title'                  => __( 'Trusted By Homeowners & Businesses', 'solaris' ),
		'testimonials_eyebrow_color'          => '',
		'testimonials_title_color'            => '',
		'testimonials_bg_color'               => '',
		'testimonials_bg_image'               => '',
		'testimonials_bg_image_opacity'       => 1,
		'testimonials_bg_image_filter'        => 'none',
		'testimonials_bg_image_repeat'        => 'no-repeat',
		'testimonials_bg_image_position'      => 'center',
		'testimonials_card_bg_color'          => '',
		'testimonials_card_border_color'      => '',
		'testimonials_card_text_color'        => '',
		'testimonials_card_avatar_bg_color'   => '',
		'testimonials_card_avatar_text_color' => '',
		'testimonials_card_hover_effect'      => 'none',
		'testimonials_card_animation'         => 'fade-up',

		// Frequently Asked Questions.
		'faq_title'              => __( 'Got Questions? We Have Answers.', 'solaris' ),
		'faq_eyebrow_color'      => '',
		'faq_title_color'        => '',
		'faq_items'              => wp_json_encode( array(
			array( 'question' => __( 'How do solar panels work?', 'solaris' ), 'answer' => __( 'Solar panels convert sunlight into electricity using photovoltaic cells. An inverter then converts that electricity into a form your home or business can use.', 'solaris' ) ),
			array( 'question' => __( 'What size solar system do I need?', 'solaris' ), 'answer' => __( 'It depends on your energy usage, roof space and budget. Contact our team for a personalized recommendation.', 'solaris' ) ),
			array( 'question' => __( 'How long do solar panels last?', 'solaris' ), 'answer' => __( 'Most panels are rated for 25+ years of productive use, with manufacturer warranties to match.', 'solaris' ) ),
			array( 'question' => __( 'Do solar panels work on cloudy days?', 'solaris' ), 'answer' => __( 'Yes — panels still generate electricity in cloudy conditions, though at reduced output compared to direct sunlight.', 'solaris' ) ),
			array( 'question' => __( 'What type of inverter should I choose?', 'solaris' ), 'answer' => __( 'String, micro, and hybrid inverters each suit different setups. Check individual product specifications or reach out for guidance.', 'solaris' ) ),
			array( 'question' => __( 'Do you provide installation?', 'solaris' ), 'answer' => __( 'We sell equipment; installation availability varies by region — contact us to confirm options in your area.', 'solaris' ) ),
		) ),
		'faq_bg_color'           => '',
		'faq_bg_image'           => '',
		'faq_bg_image_opacity'   => 1,
		'faq_bg_image_filter'    => 'none',
		'faq_bg_image_repeat'    => 'no-repeat',
		'faq_bg_image_position'  => 'center',
		'faq_item_bg_color'      => '',
		'faq_item_border_color'  => '',
		'faq_item_text_color'    => '',
		'faq_icon_color'         => '',
		'faq_hover_effect'       => 'none',
		'faq_animation'          => 'none',

		// Promo banner.
		'promo_heading'     => __( 'Make the Switch to Solar', 'solaris' ),
		'promo_text'        => __( 'Discover efficient solar solutions for your home or business.', 'solaris' ),
		'promo_button_text' => __( 'Shop Now', 'solaris' ),
		'promo_button_url'  => '',

		// Final CTA.
		'final_cta_heading'     => __( 'Ready to Start Your Solar Journey?', 'solaris' ),
		'final_cta_text'        => __( 'Browse our full range of panels, inverters, batteries and accessories.', 'solaris' ),
		'final_cta_button_text' => __( 'Shop Now', 'solaris' ),
		'final_cta_button_url'  => '',

		// Contact info.
		'contact_phone'   => '',
		'contact_email'   => '',
		'contact_address' => '',
		'contact_hours'   => __( 'Mon–Fri, 9am–5pm (placeholder)', 'solaris' ),

		// Social links.
		'social_facebook'  => '',
		'social_twitter'   => '',
		'social_instagram' => '',
		'social_linkedin'  => '',
		'social_youtube'   => '',

		// Product card reveal animation. Card N's duration/delay =
		// base + (N-1) * step, N running 1-4 across each row.
		'reveal_base_duration'  => 0.5,
		'reveal_duration_step'  => 0.15,
		'reveal_base_delay'     => 0,
		'reveal_delay_step'     => 0.1,

		'loop_quantity_enabled' => true,
		'qty_stepper_enabled'   => true,
		'shop_show_categories'       => true,
		'shop_show_child_categories' => true,
		'shop_category_position'     => 'left',
		'shop_columns_desktop'       => 4,
		'shop_columns_mobile'        => 2,
		'shop_loading_mode'          => 'pagination',
		'shop_products_per_page'     => 12,
		'show_view_cart_link'   => true,
		'fly_to_cart_enabled'   => true,
		'fly_to_cart_multi_enabled' => false,
		'fly_to_cart_clone_bg_color' => '#ffffff',
		'fly_to_cart_clone_shadow'   => 0,
		'quick_view_enabled'   => true,
	) );
}

/**
 * Gradient-overlay defaults for every Customizer-styled homepage section
 * (sits above the section background color/image, below the content) —
 * merged into solaris_customizer_defaults() below rather than repeated
 * five times per section.
 */
function solaris_section_overlay_defaults() {
	$defaults = array();
	foreach ( array( 'why_choose', 'solutions', 'testimonials', 'faq', 'featured_products' ) as $prefix ) {
		$defaults[ "{$prefix}_bg_overlay_enabled" ]     = false;
		$defaults[ "{$prefix}_bg_overlay_start_color" ] = '#071c15';
		$defaults[ "{$prefix}_bg_overlay_end_color" ]   = '#071c15';
		$defaults[ "{$prefix}_bg_overlay_angle" ]       = 135;
		$defaults[ "{$prefix}_bg_overlay_opacity" ]     = 0.5;
	}
	return $defaults;
}

function solaris_get_option( $key ) {
	$defaults = solaris_customizer_defaults();
	$default  = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
	return get_theme_mod( $key, $default );
}

function solaris_sanitize_checkbox( $checked ) {
	return (bool) $checked;
}

function solaris_sanitize_select( $input, $setting ) {
	$choices = $setting->manager->get_control( $setting->id )->choices;
	return array_key_exists( $input, $choices ) ? $input : $setting->default;
}

function solaris_sanitize_float( $input ) {
	return is_numeric( $input ) ? floatval( $input ) : 0;
}

/**
 * Clamps to the control's own advertised range (input_attrs min/max on the
 * "Products Per Page" control are an HTML5 attribute only — not enforced by
 * WordPress on save without this).
 */
function solaris_sanitize_products_per_page( $input ) {
	return min( 100, max( 1, absint( $input ) ) );
}

/**
 * Registers the "Section Identity Text Color" (eyebrow) and "Title Color"
 * controls shared by every Customizer-styled homepage section. Independent
 * of whether the section's title *text* has its own dedicated control
 * (Featured Products keeps its pre-existing `featured_products_heading`
 * field rather than a duplicate `featured_products_title`).
 */
function solaris_register_section_title_color_controls( $wp_customize, $section_id, $prefix, $defaults ) {
	$wp_customize->add_setting( "{$prefix}_eyebrow_color", array( 'default' => $defaults["{$prefix}_eyebrow_color"], 'sanitize_callback' => 'sanitize_hex_color' ) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, "{$prefix}_eyebrow_color", array( 'label' => __( 'Section Identity Text Color', 'solaris' ), 'description' => __( 'The small eyebrow label above the title.', 'solaris' ), 'section' => $section_id ) ) );

	$wp_customize->add_setting( "{$prefix}_title_color", array( 'default' => $defaults["{$prefix}_title_color"], 'sanitize_callback' => 'sanitize_hex_color' ) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, "{$prefix}_title_color", array( 'label' => __( 'Title Color', 'solaris' ), 'section' => $section_id ) ) );
}

/**
 * Registers the background color/image/opacity/filter/repeat/position +
 * gradient-overlay controls shared by every Customizer-styled homepage
 * section. Expects `{$prefix}_bg_color`, `{$prefix}_bg_image` and its
 * sibling fields, plus the `{$prefix}_bg_overlay_*` fields from
 * solaris_section_overlay_defaults(), to already exist in
 * solaris_customizer_defaults().
 */
function solaris_register_section_bg_layer_controls( $wp_customize, $section_id, $prefix, $defaults ) {
	$wp_customize->add_setting( "{$prefix}_bg_color", array( 'default' => $defaults["{$prefix}_bg_color"], 'sanitize_callback' => 'sanitize_hex_color' ) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, "{$prefix}_bg_color", array( 'label' => __( 'Section Background Color', 'solaris' ), 'section' => $section_id ) ) );

	$wp_customize->add_setting( "{$prefix}_bg_image", array( 'default' => $defaults["{$prefix}_bg_image"], 'sanitize_callback' => 'esc_url_raw' ) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, "{$prefix}_bg_image", array( 'label' => __( 'Section Background Image', 'solaris' ), 'section' => $section_id ) ) );

	$wp_customize->add_setting( "{$prefix}_bg_image_opacity", array( 'default' => $defaults["{$prefix}_bg_image_opacity"], 'sanitize_callback' => 'solaris_sanitize_float' ) );
	$wp_customize->add_control( "{$prefix}_bg_image_opacity", array( 'label' => __( 'Background Image Opacity', 'solaris' ), 'section' => $section_id, 'type' => 'range', 'input_attrs' => array( 'min' => 0, 'max' => 1, 'step' => 0.05 ) ) );

	$wp_customize->add_setting( "{$prefix}_bg_image_filter", array( 'default' => $defaults["{$prefix}_bg_image_filter"], 'sanitize_callback' => 'solaris_sanitize_select' ) );
	$wp_customize->add_control(
		"{$prefix}_bg_image_filter",
		array(
			'label'   => __( 'Background Image Filter', 'solaris' ),
			'section' => $section_id,
			'type'    => 'select',
			'choices' => solaris_image_filter_choices(),
		)
	);

	$wp_customize->add_setting( "{$prefix}_bg_image_repeat", array( 'default' => $defaults["{$prefix}_bg_image_repeat"], 'sanitize_callback' => 'solaris_sanitize_select' ) );
	$wp_customize->add_control(
		"{$prefix}_bg_image_repeat",
		array(
			'label'   => __( 'Background Image Repeat', 'solaris' ),
			'section' => $section_id,
			'type'    => 'select',
			'choices' => array(
				'no-repeat' => __( 'No Repeat (cover)', 'solaris' ),
				'repeat'    => __( 'Tile (repeat both directions)', 'solaris' ),
				'repeat-x'  => __( 'Tile Horizontally', 'solaris' ),
				'repeat-y'  => __( 'Tile Vertically', 'solaris' ),
			),
		)
	);

	$wp_customize->add_setting( "{$prefix}_bg_image_position", array( 'default' => $defaults["{$prefix}_bg_image_position"], 'sanitize_callback' => 'solaris_sanitize_select' ) );
	$wp_customize->add_control(
		"{$prefix}_bg_image_position",
		array(
			'label'   => __( 'Background Image Position', 'solaris' ),
			'section' => $section_id,
			'type'    => 'select',
			'choices' => array(
				'center'        => __( 'Center', 'solaris' ),
				'top'           => __( 'Top', 'solaris' ),
				'bottom'        => __( 'Bottom', 'solaris' ),
				'left'          => __( 'Left', 'solaris' ),
				'right'         => __( 'Right', 'solaris' ),
				'top left'      => __( 'Top Left', 'solaris' ),
				'top right'     => __( 'Top Right', 'solaris' ),
				'bottom left'   => __( 'Bottom Left', 'solaris' ),
				'bottom right'  => __( 'Bottom Right', 'solaris' ),
			),
		)
	);

	$wp_customize->add_setting( "{$prefix}_bg_overlay_enabled", array( 'default' => $defaults["{$prefix}_bg_overlay_enabled"], 'sanitize_callback' => 'solaris_sanitize_checkbox' ) );
	$wp_customize->add_control( "{$prefix}_bg_overlay_enabled", array( 'label' => __( 'Enable Gradient Overlay', 'solaris' ), 'description' => __( 'A tinted gradient layer over the section background — improves text contrast over photos.', 'solaris' ), 'section' => $section_id, 'type' => 'checkbox' ) );

	$wp_customize->add_setting( "{$prefix}_bg_overlay_start_color", array( 'default' => $defaults["{$prefix}_bg_overlay_start_color"], 'sanitize_callback' => 'sanitize_hex_color' ) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, "{$prefix}_bg_overlay_start_color", array( 'label' => __( 'Overlay Gradient Start Color', 'solaris' ), 'section' => $section_id ) ) );

	$wp_customize->add_setting( "{$prefix}_bg_overlay_end_color", array( 'default' => $defaults["{$prefix}_bg_overlay_end_color"], 'sanitize_callback' => 'sanitize_hex_color' ) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, "{$prefix}_bg_overlay_end_color", array( 'label' => __( 'Overlay Gradient End Color', 'solaris' ), 'section' => $section_id ) ) );

	$wp_customize->add_setting( "{$prefix}_bg_overlay_angle", array( 'default' => $defaults["{$prefix}_bg_overlay_angle"], 'sanitize_callback' => 'absint' ) );
	$wp_customize->add_control( "{$prefix}_bg_overlay_angle", array( 'label' => __( 'Overlay Gradient Angle', 'solaris' ), 'section' => $section_id, 'type' => 'range', 'input_attrs' => array( 'min' => 0, 'max' => 360, 'step' => 5 ) ) );

	$wp_customize->add_setting( "{$prefix}_bg_overlay_opacity", array( 'default' => $defaults["{$prefix}_bg_overlay_opacity"], 'sanitize_callback' => 'solaris_sanitize_float' ) );
	$wp_customize->add_control( "{$prefix}_bg_overlay_opacity", array( 'label' => __( 'Overlay Opacity', 'solaris' ), 'section' => $section_id, 'type' => 'range', 'input_attrs' => array( 'min' => 0, 'max' => 1, 'step' => 0.05 ) ) );
}

/**
 * Registers the "Title" text field plus every shared title-color and
 * background-layer control for a Customizer-styled homepage section.
 * Used by Why Choose Us, Solar Solutions, Customer Stories and FAQ, which
 * all have a single generic "Title" field. Featured Products has its own
 * pre-existing heading field, so it calls
 * solaris_register_section_title_color_controls() and
 * solaris_register_section_bg_layer_controls() directly instead of this.
 */
function solaris_register_section_bg_controls( $wp_customize, $section_id, $prefix, $title_label, $defaults ) {
	$wp_customize->add_setting( "{$prefix}_title", array( 'default' => $defaults["{$prefix}_title"], 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control( "{$prefix}_title", array( 'label' => $title_label, 'section' => $section_id, 'type' => 'text' ) );

	solaris_register_section_title_color_controls( $wp_customize, $section_id, $prefix, $defaults );
	solaris_register_section_bg_layer_controls( $wp_customize, $section_id, $prefix, $defaults );
}

/** Registers a single select-type Customizer control with the given choices. */
function solaris_register_select_control( $wp_customize, $section_id, $setting_id, $label, $default, $choices ) {
	$wp_customize->add_setting( $setting_id, array( 'default' => $default, 'sanitize_callback' => 'solaris_sanitize_select' ) );
	$wp_customize->add_control( $setting_id, array( 'label' => $label, 'section' => $section_id, 'type' => 'select', 'choices' => $choices ) );
}

/** Choices shared by every section's "Card Entrance Animation" select. */
function solaris_animation_choices() {
	return array(
		'fade-up'     => __( 'Fade Up', 'solaris' ),
		'zoom-in'     => __( 'Zoom In', 'solaris' ),
		'slide-left'  => __( 'Slide From Left', 'solaris' ),
		'slide-right' => __( 'Slide From Right', 'solaris' ),
		'none'        => __( 'None', 'solaris' ),
	);
}

/**
 * Choices shared by every "Image Filter" select — the section background
 * image layers (solaris_register_section_bg_layer_controls()) and the Hero
 * overflow image. Keys must match solaris_image_filter_css_map()'s keys in
 * inc/template-functions.php, which resolves each to a real CSS filter().
 */
function solaris_image_filter_choices() {
	return array(
		'none'      => __( 'None', 'solaris' ),
		'grayscale' => __( 'Grayscale', 'solaris' ),
		'blur'      => __( 'Blur', 'solaris' ),
		'sepia'     => __( 'Sepia', 'solaris' ),
		'darken'    => __( 'Darken', 'solaris' ),
	);
}

/**
 * Validates the Hero Slides repeater's JSON: an array of
 * {id: attachment ID, active: bool, plus optional per-slide text/button
 * fields — eyebrow, title, subtext, button1_text, button1_url,
 * button2_text, button2_url}, dropping anything that isn't a real,
 * currently-existing image attachment. Falls back to '[]' (no slides —
 * theme falls back to the single Hero Background Image) on any malformed
 * input rather than saving a broken value.
 */
function solaris_sanitize_hero_slides( $json ) {
	$decoded = json_decode( is_string( $json ) ? $json : '', true );
	if ( ! is_array( $decoded ) ) {
		return '[]';
	}

	$clean = array();
	foreach ( $decoded as $slide ) {
		if ( ! is_array( $slide ) || empty( $slide['id'] ) ) {
			continue;
		}
		$attachment_id = absint( $slide['id'] );
		if ( ! $attachment_id || 'attachment' !== get_post_type( $attachment_id ) || ! wp_attachment_is_image( $attachment_id ) ) {
			continue;
		}
		$clean[] = array(
			'id'           => $attachment_id,
			'active'       => ! empty( $slide['active'] ),
			'eyebrow'      => isset( $slide['eyebrow'] ) ? sanitize_text_field( $slide['eyebrow'] ) : '',
			'title'        => isset( $slide['title'] ) ? sanitize_text_field( $slide['title'] ) : '',
			'subtext'      => isset( $slide['subtext'] ) ? sanitize_textarea_field( $slide['subtext'] ) : '',
			'button1_text' => isset( $slide['button1_text'] ) ? sanitize_text_field( $slide['button1_text'] ) : '',
			'button1_url'  => isset( $slide['button1_url'] ) ? esc_url_raw( $slide['button1_url'] ) : '',
			'button2_text' => isset( $slide['button2_text'] ) ? sanitize_text_field( $slide['button2_text'] ) : '',
			'button2_url'  => isset( $slide['button2_url'] ) ? esc_url_raw( $slide['button2_url'] ) : '',
		);
	}

	return wp_json_encode( $clean );
}

/**
 * Validates the FAQ Items repeater's JSON: an array of
 * {question, answer} string pairs. Drops any entry with a blank question
 * (an answer alone isn't useful); falls back to '[]' on malformed input.
 */
function solaris_sanitize_faq_items( $json ) {
	$decoded = json_decode( is_string( $json ) ? $json : '', true );
	if ( ! is_array( $decoded ) ) {
		return '[]';
	}

	$clean = array();
	foreach ( $decoded as $item ) {
		if ( ! is_array( $item ) || empty( $item['question'] ) ) {
			continue;
		}
		$clean[] = array(
			'question' => sanitize_text_field( $item['question'] ),
			'answer'   => isset( $item['answer'] ) ? sanitize_textarea_field( $item['answer'] ) : '',
		);
	}

	return wp_json_encode( $clean );
}

/**
 * JSON-encoded array of page IDs, shared by every "Specific Pages" display
 * mode (Fighter-Z, Fly Birds, ...). -1 is a synthetic ID meaning "the front
 * page" — WordPress doesn't always represent that as a real page (e.g. when
 * Settings > Reading shows latest posts), so it can't just be one of the
 * real IDs below.
 */
function solaris_sanitize_page_ids( $json ) {
	$decoded = json_decode( is_string( $json ) ? $json : '', true );
	if ( ! is_array( $decoded ) ) {
		return '[]';
	}

	$clean = array();
	foreach ( $decoded as $page_id ) {
		$page_id = (int) $page_id;
		if ( -1 === $page_id || ( $page_id > 0 && 'page' === get_post_type( $page_id ) ) ) {
			$clean[] = $page_id;
		}
	}

	return wp_json_encode( array_values( array_unique( $clean ) ) );
}

/**
 * JSON-encoded array of post category term IDs (homepage blog section).
 * Drops anything that isn't an existing `category` term.
 */
function solaris_sanitize_category_ids( $json ) {
	$decoded = json_decode( is_string( $json ) ? $json : '', true );
	if ( ! is_array( $decoded ) ) {
		return '[]';
	}

	$clean = array();
	foreach ( $decoded as $term_id ) {
		$term_id = (int) $term_id;
		if ( $term_id > 0 && term_exists( $term_id, 'category' ) ) {
			$clean[] = $term_id;
		}
	}

	return wp_json_encode( array_values( array_unique( $clean ) ) );
}

function solaris_customize_register( $wp_customize ) {
	$wp_customize->add_panel(
		'solaris_options',
		array(
			'title'    => __( 'Solaris Theme Options', 'solaris' ),
			'priority' => 30,
		)
	);

	/* ---------------------------------------------------------------- Colors */
	$wp_customize->add_section(
		'solaris_colors',
		array( 'title' => __( 'Colors', 'solaris' ), 'panel' => 'solaris_options' )
	);

	$color_fields = array(
		'color_primary'        => __( 'Primary Color', 'solaris' ),
		'color_secondary'      => __( 'Secondary Color', 'solaris' ),
		'color_accent'         => __( 'Accent Color', 'solaris' ),
		'color_heading'        => __( 'Heading Color', 'solaris' ),
		'color_text'           => __( 'Text Color', 'solaris' ),
		'color_background'     => __( 'Background Color', 'solaris' ),
		'header_background'    => __( 'Header Background', 'solaris' ),
		'header_text_color'    => __( 'Header Text Color', 'solaris' ),
		'footer_background'    => __( 'Footer Background', 'solaris' ),
		'footer_text_color'    => __( 'Footer Text Color', 'solaris' ),
		'footer_heading_color' => __( 'Footer Heading Color', 'solaris' ),
		'footer_accent_color'  => __( 'Footer Accent Color', 'solaris' ),
		'button_text_color'    => __( 'Button Text Color', 'solaris' ),
		'btn_gradient_start'   => __( 'Button Gradient Start', 'solaris' ),
		'btn_gradient_mid'     => __( 'Button Gradient Middle', 'solaris' ),
		'btn_gradient_end'     => __( 'Button Gradient End', 'solaris' ),
	);
	$defaults = solaris_customizer_defaults();
	foreach ( $color_fields as $id => $label ) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $defaults[ $id ],
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'postMessage',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				$id,
				array( 'label' => $label, 'section' => 'solaris_colors' )
			)
		);
	}

	/* ----------------------------------------------------------- Typography */
	$wp_customize->add_section(
		'solaris_typography',
		array( 'title' => __( 'Typography', 'solaris' ), 'panel' => 'solaris_options' )
	);

	$font_choices = array(
		'Space Grotesk' => 'Space Grotesk',
		'Inter'   => 'Inter',
		'Manrope' => 'Manrope',
		'Poppins' => 'Poppins',
		'system-ui' => __( 'System Default', 'solaris' ),
	);

	$wp_customize->add_setting( 'heading_font', array( 'default' => $defaults['heading_font'], 'sanitize_callback' => 'solaris_sanitize_select' ) );
	$wp_customize->add_control( 'heading_font', array( 'label' => __( 'Heading Font', 'solaris' ), 'section' => 'solaris_typography', 'type' => 'select', 'choices' => $font_choices ) );

	$wp_customize->add_setting( 'body_font', array( 'default' => $defaults['body_font'], 'sanitize_callback' => 'solaris_sanitize_select' ) );
	$wp_customize->add_control( 'body_font', array( 'label' => __( 'Body Font', 'solaris' ), 'section' => 'solaris_typography', 'type' => 'select', 'choices' => $font_choices ) );

	$wp_customize->add_setting( 'base_font_size', array( 'default' => $defaults['base_font_size'], 'sanitize_callback' => 'absint' ) );
	$wp_customize->add_control( 'base_font_size', array( 'label' => __( 'Base Font Size (px)', 'solaris' ), 'section' => 'solaris_typography', 'type' => 'range', 'input_attrs' => array( 'min' => 14, 'max' => 19, 'step' => 1 ) ) );

	$wp_customize->add_setting( 'heading_scale', array( 'default' => $defaults['heading_scale'], 'sanitize_callback' => 'solaris_sanitize_float' ) );
	$wp_customize->add_control( 'heading_scale', array( 'label' => __( 'Heading Scale', 'solaris' ), 'section' => 'solaris_typography', 'type' => 'range', 'input_attrs' => array( 'min' => 1.1, 'max' => 1.5, 'step' => 0.05 ) ) );

	$wp_customize->add_setting( 'line_height', array( 'default' => $defaults['line_height'], 'sanitize_callback' => 'solaris_sanitize_float' ) );
	$wp_customize->add_control( 'line_height', array( 'label' => __( 'Body Line Height', 'solaris' ), 'section' => 'solaris_typography', 'type' => 'range', 'input_attrs' => array( 'min' => 1.3, 'max' => 2, 'step' => 0.05 ) ) );

	/* ---------------------------------------------------------------- Header */
	$wp_customize->add_section(
		'solaris_header',
		array( 'title' => __( 'Header', 'solaris' ), 'panel' => 'solaris_options' )
	);

	$wp_customize->add_setting( 'header_height', array( 'default' => $defaults['header_height'], 'sanitize_callback' => 'absint' ) );
	$wp_customize->add_control( 'header_height', array( 'label' => __( 'Header Height (px)', 'solaris' ), 'section' => 'solaris_header', 'type' => 'range', 'input_attrs' => array( 'min' => 64, 'max' => 120, 'step' => 2 ) ) );

	$wp_customize->add_setting( 'header_transparent', array( 'default' => $defaults['header_transparent'], 'sanitize_callback' => 'solaris_sanitize_checkbox' ) );
	$wp_customize->add_control( 'header_transparent', array( 'label' => __( 'Transparent header over homepage hero', 'solaris' ), 'section' => 'solaris_header', 'type' => 'checkbox' ) );

	$wp_customize->add_setting( 'sticky_header', array( 'default' => $defaults['sticky_header'], 'sanitize_callback' => 'solaris_sanitize_checkbox' ) );
	$wp_customize->add_control( 'sticky_header', array( 'label' => __( 'Sticky header on scroll', 'solaris' ), 'section' => 'solaris_header', 'type' => 'checkbox' ) );

	$wp_customize->add_setting( 'header_cta_text', array( 'default' => $defaults['header_cta_text'], 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control( 'header_cta_text', array( 'label' => __( 'Header CTA Button Text', 'solaris' ), 'section' => 'solaris_header', 'type' => 'text' ) );

	$wp_customize->add_setting( 'header_cta_url', array( 'default' => $defaults['header_cta_url'], 'sanitize_callback' => 'esc_url_raw' ) );
	$wp_customize->add_control( 'header_cta_url', array( 'label' => __( 'Header CTA Button URL (defaults to Shop page)', 'solaris' ), 'section' => 'solaris_header', 'type' => 'url' ) );

	$wp_customize->add_setting( 'show_cart_icon', array( 'default' => $defaults['show_cart_icon'], 'sanitize_callback' => 'solaris_sanitize_checkbox' ) );
	$wp_customize->add_control( 'show_cart_icon', array( 'label' => __( 'Show cart icon', 'solaris' ), 'section' => 'solaris_header', 'type' => 'checkbox' ) );

	$wp_customize->add_setting( 'show_account_icon', array( 'default' => $defaults['show_account_icon'], 'sanitize_callback' => 'solaris_sanitize_checkbox' ) );
	$wp_customize->add_control( 'show_account_icon', array( 'label' => __( 'Show account icon', 'solaris' ), 'section' => 'solaris_header', 'type' => 'checkbox' ) );

	$wp_customize->add_setting( 'header_bg_gradient_enabled', array( 'default' => $defaults['header_bg_gradient_enabled'], 'sanitize_callback' => 'solaris_sanitize_checkbox' ) );
	$wp_customize->add_control( 'header_bg_gradient_enabled', array( 'label' => __( 'Use a gradient header background (solid/scrolled state)', 'solaris' ), 'section' => 'solaris_header', 'type' => 'checkbox' ) );

	$wp_customize->add_setting( 'header_gradient_start', array( 'default' => $defaults['header_gradient_start'], 'sanitize_callback' => 'sanitize_hex_color' ) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'header_gradient_start', array( 'label' => __( 'Header Gradient Start', 'solaris' ), 'section' => 'solaris_header' ) ) );

	$wp_customize->add_setting( 'header_gradient_end', array( 'default' => $defaults['header_gradient_end'], 'sanitize_callback' => 'sanitize_hex_color' ) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'header_gradient_end', array( 'label' => __( 'Header Gradient End', 'solaris' ), 'section' => 'solaris_header' ) ) );

	$wp_customize->add_setting( 'header_gradient_fade', array( 'default' => $defaults['header_gradient_fade'], 'sanitize_callback' => 'solaris_sanitize_checkbox' ) );
	$wp_customize->add_control( 'header_gradient_fade', array( 'label' => __( 'Fade gradient end to transparent', 'solaris' ), 'section' => 'solaris_header', 'type' => 'checkbox' ) );

	$wp_customize->add_setting( 'header_gradient_angle', array( 'default' => $defaults['header_gradient_angle'], 'sanitize_callback' => 'absint' ) );
	$wp_customize->add_control( 'header_gradient_angle', array( 'label' => __( 'Header Gradient Angle (degrees)', 'solaris' ), 'section' => 'solaris_header', 'type' => 'range', 'input_attrs' => array( 'min' => 0, 'max' => 360, 'step' => 5 ) ) );

	/* ---------------------------------------------------------------- Footer */
	$wp_customize->add_section(
		'solaris_footer',
		array( 'title' => __( 'Footer', 'solaris' ), 'panel' => 'solaris_options' )
	);

	$wp_customize->add_setting( 'footer_logo', array( 'default' => $defaults['footer_logo'], 'sanitize_callback' => 'esc_url_raw' ) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'footer_logo', array( 'label' => __( 'Footer Logo (defaults to the Site Identity logo if left blank)', 'solaris' ), 'section' => 'solaris_footer' ) ) );

	$wp_customize->add_setting( 'footer_logo_width', array( 'default' => $defaults['footer_logo_width'], 'sanitize_callback' => 'absint', 'transport' => 'postMessage' ) );
	$wp_customize->add_control( 'footer_logo_width', array( 'label' => __( 'Footer Logo Width — Desktop (px)', 'solaris' ), 'section' => 'solaris_footer', 'type' => 'range', 'input_attrs' => array( 'min' => 40, 'max' => 320, 'step' => 4 ) ) );

	$wp_customize->add_setting( 'footer_logo_height', array( 'default' => $defaults['footer_logo_height'], 'sanitize_callback' => 'absint', 'transport' => 'postMessage' ) );
	$wp_customize->add_control( 'footer_logo_height', array( 'label' => __( 'Footer Logo Height — Desktop (px)', 'solaris' ), 'section' => 'solaris_footer', 'type' => 'range', 'input_attrs' => array( 'min' => 16, 'max' => 160, 'step' => 2 ) ) );

	$wp_customize->add_setting( 'footer_logo_width_mobile', array( 'default' => $defaults['footer_logo_width_mobile'], 'sanitize_callback' => 'absint', 'transport' => 'postMessage' ) );
	$wp_customize->add_control( 'footer_logo_width_mobile', array( 'label' => __( 'Footer Logo Width — Mobile (px)', 'solaris' ), 'section' => 'solaris_footer', 'type' => 'range', 'input_attrs' => array( 'min' => 32, 'max' => 240, 'step' => 4 ) ) );

	$wp_customize->add_setting( 'footer_logo_height_mobile', array( 'default' => $defaults['footer_logo_height_mobile'], 'sanitize_callback' => 'absint', 'transport' => 'postMessage' ) );
	$wp_customize->add_control( 'footer_logo_height_mobile', array( 'label' => __( 'Footer Logo Height — Mobile (px)', 'solaris' ), 'section' => 'solaris_footer', 'type' => 'range', 'input_attrs' => array( 'min' => 12, 'max' => 120, 'step' => 2 ) ) );

	$wp_customize->add_setting( 'copyright_text', array( 'default' => $defaults['copyright_text'], 'sanitize_callback' => 'wp_kses_post' ) );
	$wp_customize->add_control( 'copyright_text', array( 'label' => __( 'Copyright Text (use {{year}} for the current year)', 'solaris' ), 'section' => 'solaris_footer', 'type' => 'text' ) );

	$wp_customize->add_setting( 'footer_wave_enabled', array( 'default' => $defaults['footer_wave_enabled'], 'sanitize_callback' => 'solaris_sanitize_checkbox' ) );
	$wp_customize->add_control( 'footer_wave_enabled', array( 'label' => __( 'Show Animated Footer Wave', 'solaris' ), 'description' => __( 'A CSS/SVG animated wave across the top of the footer. No JS animation loop — pure CSS transforms, safe for performance.', 'solaris' ), 'section' => 'solaris_footer', 'type' => 'checkbox' ) );

	$wp_customize->add_setting( 'footer_wave_style', array( 'default' => $defaults['footer_wave_style'], 'sanitize_callback' => 'solaris_sanitize_select' ) );
	$wp_customize->add_control( 'footer_wave_style', array(
		'label'   => __( 'Wave Animation Style', 'solaris' ),
		'section' => 'solaris_footer',
		'type'    => 'select',
		'choices' => array(
			'gentle' => __( 'Gentle Wave (calm, single layer)', 'solaris' ),
			'ocean'  => __( 'Ocean Waves (layered, with rising bubbles)', 'solaris' ),
			'aurora' => __( 'Aurora Ripple (soft glow, no SVG)', 'solaris' ),
		),
	) );

	$wp_customize->add_setting( 'footer_wave_color', array( 'default' => $defaults['footer_wave_color'], 'sanitize_callback' => 'sanitize_hex_color' ) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'footer_wave_color', array( 'label' => __( 'Wave Color', 'solaris' ), 'description' => __( 'Leave blank for a subtle translucent highlight over the footer background.', 'solaris' ), 'section' => 'solaris_footer' ) ) );

	$wp_customize->add_setting( 'footer_wave_speed', array( 'default' => $defaults['footer_wave_speed'], 'sanitize_callback' => 'absint' ) );
	$wp_customize->add_control( 'footer_wave_speed', array( 'label' => __( 'Wave Animation Speed (seconds per cycle)', 'solaris' ), 'section' => 'solaris_footer', 'type' => 'range', 'input_attrs' => array( 'min' => 1, 'max' => 20, 'step' => 1 ) ) );

	$wp_customize->add_setting( 'footer_wave_align', array( 'default' => $defaults['footer_wave_align'], 'sanitize_callback' => 'solaris_sanitize_select' ) );
	$wp_customize->add_control( 'footer_wave_align', array(
		'label'   => __( 'Align To', 'solaris' ),
		'description' => __( 'Which edge of the footer the wave sits against.', 'solaris' ),
		'section' => 'solaris_footer',
		'type'    => 'select',
		'choices' => array(
			'top'    => __( 'Top', 'solaris' ),
			'bottom' => __( 'Bottom', 'solaris' ),
		),
	) );

	$wp_customize->add_setting( 'footer_wave_size_mode', array( 'default' => $defaults['footer_wave_size_mode'], 'sanitize_callback' => 'solaris_sanitize_select' ) );
	$wp_customize->add_control( 'footer_wave_size_mode', array(
		'label'   => __( 'Size Mode', 'solaris' ),
		'description' => __( 'Short View: a thin animated band. Full Height: the wave crest plus a solid color wash across the whole footer.', 'solaris' ),
		'section' => 'solaris_footer',
		'type'    => 'select',
		'choices' => array(
			'short' => __( 'Short View', 'solaris' ),
			'full'  => __( 'Full Height', 'solaris' ),
		),
	) );

	/* ------------------------------------------------------------------ Hero */
	$wp_customize->add_section(
		'solaris_hero',
		array( 'title' => __( 'Homepage Hero', 'solaris' ), 'panel' => 'solaris_options' )
	);

	$wp_customize->add_setting( 'hero_image', array( 'default' => $defaults['hero_image'], 'sanitize_callback' => 'esc_url_raw' ) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'hero_image', array( 'label' => __( 'Hero Background Image (used only if no Hero Slides are configured below)', 'solaris' ), 'section' => 'solaris_hero' ) ) );

	$wp_customize->add_setting( 'hero_overflow_image_enabled', array( 'default' => $defaults['hero_overflow_image_enabled'], 'sanitize_callback' => 'solaris_sanitize_checkbox' ) );
	$wp_customize->add_control( 'hero_overflow_image_enabled', array( 'label' => __( 'Show Overflowing Decorative Image', 'solaris' ), 'section' => 'solaris_hero', 'type' => 'checkbox' ) );

	$wp_customize->add_setting( 'hero_overflow_image', array( 'default' => $defaults['hero_overflow_image'], 'sanitize_callback' => 'esc_url_raw' ) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'hero_overflow_image', array( 'label' => __( 'Overflowing Decorative Image', 'solaris' ), 'description' => __( 'Small decorative image that overlaps the bottom edge of the hero section. Leave blank to hide it.', 'solaris' ), 'section' => 'solaris_hero' ) ) );

	$wp_customize->add_setting( 'hero_overflow_image_effect', array( 'default' => $defaults['hero_overflow_image_effect'], 'sanitize_callback' => 'solaris_sanitize_select' ) );
	$wp_customize->add_control(
		'hero_overflow_image_effect',
		array(
			'label'   => __( 'Overflow Image Effect', 'solaris' ),
			'section' => 'solaris_hero',
			'type'    => 'select',
			'choices' => solaris_image_filter_choices(),
		)
	);

	/* ------------------------------------------------------------ Fighter-Z */
	$wp_customize->add_section(
		'solaris_fighter_z',
		array( 'title' => __( 'Fly Fighter-Z', 'solaris' ), 'panel' => 'solaris_options' )
	);

	$wp_customize->add_setting( 'hero_jet_enabled', array( 'default' => $defaults['hero_jet_enabled'], 'sanitize_callback' => 'solaris_sanitize_checkbox' ) );
	$wp_customize->add_control( 'hero_jet_enabled', array( 'label' => __( 'Fly Fighter-Z', 'solaris' ), 'description' => __( 'A single decorative fighter jet that occasionally flies across the page in a random direction, with a user-controlled engine-sound toggle.', 'solaris' ), 'section' => 'solaris_fighter_z', 'type' => 'checkbox' ) );

	$wp_customize->add_setting( 'hero_jet_display_mode', array( 'default' => $defaults['hero_jet_display_mode'], 'sanitize_callback' => 'solaris_sanitize_select' ) );
	$wp_customize->add_control(
		'hero_jet_display_mode',
		array(
			'label'   => __( 'Show On', 'solaris' ),
			'section' => 'solaris_fighter_z',
			'type'    => 'select',
			'choices' => array(
				'all_pages'      => __( 'All Pages', 'solaris' ),
				'specific_pages' => __( 'Specific Pages', 'solaris' ),
			),
		)
	);

	$wp_customize->add_setting( 'hero_jet_pages', array( 'default' => $defaults['hero_jet_pages'], 'sanitize_callback' => 'solaris_sanitize_page_ids' ) );
	if ( class_exists( 'Solaris_Page_Checklist_Control' ) ) {
		$wp_customize->add_control(
			new Solaris_Page_Checklist_Control(
				$wp_customize,
				'hero_jet_pages',
				array(
					'label'       => __( 'Specific Pages', 'solaris' ),
					'description' => __( 'Only used when "Show On" above is set to Specific Pages.', 'solaris' ),
					'section'     => 'solaris_fighter_z',
				)
			)
		);
	}

	$wp_customize->add_setting( 'hero_jet_source', array( 'default' => $defaults['hero_jet_source'], 'sanitize_callback' => 'esc_url_raw' ) );
	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'hero_jet_source',
			array(
				'label'       => __( 'Fighter-Z Image (PNG/SVG)', 'solaris' ),
				'description' => __( 'Upload your own fighter-jet image. Leave blank to use the built-in silhouette.', 'solaris' ),
				'section'     => 'solaris_fighter_z',
			)
		)
	);

	// Height isn't independently settable — it's derived from the uploaded
	// image's own real aspect ratio (see solaris_render_hero_jet()), so
	// raising Width scales the jet proportionally instead of stretching or
	// shrinking it inside a mismatched box.
	$wp_customize->add_setting( 'hero_jet_width', array( 'default' => $defaults['hero_jet_width'], 'sanitize_callback' => 'absint' ) );
	$wp_customize->add_control( 'hero_jet_width', array( 'label' => __( 'Fighter-Z Width (px)', 'solaris' ), 'description' => __( 'Height follows automatically from the image\'s own aspect ratio.', 'solaris' ), 'section' => 'solaris_fighter_z', 'type' => 'number', 'input_attrs' => array( 'min' => 20, 'max' => 600, 'step' => 1 ) ) );

	$wp_customize->add_setting( 'hero_jet_top_offset', array( 'default' => $defaults['hero_jet_top_offset'], 'sanitize_callback' => 'absint' ) );
	$wp_customize->add_control( 'hero_jet_top_offset', array( 'label' => __( 'From Top (px)', 'solaris' ), 'description' => __( 'Distance below the header where Fighter-Z flies.', 'solaris' ), 'section' => 'solaris_fighter_z', 'type' => 'number', 'input_attrs' => array( 'min' => 0, 'max' => 1000, 'step' => 1 ) ) );

	$wp_customize->add_setting( 'hero_jet_speed', array( 'default' => $defaults['hero_jet_speed'], 'sanitize_callback' => 'solaris_sanitize_float' ) );
	$wp_customize->add_control( 'hero_jet_speed', array( 'label' => __( 'Fighter-Z Speed (seconds to cross)', 'solaris' ), 'description' => __( 'Fixed time for one pass — lower is faster.', 'solaris' ), 'section' => 'solaris_fighter_z', 'type' => 'number', 'input_attrs' => array( 'min' => 2, 'max' => 40, 'step' => 0.5 ) ) );

	$wp_customize->add_setting( 'hero_jet_thrust_length', array( 'default' => $defaults['hero_jet_thrust_length'], 'sanitize_callback' => 'absint' ) );
	$wp_customize->add_control( 'hero_jet_thrust_length', array( 'label' => __( 'Thrust Length', 'solaris' ), 'description' => __( 'Length (px) of the animated engine-thrust flame behind Fighter-Z.', 'solaris' ), 'section' => 'solaris_fighter_z', 'type' => 'number', 'input_attrs' => array( 'min' => 0, 'max' => 200, 'step' => 1 ) ) );

	$wp_customize->add_setting( 'hero_jet_pause_min', array( 'default' => $defaults['hero_jet_pause_min'], 'sanitize_callback' => 'absint' ) );
	$wp_customize->add_setting( 'hero_jet_pause_max', array( 'default' => $defaults['hero_jet_pause_max'], 'sanitize_callback' => 'absint' ) );
	if ( class_exists( 'Solaris_Range_Control' ) ) {
		$wp_customize->add_control(
			new Solaris_Range_Control(
				$wp_customize,
				'hero_jet_pause_range',
				array(
					'label'       => __( 'Next Travel After (seconds)', 'solaris' ),
					'description' => __( 'After each pass, Fighter-Z waits a random number of seconds (between these two values) before flying again.', 'solaris' ),
					'section'     => 'solaris_fighter_z',
					'settings'    => array( 'min' => 'hero_jet_pause_min', 'max' => 'hero_jet_pause_max' ),
					'min_label'   => __( 'From', 'solaris' ),
					'max_label'   => __( 'To', 'solaris' ),
					'range_input_attrs' => array(
						'min' => array( 'min' => 0, 'max' => 120, 'step' => 1 ),
						'max' => array( 'min' => 0, 'max' => 120, 'step' => 1 ),
					),
				)
			)
		);
	}

	$wp_customize->add_setting( 'hero_jet_sound_mode', array( 'default' => $defaults['hero_jet_sound_mode'], 'sanitize_callback' => 'solaris_sanitize_select' ) );
	$wp_customize->add_control(
		'hero_jet_sound_mode',
		array(
			'label'   => __( 'Sound Mode', 'solaris' ),
			'description' => __( 'Overall volume of the Fighter-Z engine sound.', 'solaris' ),
			'section' => 'solaris_fighter_z',
			'type'    => 'select',
			'choices' => array(
				'low'    => __( 'Low', 'solaris' ),
				'medium' => __( 'Medium', 'solaris' ),
				'high'   => __( 'High', 'solaris' ),
			),
		)
	);

	// Stores the selected attachment ID, not a URL — WP_Customize_Media_Control's
	// JS sets the setting to the attachment ID once a real file is picked
	// (a URL default is only used for the unselected placeholder state).
	$wp_customize->add_setting( 'hero_jet_sound', array( 'default' => $defaults['hero_jet_sound'], 'sanitize_callback' => 'absint' ) );
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'hero_jet_sound',
			array(
				'label'       => __( 'Fighter-Z Sound', 'solaris' ),
				'description' => __( 'Pick an audio file from the Media Library. Plays while the plane is on screen; leave blank to use the built-in synthesized engine sound.', 'solaris' ),
				'section'     => 'solaris_fighter_z',
				'mime_type'   => 'audio',
			)
		)
	);

	$wp_customize->add_setting( 'hero_jet_sound_icon_enabled', array( 'default' => $defaults['hero_jet_sound_icon_enabled'], 'sanitize_callback' => 'solaris_sanitize_checkbox' ) );
	$wp_customize->add_control( 'hero_jet_sound_icon_enabled', array( 'label' => __( 'Show Sound Icon', 'solaris' ), 'description' => __( 'The fixed bottom-left speaker button visitors use to toggle the engine sound. Sound itself still auto-unlocks on the visitor\'s first click/tap anywhere even if this is off — this only hides the manual mute control.', 'solaris' ), 'section' => 'solaris_fighter_z', 'type' => 'checkbox' ) );

	/* -------------------------------------------------------------- Fly Birds */
	$wp_customize->add_section(
		'solaris_fly_birds',
		array( 'title' => __( 'Fly Birds', 'solaris' ), 'panel' => 'solaris_options' )
	);

	$wp_customize->add_setting( 'fly_birds_enabled', array( 'default' => $defaults['fly_birds_enabled'], 'sanitize_callback' => 'solaris_sanitize_checkbox' ) );
	$wp_customize->add_control( 'fly_birds_enabled', array( 'label' => __( 'Status', 'solaris' ), 'description' => __( 'A flock of decorative birds gliding across the page.', 'solaris' ), 'section' => 'solaris_fly_birds', 'type' => 'checkbox' ) );

	$wp_customize->add_setting( 'fly_birds_display_mode', array( 'default' => $defaults['fly_birds_display_mode'], 'sanitize_callback' => 'solaris_sanitize_select' ) );
	$wp_customize->add_control(
		'fly_birds_display_mode',
		array(
			'label'   => __( 'Show On', 'solaris' ),
			'section' => 'solaris_fly_birds',
			'type'    => 'select',
			'choices' => array(
				'all_pages'      => __( 'All Pages', 'solaris' ),
				'specific_pages' => __( 'Specific Pages', 'solaris' ),
			),
		)
	);

	$wp_customize->add_setting( 'fly_birds_pages', array( 'default' => $defaults['fly_birds_pages'], 'sanitize_callback' => 'solaris_sanitize_page_ids' ) );
	if ( class_exists( 'Solaris_Page_Checklist_Control' ) ) {
		$wp_customize->add_control(
			new Solaris_Page_Checklist_Control(
				$wp_customize,
				'fly_birds_pages',
				array(
					'label'       => __( 'Specific Pages', 'solaris' ),
					'description' => __( 'Only used when "Show On" above is set to Specific Pages.', 'solaris' ),
					'section'     => 'solaris_fly_birds',
				)
			)
		);
	}

	$wp_customize->add_setting( 'fly_birds_quantity', array( 'default' => $defaults['fly_birds_quantity'], 'sanitize_callback' => 'absint' ) );
	$wp_customize->add_control( 'fly_birds_quantity', array( 'label' => __( 'Bird Quantity', 'solaris' ), 'section' => 'solaris_fly_birds', 'type' => 'number', 'input_attrs' => array( 'min' => 1, 'max' => 30, 'step' => 1 ) ) );

	$wp_customize->add_setting( 'fly_birds_color_mode', array( 'default' => $defaults['fly_birds_color_mode'], 'sanitize_callback' => 'solaris_sanitize_select' ) );
	$wp_customize->add_control(
		'fly_birds_color_mode',
		array(
			'label'   => __( 'Birds Color', 'solaris' ),
			'section' => 'solaris_fly_birds',
			'type'    => 'select',
			'choices' => array(
				'fixed'  => __( 'Fixed Color', 'solaris' ),
				'random' => __( 'Random Colors', 'solaris' ),
			),
		)
	);

	$wp_customize->add_setting( 'fly_birds_color', array( 'default' => $defaults['fly_birds_color'], 'sanitize_callback' => 'sanitize_hex_color' ) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'fly_birds_color', array( 'label' => __( 'Fixed Bird Color', 'solaris' ), 'description' => __( 'Only used when Birds Color above is set to Fixed Color.', 'solaris' ), 'section' => 'solaris_fly_birds' ) ) );

	$wp_customize->add_setting( 'fly_birds_size_mode', array( 'default' => $defaults['fly_birds_size_mode'], 'sanitize_callback' => 'solaris_sanitize_select' ) );
	$wp_customize->add_control(
		'fly_birds_size_mode',
		array(
			'label'   => __( 'Birds Size', 'solaris' ),
			'section' => 'solaris_fly_birds',
			'type'    => 'select',
			'choices' => array(
				'fixed' => __( 'Fixed Size', 'solaris' ),
				'range' => __( 'Random Between a Range', 'solaris' ),
			),
		)
	);

	$wp_customize->add_setting( 'fly_birds_size', array( 'default' => $defaults['fly_birds_size'], 'sanitize_callback' => 'absint' ) );
	$wp_customize->add_control( 'fly_birds_size', array( 'label' => __( 'Fixed Bird Size (px)', 'solaris' ), 'description' => __( 'Only used when Birds Size above is set to Fixed Size.', 'solaris' ), 'section' => 'solaris_fly_birds', 'type' => 'number', 'input_attrs' => array( 'min' => 10, 'max' => 150, 'step' => 1 ) ) );

	$wp_customize->add_setting( 'fly_birds_size_min', array( 'default' => $defaults['fly_birds_size_min'], 'sanitize_callback' => 'absint' ) );
	$wp_customize->add_setting( 'fly_birds_size_max', array( 'default' => $defaults['fly_birds_size_max'], 'sanitize_callback' => 'absint' ) );
	if ( class_exists( 'Solaris_Range_Control' ) ) {
		$wp_customize->add_control(
			new Solaris_Range_Control(
				$wp_customize,
				'fly_birds_size_range',
				array(
					'label'       => __( 'Bird Size Range (px)', 'solaris' ),
					'description' => __( 'Only used when Birds Size above is set to Random Between a Range — each bird picks its own size in this range.', 'solaris' ),
					'section'     => 'solaris_fly_birds',
					'settings'    => array( 'min' => 'fly_birds_size_min', 'max' => 'fly_birds_size_max' ),
					'min_label'   => __( 'From', 'solaris' ),
					'max_label'   => __( 'To', 'solaris' ),
					'range_input_attrs' => array(
						'min' => array( 'min' => 10, 'max' => 150, 'step' => 1 ),
						'max' => array( 'min' => 10, 'max' => 150, 'step' => 1 ),
					),
				)
			)
		);
	}

	$wp_customize->add_setting( 'fly_birds_speed', array( 'default' => $defaults['fly_birds_speed'], 'sanitize_callback' => 'solaris_sanitize_float' ) );
	$wp_customize->add_control( 'fly_birds_speed', array( 'label' => __( 'Flying Speed (seconds to cross)', 'solaris' ), 'description' => __( 'Lower is faster.', 'solaris' ), 'section' => 'solaris_fly_birds', 'type' => 'number', 'input_attrs' => array( 'min' => 3, 'max' => 60, 'step' => 0.5 ) ) );

	$wp_customize->add_setting( 'hero_slides', array( 'default' => $defaults['hero_slides'], 'sanitize_callback' => 'solaris_sanitize_hero_slides' ) );
	if ( class_exists( 'Solaris_Hero_Slides_Control' ) ) {
		$wp_customize->add_control(
			new Solaris_Hero_Slides_Control(
				$wp_customize,
				'hero_slides',
				array(
					'label'       => __( 'Hero Slides', 'solaris' ),
					'description' => __( 'Add one or more images to turn the hero into a slider with dot navigation. Drag the ☷ handle to reorder; uncheck Active to hide a slide without deleting it. With 0 or 1 active slide the theme falls back to the single Hero Background Image above.', 'solaris' ),
					'section'     => 'solaris_hero',
				)
			)
		);
	}

	$wp_customize->add_setting( 'hero_slider_transition', array( 'default' => $defaults['hero_slider_transition'], 'sanitize_callback' => 'solaris_sanitize_select' ) );
	$wp_customize->add_control( 'hero_slider_transition', array(
		'label'   => __( 'Slide Transition Animation', 'solaris' ),
		'description' => __( 'Only affects the hero image slider (2+ active Hero Slides). CSS-only transition, no JS animation loop.', 'solaris' ),
		'section' => 'solaris_hero',
		'type'    => 'select',
		'choices' => array(
			'fade'  => __( 'Fade', 'solaris' ),
			'slide' => __( 'Slide', 'solaris' ),
			'zoom'  => __( 'Zoom', 'solaris' ),
		),
	) );

	$wp_customize->add_setting( 'hero_overlay_opacity', array( 'default' => $defaults['hero_overlay_opacity'], 'sanitize_callback' => 'solaris_sanitize_float', 'transport' => 'postMessage' ) );
	$wp_customize->add_control( 'hero_overlay_opacity', array( 'label' => __( 'Overlay Opacity', 'solaris' ), 'section' => 'solaris_hero', 'type' => 'range', 'input_attrs' => array( 'min' => 0, 'max' => 0.95, 'step' => 0.01 ) ) );

	$hero_text_fields = array(
		'hero_eyebrow'          => array( __( 'Eyebrow Text', 'solaris' ), 'text' ),
		'hero_heading'         => array( __( 'Headline', 'solaris' ), 'text' ),
		'hero_description'     => array( __( 'Supporting Description', 'solaris' ), 'textarea' ),
		'hero_primary_text'    => array( __( 'Primary CTA Text', 'solaris' ), 'text' ),
		'hero_primary_url'     => array( __( 'Primary CTA URL (defaults to Shop page)', 'solaris' ), 'url' ),
		'hero_secondary_text'  => array( __( 'Secondary CTA Text', 'solaris' ), 'text' ),
		'hero_secondary_url'   => array( __( 'Secondary CTA URL', 'solaris' ), 'url' ),
	);
	foreach ( $hero_text_fields as $id => $field ) {
		list( $label, $type ) = $field;
		$sanitize = 'url' === $type ? 'esc_url_raw' : ( 'textarea' === $type ? 'sanitize_textarea_field' : 'sanitize_text_field' );
		$wp_customize->add_setting( $id, array( 'default' => $defaults[ $id ], 'sanitize_callback' => $sanitize, 'transport' => 'postMessage' ) );
		$wp_customize->add_control( $id, array( 'label' => $label, 'section' => 'solaris_hero', 'type' => $type ) );
	}

	$wp_customize->add_setting( 'hero_content_animation', array( 'default' => $defaults['hero_content_animation'], 'sanitize_callback' => 'solaris_sanitize_select' ) );
	$wp_customize->add_control( 'hero_content_animation', array(
		'label'       => __( 'Hero Content Animation', 'solaris' ),
		'description' => __( 'Entrance animation for the eyebrow/title/subtext/buttons block. Replays each time the slide changes, if using the Hero Slides slider.', 'solaris' ),
		'section'     => 'solaris_hero',
		'type'        => 'select',
		'choices'     => array(
			'none'        => __( 'None', 'solaris' ),
			'fade-up'     => __( 'Fade Up', 'solaris' ),
			'fade-down'   => __( 'Fade Down', 'solaris' ),
			'zoom-in'     => __( 'Zoom In', 'solaris' ),
			'slide-left'  => __( 'Slide From Left', 'solaris' ),
			'slide-right' => __( 'Slide From Right', 'solaris' ),
		),
	) );

	$wp_customize->add_setting( 'hero_solar_system_enabled', array( 'default' => $defaults['hero_solar_system_enabled'], 'sanitize_callback' => 'solaris_sanitize_checkbox' ) );
	$wp_customize->add_control( 'hero_solar_system_enabled', array( 'label' => __( 'Show Animated Solar System', 'solaris' ), 'description' => __( 'Decorative, transparent, absolutely-positioned CSS orbit animation on the right side of the hero. Hidden on tablet/mobile and for users who prefer reduced motion.', 'solaris' ), 'section' => 'solaris_hero', 'type' => 'checkbox' ) );

	/* --------------------------------------------------------- Stats section */
	$wp_customize->add_section(
		'solaris_stats',
		array( 'title' => __( 'Homepage Stats', 'solaris' ), 'panel' => 'solaris_options', 'description' => __( 'Placeholder figures — replace with verified business figures before launch.', 'solaris' ) )
	);
	for ( $i = 1; $i <= 4; $i++ ) {
		$wp_customize->add_setting( "stat_{$i}_value", array( 'default' => $defaults["stat_{$i}_value"], 'sanitize_callback' => 'sanitize_text_field' ) );
		$wp_customize->add_control( "stat_{$i}_value", array( 'label' => sprintf( __( 'Stat %d Value', 'solaris' ), $i ), 'section' => 'solaris_stats', 'type' => 'text' ) );

		$wp_customize->add_setting( "stat_{$i}_label", array( 'default' => $defaults["stat_{$i}_label"], 'sanitize_callback' => 'sanitize_text_field' ) );
		$wp_customize->add_control( "stat_{$i}_label", array( 'label' => sprintf( __( 'Stat %d Label', 'solaris' ), $i ), 'section' => 'solaris_stats', 'type' => 'text' ) );
	}

	/* ------------------------------------------------------ Homepage blog posts */
	$wp_customize->add_section(
		'solaris_home_blog',
		array( 'title' => __( 'Blog Posts Section', 'solaris' ), 'panel' => 'solaris_options', 'description' => __( 'Latest posts shown on the homepage, above "Browse By Category".', 'solaris' ) )
	);

	$wp_customize->add_setting( 'home_blog_enabled', array( 'default' => $defaults['home_blog_enabled'], 'sanitize_callback' => 'solaris_sanitize_checkbox' ) );
	$wp_customize->add_control( 'home_blog_enabled', array( 'label' => __( 'Show Blog Posts Section', 'solaris' ), 'section' => 'solaris_home_blog', 'type' => 'checkbox' ) );

	$wp_customize->add_setting( 'home_blog_eyebrow', array( 'default' => $defaults['home_blog_eyebrow'], 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control( 'home_blog_eyebrow', array( 'label' => __( 'Eyebrow Text', 'solaris' ), 'section' => 'solaris_home_blog', 'type' => 'text' ) );

	$wp_customize->add_setting( 'home_blog_heading', array( 'default' => $defaults['home_blog_heading'], 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control( 'home_blog_heading', array( 'label' => __( 'Section Heading', 'solaris' ), 'section' => 'solaris_home_blog', 'type' => 'text' ) );

	$wp_customize->add_setting( 'home_blog_categories', array( 'default' => $defaults['home_blog_categories'], 'sanitize_callback' => 'solaris_sanitize_category_ids' ) );
	if ( class_exists( 'Solaris_Category_Checklist_Control' ) ) {
		$wp_customize->add_control(
			new Solaris_Category_Checklist_Control(
				$wp_customize,
				'home_blog_categories',
				array(
					'label'       => __( 'Categories', 'solaris' ),
					'description' => __( 'Show posts from these categories only. Leave all unticked to show posts from every category.', 'solaris' ),
					'section'     => 'solaris_home_blog',
				)
			)
		);
	}

	$wp_customize->add_setting( 'home_blog_count', array( 'default' => $defaults['home_blog_count'], 'sanitize_callback' => 'absint' ) );
	$wp_customize->add_control( 'home_blog_count', array( 'label' => __( 'Number of Posts', 'solaris' ), 'section' => 'solaris_home_blog', 'type' => 'number', 'input_attrs' => array( 'min' => 1, 'max' => 12, 'step' => 1 ) ) );

	$wp_customize->add_setting( 'home_blog_show_button', array( 'default' => $defaults['home_blog_show_button'], 'sanitize_callback' => 'solaris_sanitize_checkbox' ) );
	$wp_customize->add_control( 'home_blog_show_button', array( 'label' => __( 'Show "Show All" Button', 'solaris' ), 'section' => 'solaris_home_blog', 'type' => 'checkbox' ) );

	$wp_customize->add_setting( 'home_blog_button_text', array( 'default' => $defaults['home_blog_button_text'], 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control( 'home_blog_button_text', array( 'label' => __( 'Button Text', 'solaris' ), 'section' => 'solaris_home_blog', 'type' => 'text' ) );

	$wp_customize->add_setting( 'home_blog_button_url', array( 'default' => $defaults['home_blog_button_url'], 'sanitize_callback' => 'esc_url_raw' ) );
	$wp_customize->add_control( 'home_blog_button_url', array( 'label' => __( 'Button URL', 'solaris' ), 'description' => __( 'Leave blank to link automatically: the category page when one category is selected, otherwise the Blog page.', 'solaris' ), 'section' => 'solaris_home_blog', 'type' => 'url' ) );

	/* ------------------------------------------------------ Featured products */
	$wp_customize->add_section(
		'solaris_featured',
		array( 'title' => __( 'Featured Products Section', 'solaris' ), 'panel' => 'solaris_options' )
	);
	$wp_customize->add_setting( 'featured_products_heading', array( 'default' => $defaults['featured_products_heading'], 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control( 'featured_products_heading', array( 'label' => __( 'Section Heading', 'solaris' ), 'section' => 'solaris_featured', 'type' => 'text' ) );

	solaris_register_section_title_color_controls( $wp_customize, 'solaris_featured', 'featured_products', $defaults );
	solaris_register_section_bg_layer_controls( $wp_customize, 'solaris_featured', 'featured_products', $defaults );

	$wp_customize->add_setting( 'featured_products_source', array( 'default' => $defaults['featured_products_source'], 'sanitize_callback' => 'solaris_sanitize_select' ) );
	$wp_customize->add_control(
		'featured_products_source',
		array(
			'label'   => __( 'Product Source', 'solaris' ),
			'section' => 'solaris_featured',
			'type'    => 'select',
			'choices' => array(
				'featured'      => __( 'Featured Products', 'solaris' ),
				'best-selling'  => __( 'Best Sellers', 'solaris' ),
				'latest'        => __( 'Latest Products', 'solaris' ),
			),
		)
	);

	$wp_customize->add_setting( 'featured_products_count', array( 'default' => $defaults['featured_products_count'], 'sanitize_callback' => 'absint' ) );
	$wp_customize->add_control( 'featured_products_count', array( 'label' => __( 'Number of Products', 'solaris' ), 'section' => 'solaris_featured', 'type' => 'number', 'input_attrs' => array( 'min' => 4, 'max' => 16, 'step' => 4 ) ) );

	/* --------------------------------------------------------- Why Choose Us */
	$wp_customize->add_section(
		'solaris_why_choose',
		array( 'title' => __( 'Why Choose Us Section', 'solaris' ), 'panel' => 'solaris_options' )
	);

	solaris_register_section_bg_controls( $wp_customize, 'solaris_why_choose', 'why_choose', __( 'Title', 'solaris' ), $defaults );

	$wp_customize->add_setting( 'why_choose_card_bg_color', array( 'default' => $defaults['why_choose_card_bg_color'], 'sanitize_callback' => 'sanitize_hex_color' ) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'why_choose_card_bg_color', array( 'label' => __( 'Card Background Color', 'solaris' ), 'section' => 'solaris_why_choose' ) ) );

	$wp_customize->add_setting( 'why_choose_card_border_color', array( 'default' => $defaults['why_choose_card_border_color'], 'sanitize_callback' => 'sanitize_hex_color' ) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'why_choose_card_border_color', array( 'label' => __( 'Card Border Color', 'solaris' ), 'section' => 'solaris_why_choose' ) ) );

	$wp_customize->add_setting( 'why_choose_card_text_color', array( 'default' => $defaults['why_choose_card_text_color'], 'sanitize_callback' => 'sanitize_hex_color' ) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'why_choose_card_text_color', array( 'label' => __( 'Card Text Color', 'solaris' ), 'section' => 'solaris_why_choose' ) ) );

	$wp_customize->add_setting( 'why_choose_card_icon_bg_color', array( 'default' => $defaults['why_choose_card_icon_bg_color'], 'sanitize_callback' => 'sanitize_hex_color' ) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'why_choose_card_icon_bg_color', array( 'label' => __( 'Card Icon Background Color', 'solaris' ), 'section' => 'solaris_why_choose' ) ) );

	$wp_customize->add_setting( 'why_choose_card_icon_color', array( 'default' => $defaults['why_choose_card_icon_color'], 'sanitize_callback' => 'sanitize_hex_color' ) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'why_choose_card_icon_color', array( 'label' => __( 'Card Icon Color', 'solaris' ), 'section' => 'solaris_why_choose' ) ) );

	$wp_customize->add_setting( 'why_choose_card_hover_effect', array( 'default' => $defaults['why_choose_card_hover_effect'], 'sanitize_callback' => 'solaris_sanitize_select' ) );
	$wp_customize->add_control(
		'why_choose_card_hover_effect',
		array(
			'label'   => __( 'Card Hover Effect', 'solaris' ),
			'section' => 'solaris_why_choose',
			'type'    => 'select',
			'choices' => array(
				'lift'   => __( 'Lift', 'solaris' ),
				'zoom'   => __( 'Zoom Icon', 'solaris' ),
				'glow'   => __( 'Glow', 'solaris' ),
				'border' => __( 'Border Highlight', 'solaris' ),
				'none'   => __( 'None', 'solaris' ),
			),
		)
	);

	$wp_customize->add_setting( 'why_choose_card_animation', array( 'default' => $defaults['why_choose_card_animation'], 'sanitize_callback' => 'solaris_sanitize_select' ) );
	$wp_customize->add_control(
		'why_choose_card_animation',
		array(
			'label'   => __( 'Card Entrance Animation', 'solaris' ),
			'section' => 'solaris_why_choose',
			'type'    => 'select',
			'choices' => array(
				'fade-up'    => __( 'Fade Up', 'solaris' ),
				'zoom-in'    => __( 'Zoom In', 'solaris' ),
				'slide-left' => __( 'Slide From Left', 'solaris' ),
				'slide-right' => __( 'Slide From Right', 'solaris' ),
				'none'       => __( 'None', 'solaris' ),
			),
		)
	);

	/* ----------------------------------------------------------- Solar Solutions */
	$wp_customize->add_section( 'solaris_solutions_style', array( 'title' => __( 'Solar Solutions Section', 'solaris' ), 'panel' => 'solaris_options' ) );
	solaris_register_section_bg_controls( $wp_customize, 'solaris_solutions_style', 'solutions', __( 'Title', 'solaris' ), $defaults );

	$wp_customize->add_setting( 'solutions_card_overlay_color', array( 'default' => $defaults['solutions_card_overlay_color'], 'sanitize_callback' => 'sanitize_hex_color' ) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'solutions_card_overlay_color', array( 'label' => __( 'Card Photo Overlay Color', 'solaris' ), 'description' => __( 'Tint of the dark gradient over each card\'s photo.', 'solaris' ), 'section' => 'solaris_solutions_style' ) ) );

	$wp_customize->add_setting( 'solutions_card_border_color', array( 'default' => $defaults['solutions_card_border_color'], 'sanitize_callback' => 'sanitize_hex_color' ) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'solutions_card_border_color', array( 'label' => __( 'Card Border Color', 'solaris' ), 'section' => 'solaris_solutions_style' ) ) );

	$wp_customize->add_setting( 'solutions_card_text_color', array( 'default' => $defaults['solutions_card_text_color'], 'sanitize_callback' => 'sanitize_hex_color' ) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'solutions_card_text_color', array( 'label' => __( 'Card Text Color', 'solaris' ), 'section' => 'solaris_solutions_style' ) ) );

	$wp_customize->add_setting( 'solutions_card_icon_bg_color', array( 'default' => $defaults['solutions_card_icon_bg_color'], 'sanitize_callback' => 'sanitize_hex_color' ) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'solutions_card_icon_bg_color', array( 'label' => __( 'Card Icon Background Color', 'solaris' ), 'section' => 'solaris_solutions_style' ) ) );

	$wp_customize->add_setting( 'solutions_card_icon_color', array( 'default' => $defaults['solutions_card_icon_color'], 'sanitize_callback' => 'sanitize_hex_color' ) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'solutions_card_icon_color', array( 'label' => __( 'Card Icon Color', 'solaris' ), 'section' => 'solaris_solutions_style' ) ) );

	solaris_register_select_control( $wp_customize, 'solaris_solutions_style', 'solutions_card_hover_effect', __( 'Card Hover Effect', 'solaris' ), $defaults['solutions_card_hover_effect'], array(
		'none'   => __( 'None', 'solaris' ),
		'lift'   => __( 'Lift', 'solaris' ),
		'zoom'   => __( 'Zoom Icon', 'solaris' ),
		'glow'   => __( 'Glow', 'solaris' ),
		'border' => __( 'Border Highlight', 'solaris' ),
	) );
	solaris_register_select_control( $wp_customize, 'solaris_solutions_style', 'solutions_card_animation', __( 'Card Entrance Animation', 'solaris' ), $defaults['solutions_card_animation'], solaris_animation_choices() );

	/* --------------------------------------------------- Customer Stories */
	$wp_customize->add_section( 'solaris_testimonials_style', array( 'title' => __( 'Customer Stories Section', 'solaris' ), 'panel' => 'solaris_options' ) );
	solaris_register_section_bg_controls( $wp_customize, 'solaris_testimonials_style', 'testimonials', __( 'Title', 'solaris' ), $defaults );

	$wp_customize->add_setting( 'testimonials_card_bg_color', array( 'default' => $defaults['testimonials_card_bg_color'], 'sanitize_callback' => 'sanitize_hex_color' ) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'testimonials_card_bg_color', array( 'label' => __( 'Card Background Color', 'solaris' ), 'section' => 'solaris_testimonials_style' ) ) );

	$wp_customize->add_setting( 'testimonials_card_border_color', array( 'default' => $defaults['testimonials_card_border_color'], 'sanitize_callback' => 'sanitize_hex_color' ) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'testimonials_card_border_color', array( 'label' => __( 'Card Border Color', 'solaris' ), 'section' => 'solaris_testimonials_style' ) ) );

	$wp_customize->add_setting( 'testimonials_card_text_color', array( 'default' => $defaults['testimonials_card_text_color'], 'sanitize_callback' => 'sanitize_hex_color' ) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'testimonials_card_text_color', array( 'label' => __( 'Quote Text Color', 'solaris' ), 'section' => 'solaris_testimonials_style' ) ) );

	$wp_customize->add_setting( 'testimonials_card_avatar_bg_color', array( 'default' => $defaults['testimonials_card_avatar_bg_color'], 'sanitize_callback' => 'sanitize_hex_color' ) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'testimonials_card_avatar_bg_color', array( 'label' => __( 'Avatar Background Color', 'solaris' ), 'section' => 'solaris_testimonials_style' ) ) );

	$wp_customize->add_setting( 'testimonials_card_avatar_text_color', array( 'default' => $defaults['testimonials_card_avatar_text_color'], 'sanitize_callback' => 'sanitize_hex_color' ) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'testimonials_card_avatar_text_color', array( 'label' => __( 'Avatar Initial Color', 'solaris' ), 'section' => 'solaris_testimonials_style' ) ) );

	solaris_register_select_control( $wp_customize, 'solaris_testimonials_style', 'testimonials_card_hover_effect', __( 'Card Hover Effect', 'solaris' ), $defaults['testimonials_card_hover_effect'], array(
		'none'   => __( 'None', 'solaris' ),
		'lift'   => __( 'Lift', 'solaris' ),
		'zoom'   => __( 'Zoom Avatar', 'solaris' ),
		'glow'   => __( 'Glow', 'solaris' ),
		'border' => __( 'Border Highlight', 'solaris' ),
	) );
	solaris_register_select_control( $wp_customize, 'solaris_testimonials_style', 'testimonials_card_animation', __( 'Card Entrance Animation', 'solaris' ), $defaults['testimonials_card_animation'], solaris_animation_choices() );

	/* ------------------------------------------------------------------- FAQ */
	$wp_customize->add_section( 'solaris_faq_style', array( 'title' => __( 'Frequently Asked Questions', 'solaris' ), 'panel' => 'solaris_options' ) );
	solaris_register_section_bg_controls( $wp_customize, 'solaris_faq_style', 'faq', __( 'Title', 'solaris' ), $defaults );

	$wp_customize->add_setting( 'faq_items', array( 'default' => $defaults['faq_items'], 'sanitize_callback' => 'solaris_sanitize_faq_items' ) );
	if ( class_exists( 'Solaris_Faq_Items_Control' ) ) {
		$wp_customize->add_control(
			new Solaris_Faq_Items_Control(
				$wp_customize,
				'faq_items',
				array(
					'label'       => __( 'Questions & Answers', 'solaris' ),
					'description' => __( 'Drag the ☷ handle to reorder. Add or remove questions freely.', 'solaris' ),
					'section'     => 'solaris_faq_style',
				)
			)
		);
	}

	$wp_customize->add_setting( 'faq_item_bg_color', array( 'default' => $defaults['faq_item_bg_color'], 'sanitize_callback' => 'sanitize_hex_color' ) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'faq_item_bg_color', array( 'label' => __( 'Question Row Background Color', 'solaris' ), 'section' => 'solaris_faq_style' ) ) );

	$wp_customize->add_setting( 'faq_item_border_color', array( 'default' => $defaults['faq_item_border_color'], 'sanitize_callback' => 'sanitize_hex_color' ) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'faq_item_border_color', array( 'label' => __( 'Divider Border Color', 'solaris' ), 'section' => 'solaris_faq_style' ) ) );

	$wp_customize->add_setting( 'faq_item_text_color', array( 'default' => $defaults['faq_item_text_color'], 'sanitize_callback' => 'sanitize_hex_color' ) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'faq_item_text_color', array( 'label' => __( 'Question Text Color', 'solaris' ), 'section' => 'solaris_faq_style' ) ) );

	$wp_customize->add_setting( 'faq_icon_color', array( 'default' => $defaults['faq_icon_color'], 'sanitize_callback' => 'sanitize_hex_color' ) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'faq_icon_color', array( 'label' => __( 'Plus Icon Color', 'solaris' ), 'section' => 'solaris_faq_style' ) ) );

	solaris_register_select_control( $wp_customize, 'solaris_faq_style', 'faq_hover_effect', __( 'Question Hover Effect', 'solaris' ), $defaults['faq_hover_effect'], array(
		'none'        => __( 'None', 'solaris' ),
		'background'  => __( 'Background Tint', 'solaris' ),
		'accent-text' => __( 'Accent Text Color', 'solaris' ),
		'border'      => __( 'Accent Left Border', 'solaris' ),
	) );
	solaris_register_select_control( $wp_customize, 'solaris_faq_style', 'faq_animation', __( 'Item Entrance Animation', 'solaris' ), $defaults['faq_animation'], solaris_animation_choices() );

	/* -------------------------------------------------------- Promo + final CTA */
	$wp_customize->add_section( 'solaris_promo', array( 'title' => __( 'Promotional Banner', 'solaris' ), 'panel' => 'solaris_options' ) );
	$promo_fields = array(
		'promo_heading'     => array( __( 'Heading', 'solaris' ), 'text' ),
		'promo_text'        => array( __( 'Text', 'solaris' ), 'textarea' ),
		'promo_button_text' => array( __( 'Button Text', 'solaris' ), 'text' ),
		'promo_button_url'  => array( __( 'Button URL (defaults to Shop page)', 'solaris' ), 'url' ),
	);
	foreach ( $promo_fields as $id => $field ) {
		list( $label, $type ) = $field;
		$sanitize = 'url' === $type ? 'esc_url_raw' : ( 'textarea' === $type ? 'sanitize_textarea_field' : 'sanitize_text_field' );
		$wp_customize->add_setting( $id, array( 'default' => $defaults[ $id ], 'sanitize_callback' => $sanitize ) );
		$wp_customize->add_control( $id, array( 'label' => $label, 'section' => 'solaris_promo', 'type' => $type ) );
	}

	$wp_customize->add_section( 'solaris_final_cta', array( 'title' => __( 'Final CTA Section', 'solaris' ), 'panel' => 'solaris_options' ) );
	$final_cta_fields = array(
		'final_cta_heading'     => array( __( 'Heading', 'solaris' ), 'text' ),
		'final_cta_text'        => array( __( 'Text', 'solaris' ), 'textarea' ),
		'final_cta_button_text' => array( __( 'Button Text', 'solaris' ), 'text' ),
		'final_cta_button_url'  => array( __( 'Button URL (defaults to Shop page)', 'solaris' ), 'url' ),
	);
	foreach ( $final_cta_fields as $id => $field ) {
		list( $label, $type ) = $field;
		$sanitize = 'url' === $type ? 'esc_url_raw' : ( 'textarea' === $type ? 'sanitize_textarea_field' : 'sanitize_text_field' );
		$wp_customize->add_setting( $id, array( 'default' => $defaults[ $id ], 'sanitize_callback' => $sanitize ) );
		$wp_customize->add_control( $id, array( 'label' => $label, 'section' => 'solaris_final_cta', 'type' => $type ) );
	}

	/* --------------------------------------------------------------- Contact */
	$wp_customize->add_section( 'solaris_contact', array( 'title' => __( 'Contact Information', 'solaris' ), 'panel' => 'solaris_options' ) );
	$contact_fields = array(
		'contact_phone'   => array( __( 'Phone Number', 'solaris' ), 'text' ),
		'contact_email'   => array( __( 'Email Address', 'solaris' ), 'email' ),
		'contact_address' => array( __( 'Address', 'solaris' ), 'textarea' ),
		'contact_hours'   => array( __( 'Business Hours', 'solaris' ), 'text' ),
	);
	foreach ( $contact_fields as $id => $field ) {
		list( $label, $type ) = $field;
		$sanitize = 'email' === $type ? 'sanitize_email' : ( 'textarea' === $type ? 'sanitize_textarea_field' : 'sanitize_text_field' );
		$wp_customize->add_setting( $id, array( 'default' => $defaults[ $id ], 'sanitize_callback' => $sanitize ) );
		$wp_customize->add_control( $id, array( 'label' => $label, 'section' => 'solaris_contact', 'type' => 'email' === $type ? 'text' : $type ) );
	}

	/* ---------------------------------------------------------------- Social */
	$wp_customize->add_section( 'solaris_social', array( 'title' => __( 'Social Links', 'solaris' ), 'panel' => 'solaris_options' ) );
	$social_fields = array(
		'social_facebook'  => 'Facebook',
		'social_twitter'   => 'X / Twitter',
		'social_instagram' => 'Instagram',
		'social_linkedin'  => 'LinkedIn',
		'social_youtube'   => 'YouTube',
	);
	foreach ( $social_fields as $id => $label ) {
		$wp_customize->add_setting( $id, array( 'default' => $defaults[ $id ], 'sanitize_callback' => 'esc_url_raw' ) );
		$wp_customize->add_control( $id, array( 'label' => $label . ' ' . __( 'URL', 'solaris' ), 'section' => 'solaris_social', 'type' => 'url' ) );
	}

	/* ---------------------------------------------------- Product card options */
	$wp_customize->add_section(
		'solaris_product_cards',
		array(
			'title'       => __( 'Product Cards', 'solaris' ),
			'panel'       => 'solaris_options',
			'description' => __( 'Options for the product cards shown on the shop, category and homepage Featured Products grids.', 'solaris' ),
		)
	);
	$wp_customize->add_setting( 'quick_view_enabled', array( 'default' => $defaults['quick_view_enabled'], 'sanitize_callback' => 'solaris_sanitize_checkbox' ) );
	$wp_customize->add_control(
		'quick_view_enabled',
		array(
			'label'       => __( 'Show Quick View Button', 'solaris' ),
			'description' => __( 'An eye icon on each card that opens the product\'s image, price, short description and "Add to cart" in a modal, without leaving the list.', 'solaris' ),
			'section'     => 'solaris_product_cards',
			'type'        => 'checkbox',
		)
	);
	$wp_customize->add_setting( 'loop_quantity_enabled', array( 'default' => $defaults['loop_quantity_enabled'], 'sanitize_callback' => 'solaris_sanitize_checkbox' ) );
	$wp_customize->add_control(
		'loop_quantity_enabled',
		array(
			'label'       => __( 'Show Quantity Input', 'solaris' ),
			'description' => __( 'Lets shoppers pick a quantity next to the price before clicking "Add to cart", instead of always adding 1.', 'solaris' ),
			'section'     => 'solaris_product_cards',
			'type'        => 'checkbox',
		)
	);
	$wp_customize->add_setting( 'qty_stepper_enabled', array( 'default' => $defaults['qty_stepper_enabled'], 'sanitize_callback' => 'solaris_sanitize_checkbox' ) );
	$wp_customize->add_control(
		'qty_stepper_enabled',
		array(
			'label'       => __( 'Show +/- Quantity Buttons', 'solaris' ),
			'description' => __( 'Adds +/- stepper buttons beside every quantity input site-wide — product list, Quick View, and the single-product page.', 'solaris' ),
			'section'     => 'solaris_product_cards',
			'type'        => 'checkbox',
		)
	);
	$wp_customize->add_setting( 'show_view_cart_link', array( 'default' => $defaults['show_view_cart_link'], 'sanitize_callback' => 'solaris_sanitize_checkbox' ) );
	$wp_customize->add_control(
		'show_view_cart_link',
		array(
			'label'       => __( 'Show "View Cart" Link After Add to Cart', 'solaris' ),
			'description' => __( 'The link WooCommerce shows next to the button right after an AJAX add-to-cart succeeds.', 'solaris' ),
			'section'     => 'solaris_product_cards',
			'type'        => 'checkbox',
		)
	);
	$wp_customize->add_setting( 'fly_to_cart_enabled', array( 'default' => $defaults['fly_to_cart_enabled'], 'sanitize_callback' => 'solaris_sanitize_checkbox' ) );
	$wp_customize->add_control(
		'fly_to_cart_enabled',
		array(
			'label'       => __( 'Show Animated Add to Cart?', 'solaris' ),
			'description' => __( 'A clone of the product image flies from the card to the header cart icon when "Add to cart" is clicked.', 'solaris' ),
			'section'     => 'solaris_product_cards',
			'type'        => 'checkbox',
		)
	);
	$wp_customize->add_setting( 'fly_to_cart_multi_enabled', array( 'default' => $defaults['fly_to_cart_multi_enabled'], 'sanitize_callback' => 'solaris_sanitize_checkbox' ) );
	$wp_customize->add_control(
		'fly_to_cart_multi_enabled',
		array(
			'label'       => __( 'Allow Multiple for Multi Quantity?', 'solaris' ),
			'description' => __( 'The number of animations matches the quantity added — e.g. quantity 3 flies 3 image clones to the cart icon instead of just 1 (capped at 10 per add). Only applies when "Show Animated Add to Cart?" above is on.', 'solaris' ),
			'section'     => 'solaris_product_cards',
			'type'        => 'checkbox',
		)
	);
	$wp_customize->add_setting( 'fly_to_cart_clone_bg_color', array( 'default' => $defaults['fly_to_cart_clone_bg_color'], 'sanitize_callback' => 'sanitize_hex_color' ) );
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'fly_to_cart_clone_bg_color',
			array(
				'label'       => __( 'Cloned Image Background', 'solaris' ),
				'description' => __( 'Fills in behind the flying product-image clone — visible through any transparent parts of the image.', 'solaris' ),
				'section'     => 'solaris_product_cards',
			)
		)
	);
	$wp_customize->add_setting( 'fly_to_cart_clone_shadow', array( 'default' => $defaults['fly_to_cart_clone_shadow'], 'sanitize_callback' => 'absint' ) );
	$wp_customize->add_control(
		'fly_to_cart_clone_shadow',
		array(
			'label'       => __( 'Cloned Image Box Shadow', 'solaris' ),
			'description' => __( '0 = no shadow, 10 = strongest.', 'solaris' ),
			'section'     => 'solaris_product_cards',
			'type'        => 'range',
			'input_attrs' => array( 'min' => 0, 'max' => 10, 'step' => 1 ),
		)
	);
	$wp_customize->add_setting( 'cart_click_action', array( 'default' => $defaults['cart_click_action'], 'sanitize_callback' => 'solaris_sanitize_select' ) );
	$wp_customize->add_control(
		'cart_click_action',
		array(
			'label'       => __( 'Cart Count From Header — Select Action', 'solaris' ),
			'description' => __( 'What happens when the cart icon in the header is clicked.', 'solaris' ),
			'section'     => 'solaris_product_cards',
			'type'        => 'select',
			'choices'     => array(
				'page'     => __( 'Go to Cart Page', 'solaris' ),
				'popup'    => __( 'Popup View', 'solaris' ),
				'sidebar'  => __( 'Sidebar View', 'solaris' ),
				'modal'    => __( 'Modal View', 'solaris' ),
				'slide-up' => __( 'Slide Up View', 'solaris' ),
			),
		)
	);
	$wp_customize->add_setting( 'open_cart_panel_on_add_to_cart', array( 'default' => $defaults['open_cart_panel_on_add_to_cart'], 'sanitize_callback' => 'solaris_sanitize_checkbox' ) );
	$wp_customize->add_control(
		'open_cart_panel_on_add_to_cart',
		array(
			'label'       => __( 'After Add to Cart, Open Minicart?', 'solaris' ),
			'description' => __( 'Only applies when "Cart Count From Header" above isn\'t "Go to Cart Page" — there\'s no minicart to open otherwise.', 'solaris' ),
			'section'     => 'solaris_product_cards',
			'type'        => 'checkbox',
		)
	);
	$wp_customize->add_setting( 'sticky_cart_buttons', array( 'default' => $defaults['sticky_cart_buttons'], 'sanitize_callback' => 'solaris_sanitize_checkbox' ) );
	$wp_customize->add_control(
		'sticky_cart_buttons',
		array(
			'label'       => __( 'Sticky View Cart / Checkout Buttons?', 'solaris' ),
			'description' => __( 'Keeps the View Cart / Checkout buttons always visible at the bottom of the minicart instead of scrolling away with a long cart. Only applies when "Cart Count From Header" above isn\'t "Go to Cart Page".', 'solaris' ),
			'section'     => 'solaris_product_cards',
			'type'        => 'checkbox',
		)
	);

	/* ------------------------------------------------------ Products list */
	$wp_customize->add_section(
		'solaris_products_list',
		array(
			'title'       => __( 'Products List', 'solaris' ),
			'panel'       => 'solaris_options',
			'description' => __( 'Category sidebar, grid columns, and pagination behavior for the shop/category archive.', 'solaris' ),
		)
	);
	$wp_customize->add_setting( 'shop_show_categories', array( 'default' => $defaults['shop_show_categories'], 'sanitize_callback' => 'solaris_sanitize_checkbox' ) );
	$wp_customize->add_control(
		'shop_show_categories',
		array(
			'label'       => __( 'Show Categories for list Page?', 'solaris' ),
			'description' => __( 'Shows the category sidebar on the shop page and on category archive pages.', 'solaris' ),
			'section' => 'solaris_products_list',
			'type'    => 'checkbox',
		)
	);
	$wp_customize->add_setting( 'shop_show_child_categories', array( 'default' => $defaults['shop_show_child_categories'], 'sanitize_callback' => 'solaris_sanitize_checkbox' ) );
	$wp_customize->add_control(
		'shop_show_child_categories',
		array(
			'label'       => __( 'Show Child Categories?', 'solaris' ),
			'description' => __( 'When enabled, child/subcategories will be displayed under their respective parent categories.', 'solaris' ),
			'section'     => 'solaris_products_list',
			'type'        => 'checkbox',
		)
	);
	$wp_customize->add_setting( 'shop_category_position', array( 'default' => $defaults['shop_category_position'], 'sanitize_callback' => 'solaris_sanitize_select' ) );
	$wp_customize->add_control(
		'shop_category_position',
		array(
			'label'   => __( 'Category List Position', 'solaris' ),
			'section' => 'solaris_products_list',
			'type'    => 'select',
			'choices' => array(
				'left'  => __( 'Left', 'solaris' ),
				'right' => __( 'Right', 'solaris' ),
			),
		)
	);
	$wp_customize->add_setting( 'shop_columns_desktop', array( 'default' => $defaults['shop_columns_desktop'], 'sanitize_callback' => 'solaris_sanitize_select' ) );
	$wp_customize->add_control(
		'shop_columns_desktop',
		array(
			'label'       => __( 'Products Per Row — Desktop', 'solaris' ),
			'section'     => 'solaris_products_list',
			'type'        => 'select',
			'choices'     => array_combine( range( 2, 6 ), range( 2, 6 ) ),
		)
	);
	$wp_customize->add_setting( 'shop_columns_mobile', array( 'default' => $defaults['shop_columns_mobile'], 'sanitize_callback' => 'solaris_sanitize_select' ) );
	$wp_customize->add_control(
		'shop_columns_mobile',
		array(
			'label'   => __( 'Products Per Row — Mobile', 'solaris' ),
			'section' => 'solaris_products_list',
			'type'    => 'select',
			'choices' => array_combine( range( 1, 3 ), range( 1, 3 ) ),
		)
	);
	$wp_customize->add_setting( 'shop_loading_mode', array( 'default' => $defaults['shop_loading_mode'], 'sanitize_callback' => 'solaris_sanitize_select' ) );
	$wp_customize->add_control(
		'shop_loading_mode',
		array(
			'label'       => __( 'Products Loading Mode', 'solaris' ),
			'description' => __( 'Pagination: standard page-by-page navigation. Lazy Load: more products load automatically as the shopper scrolls near the bottom. Load More: a button loads more products without a page refresh.', 'solaris' ),
			'section'     => 'solaris_products_list',
			'type'        => 'select',
			'choices'     => array(
				'pagination' => __( 'Pagination', 'solaris' ),
				'lazy_load'  => __( 'Lazy Load', 'solaris' ),
				'load_more'  => __( 'Load More', 'solaris' ),
			),
		)
	);
	$wp_customize->add_setting( 'shop_products_per_page', array( 'default' => $defaults['shop_products_per_page'], 'sanitize_callback' => 'solaris_sanitize_products_per_page' ) );
	$wp_customize->add_control(
		'shop_products_per_page',
		array(
			'label'       => __( 'Products Per Page', 'solaris' ),
			'description' => __( 'How many products load per page (Pagination) or per batch (Lazy Load / Load More).', 'solaris' ),
			'section'     => 'solaris_products_list',
			'type'        => 'number',
			'input_attrs' => array( 'min' => 1, 'max' => 100, 'step' => 1 ),
		)
	);

	/*
	 * Product card animation — merged into the "Product Cards" section
	 * above rather than its own section. Each card fades/slides in as it
	 * scrolls into view; cards further right in a row use duration/delay =
	 * base + (position − 1) × step, so the row cascades in left-to-right.
	 */
	$animation_fields = array(
		'reveal_base_duration' => array( __( 'Base Duration (seconds)', 'solaris' ), 0, 2 ),
		'reveal_duration_step' => array( __( 'Duration Increment Per Card (seconds)', 'solaris' ), 0, 1 ),
		'reveal_base_delay'    => array( __( 'Base Delay (seconds)', 'solaris' ), 0, 2 ),
		'reveal_delay_step'    => array( __( 'Delay Increment Per Card (seconds)', 'solaris' ), 0, 1 ),
	);
	foreach ( $animation_fields as $id => $field ) {
		list( $label, $min, $max ) = $field;
		$wp_customize->add_setting( $id, array( 'default' => $defaults[ $id ], 'sanitize_callback' => 'solaris_sanitize_float' ) );
		$wp_customize->add_control(
			$id,
			array(
				'label'       => $label,
				'section'     => 'solaris_product_cards',
				'type'        => 'range',
				'input_attrs' => array( 'min' => $min, 'max' => $max, 'step' => 0.05 ),
			)
		);
	}
}
add_action( 'customize_register', 'solaris_customize_register' );

/**
 * Build the CSS custom-property override block from the current Customizer
 * values. Kept intentionally small (variables only, no component rules) per
 * the "avoid massive inline CSS" requirement.
 */
function solaris_customizer_css() {
	$font_stacks = array(
		'Space Grotesk' => "'Space Grotesk', 'Inter', sans-serif",
		'Inter'     => "'Inter', -apple-system, BlinkMacSystemFont, sans-serif",
		'Manrope'   => "'Manrope', 'Inter', sans-serif",
		'Poppins'   => "'Poppins', 'Inter', sans-serif",
		'system-ui' => 'system-ui, -apple-system, BlinkMacSystemFont, sans-serif',
	);
	$heading_font = solaris_get_option( 'heading_font' );
	$body_font    = solaris_get_option( 'body_font' );

	$header_bg_image = 'none';
	if ( solaris_get_option( 'header_bg_gradient_enabled' ) ) {
		$gradient_end    = solaris_get_option( 'header_gradient_fade' ) ? 'transparent' : solaris_get_option( 'header_gradient_end' );
		$header_bg_image = sprintf(
			'linear-gradient(%ddeg, %s, %s)',
			absint( solaris_get_option( 'header_gradient_angle' ) ),
			solaris_get_option( 'header_gradient_start' ),
			$gradient_end
		);
	}

	$why_choose_icon_bg   = solaris_get_option( 'why_choose_card_icon_bg_color' );
	$solutions_icon_bg    = solaris_get_option( 'solutions_card_icon_bg_color' );
	$testimonials_avatar_bg = solaris_get_option( 'testimonials_card_avatar_bg_color' );

	/*
	 * The Full Height wash is a large area sitting behind the footer's own
	 * content — using the wave color at full strength there (as the crest
	 * itself correctly does) made picking any solid color visually replace
	 * the entire footer background, which reads as "the wave color changed
	 * my whole footer" even though only .footer-wave's own background
	 * changed. Capping the wash at a fixed low alpha, regardless of which
	 * color is picked, keeps it a subtle tint instead.
	 */
	$footer_wave_color_raw = solaris_get_option( 'footer_wave_color' );
	$footer_wave_wash      = $footer_wave_color_raw
		? 'rgba(' . solaris_hex_to_rgb( $footer_wave_color_raw ) . ', 0.15)'
		: 'rgba(255, 255, 255, 0.12)';

	/*
	 * Fly-to-cart clone box-shadow: a 0-10 Customizer range translated into
	 * an actual box-shadow value, scaling offset/blur/opacity together so
	 * "strength" reads as one intuitive slider instead of three separate
	 * numbers. 0 stays a real "none" rather than a shadow with 0 opacity —
	 * a shadow keyword is cheaper to paint than a fully transparent one.
	 */
	$fly_to_cart_shadow_strength = min( 10, absint( solaris_get_option( 'fly_to_cart_clone_shadow' ) ) );
	$fly_to_cart_clone_shadow    = $fly_to_cart_shadow_strength
		? sprintf(
			'0 %dpx %dpx rgba(7, 28, 21, %s)',
			round( $fly_to_cart_shadow_strength * 1.2 ),
			round( $fly_to_cart_shadow_strength * 2.4 ),
			round( $fly_to_cart_shadow_strength * 0.035, 3 )
		)
		: 'none';

	$vars = array(
		'--color-primary'          => solaris_get_option( 'color_primary' ),
		'--color-secondary'        => solaris_get_option( 'color_secondary' ),
		'--color-accent'           => solaris_get_option( 'color_accent' ),
		'--color-text'             => solaris_get_option( 'color_text' ),
		'--color-heading'          => solaris_get_option( 'color_heading' ),
		'--color-background'       => solaris_get_option( 'color_background' ),
		'--color-header-bg'        => solaris_get_option( 'header_background' ),
		'--color-header-text'      => solaris_get_option( 'header_text_color' ),
		'--color-footer-bg'        => solaris_get_option( 'footer_background' ),
		'--color-footer-text'      => solaris_get_option( 'footer_text_color' ),
		'--color-footer-heading'   => solaris_get_option( 'footer_heading_color' ),
		'--color-footer-accent'    => solaris_get_option( 'footer_accent_color' ),
		'--color-button-text'      => solaris_get_option( 'button_text_color' ),
		'--color-btn-gradient-start' => solaris_get_option( 'btn_gradient_start' ),
		'--color-btn-gradient-mid'   => solaris_get_option( 'btn_gradient_mid' ),
		'--color-btn-gradient-end'   => solaris_get_option( 'btn_gradient_end' ),
		'--font-heading'           => $font_stacks[ $heading_font ] ?? $font_stacks['Manrope'],
		'--font-body'              => $font_stacks[ $body_font ] ?? $font_stacks['Inter'],
		'--font-size-base'         => absint( solaris_get_option( 'base_font_size' ) ) . 'px',
		'--heading-scale'          => solaris_get_option( 'heading_scale' ),
		'--line-height-base'       => solaris_get_option( 'line_height' ),
		'--header-height'          => absint( solaris_get_option( 'header_height' ) ) . 'px',
		'--shop-columns-desktop'   => absint( solaris_get_option( 'shop_columns_desktop' ) ) ?: 4,
		'--shop-columns-mobile'    => absint( solaris_get_option( 'shop_columns_mobile' ) ) ?: 2,
		'--footer-logo-width'      => absint( solaris_get_option( 'footer_logo_width' ) ) . 'px',
		'--footer-logo-height'     => absint( solaris_get_option( 'footer_logo_height' ) ) . 'px',
		/*
		 * NOT var(--color-footer-bg) — a fully-opaque shape the exact same
		 * color as the footer it sits on is invisible no matter how it
		 * animates (confirmed via computed-style + pixel-diff: the Gentle
		 * and Aurora styles genuinely animate — verified transform actually
		 * changes over time — but produce zero visible pixel change against
		 * an identically-colored background). A translucent white sheen is
		 * visible against any footer color by default while staying fully
		 * overridable to a solid custom color.
		 */
		'--footer-wave-color'      => $footer_wave_color_raw ?: 'rgba(255, 255, 255, 0.12)',
		'--footer-wave-wash-color' => $footer_wave_wash,
		'--footer-wave-speed'      => absint( solaris_get_option( 'footer_wave_speed' ) ) . 's',
		'--header-bg-image'        => $header_bg_image,
		'--hero-overlay'           => solaris_get_option( 'hero_overlay_opacity' ),
		'--featured-products-eyebrow-color' => solaris_get_option( 'featured_products_eyebrow_color' ) ?: '#2bb673',
		'--featured-products-title-color'   => solaris_get_option( 'featured_products_title_color' ) ?: '#0b3d2e',
		'--featured-products-bg-color'      => solaris_get_option( 'featured_products_bg_color' ) ?: '#e8fff3',

		'--why-choose-eyebrow-color' => solaris_get_option( 'why_choose_eyebrow_color' ) ?: '#2bb673',
		'--why-choose-title-color' => solaris_get_option( 'why_choose_title_color' ) ?: '#0b3d2e',
		'--why-choose-bg-color'    => solaris_get_option( 'why_choose_bg_color' ) ?: '#ffffff',
		'--why-choose-card-bg'     => solaris_get_option( 'why_choose_card_bg_color' ) ?: '#ffffff',
		'--why-choose-card-border' => solaris_get_option( 'why_choose_card_border_color' ) ?: '#dcebe3',
		'--why-choose-card-text'   => solaris_get_option( 'why_choose_card_text_color' ) ?: '#66736d',
		'--why-choose-icon-bg'     => $why_choose_icon_bg ? $why_choose_icon_bg : 'var(--gradient-primary)',
		'--why-choose-icon-color'  => solaris_get_option( 'why_choose_card_icon_color' ) ?: '#ffffff',

		'--solutions-eyebrow-color'    => solaris_get_option( 'solutions_eyebrow_color' ) ?: '#2bb673',
		'--solutions-title-color'      => solaris_get_option( 'solutions_title_color' ) ?: '#0b3d2e',
		'--solutions-bg-color'         => solaris_get_option( 'solutions_bg_color' ) ?: '#e8fff3',
		'--solutions-card-overlay-rgb' => solaris_hex_to_rgb( solaris_get_option( 'solutions_card_overlay_color' ) ?: '#071c15' ),
		'--solutions-card-border'      => solaris_get_option( 'solutions_card_border_color' ) ?: 'transparent',
		'--solutions-card-text'        => solaris_get_option( 'solutions_card_text_color' ) ?: '#ffffff',
		'--solutions-icon-bg'          => $solutions_icon_bg ? $solutions_icon_bg : 'rgba(255, 255, 255, 0.16)',
		'--solutions-icon-color'       => solaris_get_option( 'solutions_card_icon_color' ) ?: '#ffffff',

		'--testimonials-eyebrow-color' => solaris_get_option( 'testimonials_eyebrow_color' ) ?: '#2bb673',
		'--testimonials-title-color' => solaris_get_option( 'testimonials_title_color' ) ?: '#0b3d2e',
		'--testimonials-bg-color'    => solaris_get_option( 'testimonials_bg_color' ) ?: '#e8fff3',
		'--testimonials-card-bg'     => solaris_get_option( 'testimonials_card_bg_color' ) ?: '#ffffff',
		'--testimonials-card-border' => solaris_get_option( 'testimonials_card_border_color' ) ?: '#dcebe3',
		'--testimonials-card-text'   => solaris_get_option( 'testimonials_card_text_color' ) ?: '#17231e',
		'--testimonials-avatar-bg'   => $testimonials_avatar_bg ? $testimonials_avatar_bg : 'var(--gradient-primary)',
		'--testimonials-avatar-text' => solaris_get_option( 'testimonials_card_avatar_text_color' ) ?: '#ffffff',

		'--faq-eyebrow-color' => solaris_get_option( 'faq_eyebrow_color' ) ?: '#2bb673',
		'--faq-title-color'  => solaris_get_option( 'faq_title_color' ) ?: '#0b3d2e',
		'--faq-bg-color'     => solaris_get_option( 'faq_bg_color' ) ?: '#ffffff',
		'--faq-item-bg'      => solaris_get_option( 'faq_item_bg_color' ) ?: 'transparent',
		'--faq-item-border'  => solaris_get_option( 'faq_item_border_color' ) ?: '#dcebe3',
		'--faq-item-text'    => solaris_get_option( 'faq_item_text_color' ) ?: '#0b3d2e',
		'--faq-icon-color'   => solaris_get_option( 'faq_icon_color' ) ?: '#2bb673',

		'--reveal-base-duration'   => solaris_sanitize_float( solaris_get_option( 'reveal_base_duration' ) ) . 's',
		'--reveal-duration-step'   => solaris_sanitize_float( solaris_get_option( 'reveal_duration_step' ) ) . 's',
		'--reveal-base-delay'      => solaris_sanitize_float( solaris_get_option( 'reveal_base_delay' ) ) . 's',
		'--reveal-delay-step'      => solaris_sanitize_float( solaris_get_option( 'reveal_delay_step' ) ) . 's',

		'--fly-to-cart-clone-bg'     => solaris_get_option( 'fly_to_cart_clone_bg_color' ) ?: '#ffffff',
		'--fly-to-cart-clone-shadow' => $fly_to_cart_clone_shadow,
	);

	/*
	 * NOT esc_html(): these values sit inside a <style> block, not an HTML
	 * attribute — esc_html() HTML-entity-encodes quotes (' -> &#039;), which
	 * silently breaks quoted font-family stacks as CSS syntax (the browser
	 * drops the whole invalid declaration, no visible error). Every value
	 * here is already constrained at the source (sanitize_hex_color(),
	 * absint(), the hardcoded $font_stacks map, or a Customizer float), so
	 * only strip characters that could break out of the style element.
	 */
	$css = ':root{';
	foreach ( $vars as $prop => $value ) {
		$safe_prop  = str_replace( array( '<', '>' ), '', $prop );
		$safe_value = str_replace( array( '<', '>' ), '', (string) $value );
		$css       .= $safe_prop . ':' . $safe_value . ';';
	}
	$css .= '}';

	$css .= sprintf(
		'@media (max-width:640px){:root{--footer-logo-width:%dpx;--footer-logo-height:%dpx;}}',
		absint( solaris_get_option( 'footer_logo_width_mobile' ) ),
		absint( solaris_get_option( 'footer_logo_height_mobile' ) )
	);

	return $css;
}

function solaris_customize_preview_js() {
	wp_enqueue_script( 'solaris-customizer-preview', SOLARIS_URI . '/assets/js/customizer-preview.js', array( 'customize-preview' ), solaris_asset_version( '/assets/js/customizer-preview.js' ), true );
}
add_action( 'customize_preview_init', 'solaris_customize_preview_js' );

/**
 * Runs in the Customizer's admin controls panel itself (customize.php),
 * not the live-preview iframe — customize_preview_init above is for that.
 * Adds a visible current-value display next to every native range slider.
 */
function solaris_customize_controls_assets() {
	wp_enqueue_script( 'solaris-customizer-controls', SOLARIS_URI . '/assets/js/customizer-controls.js', array(), solaris_asset_version( '/assets/js/customizer-controls.js' ), true );
	wp_enqueue_style( 'solaris-customizer-controls', SOLARIS_URI . '/assets/css/customizer-controls.css', array(), solaris_asset_version( '/assets/css/customizer-controls.css' ) );
}
add_action( 'customize_controls_enqueue_scripts', 'solaris_customize_controls_assets' );
