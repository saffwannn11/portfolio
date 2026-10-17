<?php
/**
 * FAQ accordion (questions also output as FAQPage schema by inc/seo.php).
 *
 * @package Prime
 * @var array $args { faqs, num, title, intro }
 */

defined( 'ABSPATH' ) || exit;
$faqs = isset( $args['faqs'] ) ? $args['faqs'] : array();
if ( ! $faqs ) {
	return;
}
?>
<section class="section faq-section" id="faq">
	<div class="container faq-layout">
		<div class="faq-layout__head">
			<?php prime_label( isset( $args['num'] ) ? $args['num'] : 'FAQ', __( 'Questions, answered', 'prime' ) ); ?>
			<h2 class="h2" data-split><?php echo wp_kses_post( prime_heading_html( isset( $args['title'] ) ? $args['title'] : 'Frequently asked *questions*' ) ); ?></h2>
			<p class="muted" data-anim="fade-up"><?php echo esc_html( isset( $args['intro'] ) ? $args['intro'] : __( 'Straight answers to what employers and candidates ask us most.', 'prime' ) ); ?></p>
			<a class="link-arrow" href="<?php echo esc_url( prime_url( 'contact' ) ); ?>" data-anim="fade-up"><?php esc_html_e( 'Ask us anything', 'prime' ); ?> <?php echo prime_icon( 'arrow', 16 ); // phpcs:ignore ?></a>
		</div>
		<div class="faq-list" data-anim="stagger">
			<?php foreach ( $faqs as $i => $f ) : ?>
				<details class="faq-item" <?php echo 0 === $i ? 'open' : ''; ?>>
					<summary><h3><?php echo esc_html( $f[0] ); ?></h3><span class="faq-item__icon" aria-hidden="true"></span></summary>
					<div class="faq-item__body"><p><?php echo esc_html( $f[1] ); ?></p></div>
				</details>
			<?php endforeach; ?>
		</div>
	</div>
</section>
