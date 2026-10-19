<?php
/**
 * Header.
 *
 * @package Prime
 */

defined( 'ABSPATH' ) || exit;
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#a10d24">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link sr-only" href="#main"><?php esc_html_e( 'Skip to content', 'prime' ); ?></a>

<div class="preloader" aria-hidden="true">
	<div class="preloader__inner">
		<span class="preloader__orb"><i class="preloader__wave"></i><img class="preloader__mark" src="<?php echo esc_url( prime_mark_url() ); ?>" width="64" height="58" alt="" decoding="async" fetchpriority="high"></span>
		<span class="preloader__word">PRIME RECRUITMENTS</span>
	</div>
</div>

<div class="topbar">
	<div class="container topbar__inner">
		<p class="topbar__tag"><?php echo esc_html( prime_opt( 'contact_hours' ) ); ?></p>
		<div class="topbar__links">
			<a href="mailto:<?php echo esc_attr( prime_opt( 'contact_email' ) ); ?>"><?php echo prime_icon( 'mail', 14 ); // phpcs:ignore ?><span><?php echo esc_html( prime_opt( 'contact_email' ) ); ?></span></a>
			<?php if ( prime_tel_url() ) : ?><a href="<?php echo esc_attr( prime_tel_url() ); ?>"><?php echo prime_icon( 'phone', 14 ); // phpcs:ignore ?><span><?php echo esc_html( prime_opt( 'contact_phone' ) ); ?></span></a><?php endif; ?>
			<?php if ( prime_whatsapp_url() ) : ?><a href="<?php echo esc_url( prime_whatsapp_url() ); ?>" target="_blank" rel="noopener"><?php echo prime_icon( 'whatsapp', 14 ); // phpcs:ignore ?><span>WhatsApp</span></a><?php endif; ?>
			<?php if ( prime_socials() ) : ?><span class="topbar__social social social--sm"><?php echo prime_socials(); // phpcs:ignore ?></span><?php endif; ?>
		</div>
	</div>
</div>

<header class="site-header" id="site-header">
	<div class="site-header__inner">
		<div class="site-header__brand"><?php prime_logo( 'header' ); ?></div>

		<nav class="main-nav" aria-label="<?php esc_attr_e( 'Primary', 'prime' ); ?>">
			<?php if ( has_nav_menu( 'primary' ) ) : ?>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'main-nav__list',
						'depth'          => 2,
					)
				);
				?>
			<?php else : ?>
				<ul class="main-nav__list">
					<li class="<?php echo is_front_page() ? 'is-current' : ''; ?>"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" <?php echo is_front_page() ? 'aria-current="page"' : ''; ?>><?php esc_html_e( 'Home', 'prime' ); ?></a></li>
					<li class="has-mega">
						<a href="<?php echo esc_url( prime_url( 'services' ) ); ?>"><?php esc_html_e( 'Services', 'prime' ); ?> <i class="chev"></i></a>
						<div class="mega">
							<div class="mega__grid">
								<?php foreach ( prime_pages_in( 'service' ) as $key => $p ) : ?>
									<a class="mega__item" href="<?php echo esc_url( prime_url( $key ) ); ?>">
										<span class="mega__icon"><?php echo prime_icon( $p['icon'], 20 ); // phpcs:ignore ?></span>
										<span><strong><?php echo esc_html( $p['menu'] ); ?></strong><small><?php echo esc_html( $p['short'] ); ?></small></span>
									</a>
								<?php endforeach; ?>
							</div>
							<a class="mega__feature" href="<?php echo esc_url( prime_url( 'employers' ) ); ?>">
								<span class="eyebrow"><?php esc_html_e( 'For employers', 'prime' ); ?></span>
								<strong><?php esc_html_e( 'Get an interviewed shortlist in 48–72 hours.', 'prime' ); ?></strong>
								<span class="link-arrow"><?php esc_html_e( 'Share a role', 'prime' ); ?> <?php echo prime_icon( 'arrow', 16 ); // phpcs:ignore ?></span>
							</a>
						</div>
					</li>
					<li><a href="<?php echo esc_url( prime_url( 'gifting' ) ); ?>"><?php esc_html_e( 'Corporate Gifting', 'prime' ); ?></a></li>
					<li><a href="<?php echo esc_url( prime_url( 'industries' ) ); ?>"><?php esc_html_e( 'Industries', 'prime' ); ?></a></li>
					<li><a href="<?php echo esc_url( prime_url( 'jobs' ) ); ?>"><?php esc_html_e( 'Jobs', 'prime' ); ?></a></li>
					<li class="has-drop">
						<a href="<?php echo esc_url( prime_url( 'about' ) ); ?>"><?php esc_html_e( 'About', 'prime' ); ?> <i class="chev"></i></a>
						<div class="drop">
							<a href="<?php echo esc_url( prime_url( 'about' ) ); ?>"><?php esc_html_e( 'About Us', 'prime' ); ?></a>
							<a href="<?php echo esc_url( prime_url( 'employers' ) ); ?>"><?php esc_html_e( 'For Employers', 'prime' ); ?></a>
							<a href="<?php echo esc_url( prime_url( 'job-seekers' ) ); ?>"><?php esc_html_e( 'For Job Seekers', 'prime' ); ?></a>
							<?php foreach ( prime_pages_in( 'location' ) as $key => $p ) : ?>
								<a href="<?php echo esc_url( prime_url( $key ) ); ?>"><?php echo esc_html( sprintf( /* translators: %s: city */ __( 'Recruitment in %s', 'prime' ), $p['menu'] ) ); ?></a>
							<?php endforeach; ?>
						</div>
					</li>
					<li><a href="<?php echo esc_url( prime_url( 'contact' ) ); ?>"><?php esc_html_e( 'Contact', 'prime' ); ?></a></li>
				</ul>
			<?php endif; ?>
			<span class="nav-pill" aria-hidden="true"></span>
		</nav>

		<div class="site-header__actions">
			<a class="btn btn--primary btn--sm header-cta" href="<?php echo esc_url( prime_url( 'contact' ) ); ?>#enquiry" data-modal="enquiry" data-preset="employer"><span><?php echo esc_html( prime_opt( 'header_cta_text' ) ); ?></span><?php echo prime_icon( 'arrow', 16 ); // phpcs:ignore ?></a>
			<button class="burger" type="button" aria-label="<?php esc_attr_e( 'Open menu', 'prime' ); ?>" aria-expanded="false" aria-controls="mobile-menu"><span></span><span></span></button>
		</div>
	</div>
