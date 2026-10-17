<?php
/**
 * 404.
 *
 * @package Prime
 */

defined( 'ABSPATH' ) || exit;
get_header();
?>
<section class="banner banner--grid banner--404">
	<div class="banner__bg" aria-hidden="true"><span class="banner__perspective"></span><span class="banner__scan"></span><span class="aurora a2"></span></div>
	<div class="container banner__inner">
		<p class="mono error-code">404</p>
		<h1 class="banner__title" data-split><?php echo wp_kses_post( prime_heading_html( 'This role has been *filled.*' ) ); ?></h1>
		<p class="banner__lead"><?php esc_html_e( 'The page you were looking for doesn’t exist. Try one of these instead.', 'prime' ); ?></p>
		<div class="banner__actions">
			<a class="btn btn--primary btn--lg" href="<?php echo esc_url( home_url( '/' ) ); ?>"><span><?php esc_html_e( 'Back home', 'prime' ); ?></span><?php echo prime_icon( 'arrow', 18 ); // phpcs:ignore ?></a>
			<a class="btn btn--ghost btn--lg" href="<?php echo esc_url( prime_url( 'jobs' ) ); ?>"><span><?php esc_html_e( 'Browse jobs', 'prime' ); ?></span></a>
		</div>
	</div>
</section>
<?php
get_footer();
