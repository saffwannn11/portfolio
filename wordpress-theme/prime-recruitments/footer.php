<?php
/**
 * Footer.
 *
 * @package Prime
 */

defined( 'ABSPATH' ) || exit;
$phone_href = preg_replace( '/[^0-9+]/', '', prime_opt( 'contact_phone' ) );
?>
</main>

<footer class="site-footer">
	<div class="site-footer__glow" aria-hidden="true"></div>
	<div class="container">
		<div class="footer-grid">
			<div class="footer-brand">
				<?php prime_logo( 'footer' ); ?>
				<p><?php echo esc_html( prime_home_seo()['meta_desc'] ); ?></p>
				<?php if ( prime_socials() ) : ?><div class="footer-social"><?php echo prime_socials(); // phpcs:ignore ?></div><?php endif; ?>
			</div>
			<div>
				<h3 class="footer-title"><?php esc_html_e( 'Services', 'prime' ); ?></h3>
				<ul class="footer-links">
					<?php foreach ( prime_pages_in( 'service' ) as $key => $p ) : ?>
						<li><a href="<?php echo esc_url( prime_url( $key ) ); ?>"><?php echo esc_html( $p['menu'] ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>
			<div>
				<h3 class="footer-title"><?php esc_html_e( 'Company', 'prime' ); ?></h3>
				<ul class="footer-links">
					<?php foreach ( array( 'about', 'services', 'gifting', 'industries', 'jobs', 'employers', 'job-seekers', 'contact' ) as $key ) : ?>
						<li><a href="<?php echo esc_url( prime_url( $key ) ); ?>"><?php echo esc_html( prime_page( $key )['menu'] ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>
			<div>
				<h3 class="footer-title"><?php esc_html_e( 'Locations', 'prime' ); ?></h3>
				<ul class="footer-links">
					<?php foreach ( prime_pages_in( 'location' ) as $key => $p ) : ?>
						<li><a href="<?php echo esc_url( prime_url( $key ) ); ?>"><?php echo esc_html( sprintf( /* translators: %s: city */ __( 'Recruitment agency in %s', 'prime' ), $p['menu'] ) ); ?></a></li>
					<?php endforeach; ?>
				</ul>
				<h3 class="footer-title footer-title--spaced"><?php esc_html_e( 'Contact', 'prime' ); ?></h3>
				<ul class="footer-contact">
					<li><?php echo prime_icon( 'mail', 16 ); // phpcs:ignore ?><a href="mailto:<?php echo esc_attr( prime_opt( 'contact_email' ) ); ?>"><?php echo esc_html( prime_opt( 'contact_email' ) ); ?></a></li>
					<li><?php echo prime_icon( 'phone', 16 ); // phpcs:ignore ?><a href="tel:<?php echo esc_attr( $phone_href ); ?>"><?php echo esc_html( prime_opt( 'contact_phone' ) ); ?></a></li>
					<?php if ( prime_whatsapp_url() ) : ?><li><?php echo prime_icon( 'whatsapp', 16 ); // phpcs:ignore ?><a href="<?php echo esc_url( prime_whatsapp_url() ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Chat on WhatsApp', 'prime' ); ?></a></li><?php endif; ?>
					<li><?php echo prime_icon( 'pin', 16 ); // phpcs:ignore ?><span><?php echo esc_html( prime_opt( 'contact_address' ) ); ?></span></li>
				</ul>
			</div>
		</div>

		<div class="footer-wordmark" aria-hidden="true"><span>PRIME</span></div>

		<div class="footer-bottom">
			<span>© <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'All rights reserved.', 'prime' ); ?></span>
			<span class="mono"><?php esc_html_e( 'We never charge candidates a fee.', 'prime' ); ?></span>
			<?php
			if ( has_nav_menu( 'footer' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'menu_class'     => 'footer-legal',
						'depth'          => 1,
					)
				);
			} else {
				echo '<span class="footer-legal">';
				$privacy = get_privacy_policy_url();
				if ( $privacy ) {
					echo '<a href="' . esc_url( $privacy ) . '">' . esc_html__( 'Privacy Policy', 'prime' ) . '</a>';
				}
				echo '<a href="' . esc_url( home_url( '/wp-sitemap.xml' ) ) . '">' . esc_html__( 'Sitemap', 'prime' ) . '</a></span>';
			}
			?>
		</div>
	</div>
</footer>

<?php get_template_part( 'template-parts/enquiry-modal' ); ?>

<div class="fab" aria-label="<?php esc_attr_e( 'Quick contact', 'prime' ); ?>">
	<?php if ( prime_whatsapp_url() ) : ?>
		<a class="fab__btn fab__btn--wa" href="<?php echo esc_url( prime_whatsapp_url() ); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'Chat on WhatsApp', 'prime' ); ?>"><svg width="26" height="26" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2Zm0 18.2c-1.5 0-3-.4-4.3-1.2l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1 1 12 20.2Zm4.5-6.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 0 1-3.3-2.9c-.3-.4.2-.4.7-1.4.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.7.3-.2.3-.9.9-.9 2.2s.9 2.5 1 2.7c.1.2 1.8 2.8 4.4 3.9 1.6.7 2.3.8 3.1.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.2-1.2-.1-.1-.3-.2-.5-.3Z"/></svg><span class="fab__tip"><?php esc_html_e( 'WhatsApp us', 'prime' ); ?></span></a>
	<?php endif; ?>
	<?php if ( prime_tel_url() ) : ?>
		<a class="fab__btn fab__btn--call" href="<?php echo esc_attr( prime_tel_url() ); ?>" aria-label="<?php esc_attr_e( 'Call us', 'prime' ); ?>"><?php echo prime_icon( 'phone', 22 ); // phpcs:ignore ?><span class="fab__tip"><?php echo esc_html( prime_opt( 'contact_phone' ) ); ?></span></a>
	<?php endif; ?>
</div>

<nav class="action-bar" aria-label="<?php esc_attr_e( 'Quick actions', 'prime' ); ?>">
	<?php if ( prime_tel_url() ) : ?><a href="<?php echo esc_attr( prime_tel_url() ); ?>"><?php echo prime_icon( 'phone', 20 ); // phpcs:ignore ?><span><?php esc_html_e( 'Call', 'prime' ); ?></span></a><?php endif; ?>
	<?php if ( prime_whatsapp_url() ) : ?><a href="<?php echo esc_url( prime_whatsapp_url() ); ?>" target="_blank" rel="noopener"><?php echo prime_icon( 'whatsapp', 20 ); // phpcs:ignore ?><span>WhatsApp</span></a><?php endif; ?>
	<a class="action-bar__main" href="<?php echo esc_url( prime_url( 'contact' ) ); ?>#enquiry" data-modal="enquiry"><?php echo prime_icon( 'mail', 20 ); // phpcs:ignore ?><span><?php esc_html_e( 'Enquire', 'prime' ); ?></span></a>
</nav>

<button class="to-top" type="button" aria-label="<?php esc_attr_e( 'Back to top', 'prime' ); ?>">
	<svg viewBox="0 0 48 48" aria-hidden="true"><circle class="to-top__track" cx="24" cy="24" r="21"/><circle class="to-top__ring" cx="24" cy="24" r="21"/></svg>
	<?php echo prime_icon( 'arrow', 16 ); // phpcs:ignore ?>
</button>

<?php wp_footer(); ?>
</body>
</html>
