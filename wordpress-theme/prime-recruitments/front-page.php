<?php
/**
 * Homepage. Built with Elementor when available (edit it in Elementor);
 * otherwise the same layout is rendered by the theme.
 *
 * @package Prime
 */

defined( 'ABSPATH' ) || exit;
get_header();

if ( 'page' === get_option( 'show_on_front' ) && have_posts() ) {
	the_post();
	if ( prime_is_elementor_page() ) {
		the_content();
	} else {
		prime_render_saved_layout( get_the_ID(), prime_home_sections() );
	}
} else {
	foreach ( prime_home_sections() as $sec ) {
		prime_render_section( $sec[0], $sec[1] );
	}
}

get_footer();
