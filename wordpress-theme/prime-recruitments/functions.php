<?php
/**
 * Prime Recruitments theme bootstrap.
 *
 * @package Prime
 */

defined( 'ABSPATH' ) || exit;

define( 'PRIME_VERSION', '1.7.0' );
define( 'PRIME_DIR', get_template_directory() );
define( 'PRIME_URI', get_template_directory_uri() );

require PRIME_DIR . '/inc/pages-data.php';
require PRIME_DIR . '/inc/helpers.php';
require PRIME_DIR . '/inc/sections.php';
require PRIME_DIR . '/inc/sections-data.php';
require PRIME_DIR . '/inc/elementor.php';
require PRIME_DIR . '/inc/customizer.php';
require PRIME_DIR . '/inc/seo.php';
require PRIME_DIR . '/inc/form.php';
require PRIME_DIR . '/inc/fluentform.php';
require PRIME_DIR . '/inc/setup.php';

/**
 * Theme supports and menus.
 */
function prime_theme_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 260,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	register_nav_menus(
		array(
			'primary' => __( 'Primary menu (optional – overrides the built-in mega menu)', 'prime' ),
			'footer'  => __( 'Footer legal links', 'prime' ),
		)
	);
}
add_action( 'after_setup_theme', 'prime_theme_setup' );

/**
 * Styles and scripts. Everything is bundled locally – no external JS.
 */
function prime_enqueue_assets() {
	wp_enqueue_style(
		'prime-fonts',
		'https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Nunito+Sans:wght@400;600;700&display=swap',
		array(),
		null
	);
	wp_enqueue_style( 'prime-main', PRIME_URI . '/assets/css/main.css', array(), PRIME_VERSION );

	// Deferred, footer-loaded scripts keep the first paint fast.
	$vendor = PRIME_URI . '/assets/js/vendor/';
	$defer  = array( 'in_footer' => true, 'strategy' => 'defer' );
	wp_enqueue_script( 'prime-gsap', $vendor . 'gsap.min.js', array(), '3.13.0', $defer );
	wp_enqueue_script( 'prime-scrolltrigger', $vendor . 'ScrollTrigger.min.js', array( 'prime-gsap' ), '3.13.0', $defer );
	wp_enqueue_script( 'prime-splittext', $vendor . 'SplitText.min.js', array( 'prime-gsap' ), '3.13.0', $defer );
	wp_enqueue_script( 'prime-lenis', $vendor . 'lenis.min.js', array(), '1.3.4', $defer );
	wp_enqueue_script(
		'prime-main',
		PRIME_URI . '/assets/js/main.js',
		array( 'prime-gsap', 'prime-scrolltrigger', 'prime-splittext', 'prime-lenis' ),
		PRIME_VERSION,
		$defer
	);

	if ( prime_opt( 'fluentform_id' ) ) {
		// Fluent Forms ships its own CSS; ours (in main.css) restyles it to match the theme.
		wp_add_inline_style( 'prime-main', '.prime-form-shell .fluentform{--fluentform-primary:#1D4ED8}' );
	}
}
add_action( 'wp_enqueue_scripts', 'prime_enqueue_assets' );

/**
 * Preconnect for Google Fonts.
 */
function prime_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = 'https://fonts.googleapis.com';
		$urls[] = array(
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin' => 'anonymous',
		);
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'prime_resource_hints', 10, 2 );

/**
 * Adds the "js" class before paint so animated elements can start hidden
 * without a flash, while staying visible when JavaScript is off.
 */
function prime_js_class() {
	// Failsafe: if the animation script never signals readiness, show everything.
	echo "<script>(function(d){d.classList.add('js');setTimeout(function(){if(!d.classList.contains('anim-ready')){d.classList.remove('js');}},4000);})(document.documentElement);</script>\n";
}
add_action( 'wp_head', 'prime_js_class', 0 );
