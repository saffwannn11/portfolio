<?php
/**
 * Centered enquiry popup: brand panel on the left, form on the right.
 * Opened by any link to #enquiry or any element with data-modal="enquiry".
 *
 * @package Prime
 */

defined( 'ABSPATH' ) || exit;
$side_img = prime_opt( 'modal_image' );
?>
<div class="pmodal" id="prime-modal" role="dialog" aria-modal="true" aria-labelledby="pmodal-title" hidden>
	<div class="pmodal__backdrop" data-close></div>
	<div class="pmodal__dialog" role="document">
		<button type="button" class="pmodal__close" data-close aria-label="<?php esc_attr_e( 'Close', 'prime' ); ?>"><?php echo prime_icon( 'x', 18 ); // phpcs:ignore ?></button>

		<aside class="pmodal__side" <?php echo $side_img ? 'style="--side-img:url(' . esc_url( $side_img ) . ')"' : ''; ?>>
			<div class="pmodal__brand">
				<img src="<?php echo esc_url( prime_img( 'logo-mark-light.png' ) ); ?>" width="44" height="40" alt="" loading="lazy" decoding="async">
				<span><strong>PRIME</strong> RECRUITMENTS</span>
			</div>
			<p class="pmodal__kicker" data-modal-kicker><?php esc_html_e( 'Hiring made simple', 'prime' ); ?></p>
			<p class="pmodal__headline" data-modal-headline><?php esc_html_e( 'The right people, in your inbox fast.', 'prime' ); ?></p>
			<ul class="pmodal__points">
				<li><?php echo prime_icon( 'check', 16 ); // phpcs:ignore ?><?php esc_html_e( 'Reply within one working day', 'prime' ); ?></li>
				<li><?php echo prime_icon( 'check', 16 ); // phpcs:ignore ?><?php esc_html_e( 'Recruitment & corporate gifting, pan-India', 'prime' ); ?></li>
				<li><?php echo prime_icon( 'check', 16 ); // phpcs:ignore ?><?php esc_html_e( 'No fees for job seekers – ever', 'prime' ); ?></li>
			</ul>
			<ul class="pmodal__services" aria-label="<?php esc_attr_e( 'Our services', 'prime' ); ?>">
				<?php foreach ( array( 'Permanent hiring', 'Executive search', 'IT recruitment', 'Contract staffing', 'Bulk hiring', 'Corporate gifting' ) as $svc ) : ?><li><?php echo esc_html( $svc ); ?></li><?php endforeach; ?>
			</ul>
			<div class="pmodal__person">
				<span class="pmodal__avatar"><?php echo esc_html( mb_substr( prime_opt( 'contact_person' ), 0, 1 ) ); ?></span>
				<span><strong><?php echo esc_html( prime_opt( 'contact_person' ) ); ?></strong><small><?php echo esc_html( prime_opt( 'contact_role' ) ); ?> · <?php echo esc_html( prime_opt( 'contact_phone' ) ); ?></small></span>
			</div>
			<div class="pmodal__quick">
				<?php if ( prime_tel_url() ) : ?><a href="<?php echo esc_attr( prime_tel_url() ); ?>"><?php echo prime_icon( 'phone', 16 ); // phpcs:ignore ?><?php esc_html_e( 'Call', 'prime' ); ?></a><?php endif; ?>
				<?php if ( prime_whatsapp_url() ) : ?><a href="<?php echo esc_url( prime_whatsapp_url() ); ?>" target="_blank" rel="noopener"><?php echo prime_icon( 'whatsapp', 16 ); // phpcs:ignore ?>WhatsApp</a><?php endif; ?>
				<a href="mailto:<?php echo esc_attr( prime_opt( 'contact_email' ) ); ?>"><?php echo prime_icon( 'mail', 16 ); // phpcs:ignore ?><?php esc_html_e( 'Email', 'prime' ); ?></a>
			</div>
		</aside>

		<div class="pmodal__main">
			<p class="eyebrow"><?php esc_html_e( 'Enquiry', 'prime' ); ?></p>
			<h2 class="pmodal__title" id="pmodal-title" data-modal-title><?php esc_html_e( 'Tell us what you need', 'prime' ); ?></h2>
			<p class="pmodal__sub"><?php esc_html_e( 'Share a few details and our team will get back to you within one working day.', 'prime' ); ?></p>
			<?php prime_form( 'employer', '', 'light' ); ?>
		</div>
	</div>
</div>