</header>

<div class="mobile-menu" id="mobile-menu" hidden>
	<div class="mobile-menu__inner">
		<p class="eyebrow"><?php esc_html_e( 'Menu', 'prime' ); ?></p>
		<ul class="mobile-menu__list">
			<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'prime' ); ?></a></li>
			<li><a href="<?php echo esc_url( prime_url( 'services' ) ); ?>"><?php esc_html_e( 'Services', 'prime' ); ?></a></li>
			<li><a href="<?php echo esc_url( prime_url( 'gifting' ) ); ?>"><?php esc_html_e( 'Corporate Gifting', 'prime' ); ?></a></li>
			<li><a href="<?php echo esc_url( prime_url( 'industries' ) ); ?>"><?php esc_html_e( 'Industries', 'prime' ); ?></a></li>
			<li><a href="<?php echo esc_url( prime_url( 'jobs' ) ); ?>"><?php esc_html_e( 'Jobs', 'prime' ); ?></a></li>
			<li><a href="<?php echo esc_url( prime_url( 'employers' ) ); ?>"><?php esc_html_e( 'For Employers', 'prime' ); ?></a></li>
			<li><a href="<?php echo esc_url( prime_url( 'job-seekers' ) ); ?>"><?php esc_html_e( 'For Job Seekers', 'prime' ); ?></a></li>
			<li><a href="<?php echo esc_url( prime_url( 'about' ) ); ?>"><?php esc_html_e( 'About', 'prime' ); ?></a></li>
			<li><a href="<?php echo esc_url( prime_url( 'contact' ) ); ?>"><?php esc_html_e( 'Contact', 'prime' ); ?></a></li>
		</ul>
		<div class="mobile-menu__services">
			<?php foreach ( prime_pages_in( 'service' ) as $key => $p ) : ?>
				<a href="<?php echo esc_url( prime_url( $key ) ); ?>"><?php echo esc_html( $p['menu'] ); ?></a>
			<?php endforeach; ?>
		</div>
		<a class="btn btn--primary btn--full" href="<?php echo esc_url( prime_url( 'contact' ) ); ?>#enquiry" data-modal="enquiry"><span><?php esc_html_e( 'Start a conversation', 'prime' ); ?></span><?php echo prime_icon( 'arrow', 18 ); // phpcs:ignore ?></a>
		<div class="mobile-menu__contact">
			<?php if ( prime_tel_url() ) : ?><a class="btn btn--outline" href="<?php echo esc_attr( prime_tel_url() ); ?>"><?php echo prime_icon( 'phone', 18 ); // phpcs:ignore ?><span><?php esc_html_e( 'Call', 'prime' ); ?></span></a><?php endif; ?>
			<?php if ( prime_whatsapp_url() ) : ?><a class="btn btn--whatsapp" href="<?php echo esc_url( prime_whatsapp_url() ); ?>" target="_blank" rel="noopener"><?php echo prime_icon( 'whatsapp', 18 ); // phpcs:ignore ?><span>WhatsApp</span></a><?php endif; ?>
		</div>
		<?php if ( prime_socials() ) : ?><div class="social mobile-menu__social"><?php echo prime_socials(); // phpcs:ignore ?></div><?php endif; ?>
	</div>
</div>

<main id="main" class="site-main">
