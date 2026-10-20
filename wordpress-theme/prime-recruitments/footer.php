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
	<div class="container">
		<div class="footer-grid">
			<div class="footer-brand">
				<?php prime_logo( 'footer' ); ?>
				<p><?php echo esc_html( prime_opt( 'footer_about' ) ? prime_opt( 'footer_about' ) : prime_home_seo()['meta_desc'] ); ?></p>
				<?php if ( prime_socials() ) : ?><div class="social social--dark"><?php echo prime_socials(); // phpcs:ignore ?></div><?php endif; ?>
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

		<div class="footer-mega" aria-hidden="true">
			<div class="footer-mega__word"><?php foreach ( preg_split( '//u', prime_opt( 'footer_word' ), -1, PREG_SPLIT_NO_EMPTY ) as $i => $ch ) : ?><span style="--i:<?php echo (int) $i; ?>"><?php echo esc_html( $ch ); ?></span><?php endforeach; ?><span class="footer-mega__dot" style="--i:9">.</span></div>
			<div class="footer-mega__sub"><span><?php echo esc_html( prime_opt( 'footer_word_sub' ) ); ?></span><i></i><span><?php echo esc_html( prime_opt( 'footer_tagline' ) ); ?></span></div>
		</div>

		<div class="footer-bottom">
			<span>© <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( prime_opt( 'company_name' ) ); ?>. <?php esc_html_e( 'All rights reserved.', 'prime' ); ?></span>
			<span class="footer-note"><?php echo esc_html( prime_opt( 'footer_note' ) ); ?></span>
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


<nav class="action-bar" aria-label="<?php esc_attr_e( 'Quick actions', 'prime' ); ?>">
	<?php if ( prime_tel_url() ) : ?><a href="<?php echo esc_attr( prime_tel_url() ); ?>"><?php echo prime_icon( 'phone', 20 ); // phpcs:ignore ?><span><?php esc_html_e( 'Call', 'prime' ); ?></span></a><?php endif; ?>
	<?php if ( prime_whatsapp_url() ) : ?><a href="<?php echo esc_url( prime_whatsapp_url() ); ?>" target="_blank" rel="noopener"><?php echo prime_icon( 'whatsapp', 20 ); // phpcs:ignore ?><span>WhatsApp</span></a><?php endif; ?>
	<a class="action-bar__main" href="<?php echo esc_url( prime_url( 'contact' ) ); ?>#enquiry" data-modal="enquiry"><?php echo prime_icon( 'mail', 20 ); // phpcs:ignore ?><span><?php esc_html_e( 'Enquire', 'prime' ); ?></span></a>
</nav>



<?php wp_footer(); ?>
</body>
</html>
