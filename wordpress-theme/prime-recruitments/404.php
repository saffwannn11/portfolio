<?php
/**
 * 404.
 *
 * @package Prime
 */

defined( 'ABSPATH' ) || exit;
get_header();
?>
<section class="banner banner--404">
	<div class="banner__media" aria-hidden="true"><img src="<?php echo esc_url( prime_photo( 'corridor' ) ); ?>" alt="" decoding="async"></div>
	<div class="container banner__inner">
		<p class="error-code">404</p>
		<h1 class="banner__title" data-split><?php echo wp_kses_post( prime_heading_html( 'This role has been *filled.*' ) ); ?></h1>
		<p class="banner__lead"><?php esc_html_e( 'The page you were looking for doesn’t exist. Try one of these instead.', 'prime' ); ?></p>
		<div class="banner__actions">
			<a class="btn btn--primary btn--lg" href="<?php echo esc_url( home_url( '/' ) ); ?>"><span><?php esc_html_e( 'Back home', 'prime' ); ?></span><?php echo prime_icon( 'arrow', 18 ); // phpcs:ignore ?></a>
			<a class="btn btn--light-outline btn--lg" href="<?php echo esc_url( prime_url( 'jobs' ) ); ?>"><span><?php esc_html_e( 'Browse jobs', 'prime' ); ?></span></a>
		</div>
	</div>
</section>
<?php
get_footer();
