<?php
/**
 * FAQ accordion. Questions/answers are Customizer-driven (FAQ Section →
 * Questions & Answers, Solaris_Faq_Items_Control in
 * inc/customizer-controls.php) — a repeater an admin can add to, edit, and
 * reorder directly in the live-preview Customizer, no separate CPT/wp-admin
 * screen. solaris_get_option( 'faq_items' ) always returns valid JSON
 * (solaris_sanitize_faq_items() guarantees that on save), decoded here into
 * the same {post_title, post_content} shape used everywhere else in this
 * template so the markup below didn't need to change.
 *
 * @package Solaris
 */
$faq_items = json_decode( solaris_get_option( 'faq_items' ), true );
$faqs      = array();
foreach ( (array) $faq_items as $item ) {
	$faqs[] = (object) array(
		'post_title'   => $item['question'] ?? '',
		'post_content' => $item['answer'] ?? '',
	);
}

$section_classes = array(
	'section',
	'has-section-bg',
	'faq-section',
	'faq-hover-' . solaris_get_option( 'faq_hover_effect' ),
	'faq-anim-' . solaris_get_option( 'faq_animation' ),
);
?>
<section class="<?php echo esc_attr( implode( ' ', $section_classes ) ); ?>" id="faq">
	<?php solaris_section_bg_image( 'faq' ); ?>
	<div class="container">
		<div class="section-header is-centered">
			<p class="eyebrow"><?php esc_html_e( 'Frequently Asked Questions', 'solaris' ); ?></p>
			<h2><?php echo esc_html( solaris_get_option( 'faq_title' ) ); ?></h2>
		</div>
		<div class="faq-list">
			<?php foreach ( $faqs as $index => $faq ) : ?>
				<div class="faq-item reveal" data-open="false">
					<button type="button" class="faq-item__question" aria-expanded="false" aria-controls="faq-answer-<?php echo esc_attr( $index ); ?>">
						<span><?php echo esc_html( $faq->post_title ); ?></span>
						<?php echo solaris_icon( 'plus', array( 'class' => 'faq-item__icon' ) ); ?>
					</button>
					<div class="faq-item__answer" id="faq-answer-<?php echo esc_attr( $index ); ?>" role="region">
						<div class="faq-item__answer-inner"><?php echo wp_kses_post( wpautop( $faq->post_content ) ); ?></div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
