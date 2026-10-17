<?php
/**
 * CTA section with the enquiry form (Fluent Forms or built-in fallback).
 *
 * @package Prime
 * @var array $args { title, lead, preset }
 */

defined( 'ABSPATH' ) || exit;
$title  = isset( $args['title'] ) ? $args['title'] : 'Tell us who you need. *We’ll take it from there.*';
$lead   = isset( $args['lead'] ) ? $args['lead'] : __( 'Share a role or your CV. A recruiter replies within one working day — no obligation, no spam.', 'prime' );
$preset = isset( $args['preset'] ) ? $args['preset'] : 'employer';
?>
<section class="cta-form" id="enquiry">
	<div class="cta-form__bg" aria-hidden="true"><span class="aurora a1"></span><span class="aurora a2"></span><span class="aurora a3"></span><span class="cta-form__grid"></span></div>
	<div class="container cta-form__layout">
		<div class="cta-form__copy">
			<?php prime_label( 'Contact', __( 'Start a conversation', 'prime' ), 'label--light' ); ?>
			<h2 class="h2" data-split><?php echo wp_kses_post( prime_heading_html( $title ) ); ?></h2>
			<p class="lead" data-anim="fade-up"><?php echo esc_html( $lead ); ?></p>
			<ul class="cta-form__points" data-anim="stagger">
				<li><?php echo prime_icon( 'check', 18 ); // phpcs:ignore ?><?php esc_html_e( 'Interviewed shortlist, typically in 48–72 hours', 'prime' ); ?></li>
				<li><?php echo prime_icon( 'check', 18 ); // phpcs:ignore ?><?php esc_html_e( 'Transparent, success-based pricing', 'prime' ); ?></li>
				<li><?php echo prime_icon( 'check', 18 ); // phpcs:ignore ?><?php esc_html_e( 'Never a fee for job seekers', 'prime' ); ?></li>
			</ul>
			<div class="cta-form__contact" data-anim="fade-up">
				<a href="mailto:<?php echo esc_attr( prime_opt( 'contact_email' ) ); ?>"><?php echo prime_icon( 'mail', 18 ); // phpcs:ignore ?><span><small><?php esc_html_e( 'Email', 'prime' ); ?></small><?php echo esc_html( prime_opt( 'contact_email' ) ); ?></span></a>
				<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', prime_opt( 'contact_phone' ) ) ); ?>"><?php echo prime_icon( 'phone', 18 ); // phpcs:ignore ?><span><small><?php esc_html_e( 'Call', 'prime' ); ?></small><?php echo esc_html( prime_opt( 'contact_phone' ) ); ?></span></a>
			</div>
		</div>
		<div class="cta-form__card" data-anim="rise">
			<div class="cta-form__card-head">
				<span class="dots" aria-hidden="true"><i></i><i></i><i></i></span>
				<span class="mono">enquiry.submit()</span>
			</div>
			<?php prime_form( $preset ); ?>
		</div>
	</div>
</section>
