<?php
/**
 * Template Name: Prime – Full width (for Elementor)
 *
 * Theme header and footer with an edge-to-edge content area. Pick this for
 * any new page you design from scratch in Elementor.
 *
 * @package Prime
 */

defined( 'ABSPATH' ) || exit;
get_header();
while ( have_posts() ) :
	the_post();
	the_content();
endwhile;
get_footer();
