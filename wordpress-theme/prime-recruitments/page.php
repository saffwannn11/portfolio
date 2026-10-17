<?php
/**
 * Page template.
 *
 * - Pages built with Elementor render through Elementor (fully editable).
 * - Prime landing pages without Elementor render their saved/default layout.
 * - Any other page gets a banner plus its editor content.
 *
 * @package Prime
 */

defined( 'ABSPATH' ) || exit;
get_header();

while ( have_posts() ) :
	the_post();
	$prime_page = prime_current_page();

	if ( prime_is_elementor_page() ) {
		the_content();
	} elseif ( $prime_page ) {
		prime_render_saved_layout( get_the_ID(), prime_page_sections( $prime_page['key'] ) );
	} else {
		prime_render_section(
			'banner',
			array(
				'variant'     => 'mesh',
				'title'       => get_the_title(),
				'show_answer' => '',
				'lead'        => has_excerpt() ? get_the_excerpt() : '',
			)
		);
		echo '<section class="section"><div class="container prose">';
		the_content();
		echo '</div></section>';
		prime_render_section( 'cta_form', prime_cta_defaults( 'employer' ) );
	}
endwhile;

get_footer();
