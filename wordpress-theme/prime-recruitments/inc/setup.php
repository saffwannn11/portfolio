<?php
/**
 * One-click setup: on theme activation, create every landing page (and a Home
 * page set as the static front page), each pre-built as an Elementor layout,
 * so the site is ready and editable immediately.
 * Can be re-run any time from Appearance → Prime Setup.
 *
 * @package Prime
 */

defined( 'ABSPATH' ) || exit;

/**
 * Create missing pages and give every Prime page its Elementor layout.
 * Existing content is never overwritten unless $rebuild is true.
 *
 * @param bool $rebuild Overwrite the Elementor layout of every Prime page.
 * @return array Created page titles.
 */
function prime_install_pages( $rebuild = false ) {
	// Pretty permalinks are required for the keyword-rich URLs.
	if ( ! get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
	}
	global $wp_rewrite;
	if ( $wp_rewrite ) {
		$wp_rewrite->init();
	}

	$created = array();
	$ids     = array();

	foreach ( prime_pages() as $key => $page ) {
		$existing = get_page_by_path( $page['path'] );
		if ( $existing ) {
			$ids[ $key ] = $existing->ID;
			if ( $rebuild || ! get_post_meta( $existing->ID, '_elementor_data', true ) ) {
				prime_write_elementor_layout( $existing->ID, prime_page_sections( $key ) );
			}
			continue;
		}
		$parent_id = 0;
		if ( ! empty( $page['parent'] ) && isset( $ids[ $page['parent'] ] ) ) {
			$parent_id = $ids[ $page['parent'] ];
		}
		$slug_parts = explode( '/', $page['path'] );
		$id         = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => $page['menu'],
				'post_name'    => end( $slug_parts ),
				'post_parent'  => $parent_id,
				'post_content' => '',
				'post_excerpt' => $page['meta_desc'],
			)
		);
		if ( $id && ! is_wp_error( $id ) ) {
			$ids[ $key ] = $id;
			$created[]   = $page['menu'];
			prime_write_elementor_layout( $id, prime_page_sections( $key ) );
			prime_prefill_seo_plugins( $id, $page['meta_title'], $page['meta_desc'], $page['focus_kw'] );
		}
	}

	// Static homepage.
	$home_id = (int) get_option( 'page_on_front' );
	if ( 'posts' === get_option( 'show_on_front' ) || ! $home_id ) {
		$home = get_page_by_path( 'home' );
		if ( ! $home ) {
			$home_id = wp_insert_post(
				array(
					'post_type'   => 'page',
					'post_status' => 'publish',
					'post_title'  => 'Home',
					'post_name'   => 'home',
				)
			);
			$created[] = 'Home';
		} else {
			$home_id = $home->ID;
		}
		if ( $home_id && ! is_wp_error( $home_id ) ) {
			$seo = prime_home_seo();
			prime_prefill_seo_plugins( $home_id, $seo['meta_title'], $seo['meta_desc'], $seo['focus_kw'] );
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', $home_id );
			if ( $rebuild || ! get_post_meta( $home_id, '_elementor_data', true ) ) {
				prime_write_elementor_layout( $home_id, prime_home_sections() );
			}
		}
	} elseif ( $rebuild && 'home' === get_post_field( 'post_name', $home_id ) ) {
		prime_write_elementor_layout( $home_id, prime_home_sections() );
	}

	// Primary menu location stays empty on purpose: the built-in mega menu is
	// used until you assign your own menu in Appearance → Menus.
	prime_configure_elementor();
	prime_clear_elementor_cache();

	flush_rewrite_rules();

	return $created;
}

/**
 * Pre-fill Yoast SEO / Rank Math fields so the values carry over if either
 * plugin is installed later. Only empty fields are written.
 */
function prime_prefill_seo_plugins( $post_id, $title, $desc, $focus ) {
	$map = array(
		'_yoast_wpseo_title'      => $title,
		'_yoast_wpseo_metadesc'   => $desc,
		'_yoast_wpseo_focuskw'    => $focus,
		'rank_math_title'         => $title,
		'rank_math_description'   => $desc,
		'rank_math_focus_keyword' => $focus,
	);
	foreach ( $map as $key => $value ) {
		if ( '' === (string) get_post_meta( $post_id, $key, true ) ) {
			update_post_meta( $post_id, $key, $value );
		}
	}
}

/**
 * Run on activation.
 */
function prime_after_switch_theme() {
	$created = prime_install_pages();
	set_transient( 'prime_setup_notice', $created, 120 );
}
add_action( 'after_switch_theme', 'prime_after_switch_theme' );

/**
 * Post-activation notice.
 */
function prime_setup_notice() {
	$created = get_transient( 'prime_setup_notice' );
	if ( false === $created ) {
		return;
	}
	delete_transient( 'prime_setup_notice' );
	echo '<div class="notice notice-success is-dismissible"><p><strong>Prime Recruitments is ready.</strong> ';
	if ( $created ) {
		/* translators: %d: number of pages */
		printf( esc_html__( '%d pages were created and the homepage was set.', 'prime' ), count( $created ) );
	} else {
		esc_html_e( 'All pages already existed – nothing was overwritten.', 'prime' );
	}
	echo ' ' . sprintf(
		'<a href="%1$s">%2$s</a> · <a href="%3$s">%4$s</a>',
		esc_url( admin_url( 'customize.php?autofocus[section]=title_tagline' ) ),
		esc_html__( 'Upload your logo', 'prime' ),
		esc_url( admin_url( 'customize.php?autofocus[panel]=prime_panel' ) ),
		esc_html__( 'Add contact details & Fluent Form ID', 'prime' )
	);
	echo '</p></div>';
}
add_action( 'admin_notices', 'prime_setup_notice' );

/**
 * Appearance → Prime Setup.
 */
function prime_setup_menu() {
	add_theme_page( __( 'Prime Setup', 'prime' ), __( 'Prime Setup', 'prime' ), 'manage_options', 'prime-setup', 'prime_setup_page' );
}
add_action( 'admin_menu', 'prime_setup_menu' );

function prime_setup_page() {
	$created = null;
	if ( isset( $_POST['prime_setup'] ) && check_admin_referer( 'prime_setup' ) && current_user_can( 'manage_options' ) ) {
		$created = prime_install_pages( 'rebuild' === sanitize_key( wp_unslash( $_POST['prime_setup'] ) ) );
	}
	echo '<div class="wrap"><h1>' . esc_html__( 'Prime Recruitments – Setup', 'prime' ) . '</h1>';
	if ( null !== $created ) {
		echo '<div class="notice notice-success"><p>' . esc_html( $created ? 'Created: ' . implode( ', ', $created ) : 'All pages already exist.' ) . '</p></div>';
	}
	echo '<p>' . esc_html__( 'Creates any missing pages and adds the Elementor layout to Prime pages that do not have one yet. Your edits are never changed.', 'prime' ) . '</p>';
	echo '<p><strong>' . ( did_action( 'elementor/loaded' ) ? esc_html__( 'Elementor is active – open any page and click “Edit with Elementor”.', 'prime' ) : esc_html__( 'Elementor is not active yet – install the free Elementor plugin to edit pages visually. Pages already contain their Elementor layouts.', 'prime' ) ) . '</strong></p>';
	echo '<form method="post">';
	wp_nonce_field( 'prime_setup' );
	echo '<p><button class="button button-primary" name="prime_setup" value="1">' . esc_html__( 'Create missing pages', 'prime' ) . '</button> ';
	echo '<button class="button" name="prime_setup" value="rebuild" onclick="return confirm(\'' . esc_js( __( 'Reset every Prime page to the original design? Elementor edits on those pages will be replaced and cannot be restored.', 'prime' ) ) . '\')">' . esc_html__( 'Reset all pages to original design', 'prime' ) . '</button></p></form>';

	echo '<h2>' . esc_html__( 'Pages and SEO', 'prime' ) . '</h2><table class="widefat striped"><thead><tr><th>Page</th><th>URL</th><th>Meta title</th><th>Focus keyword</th></tr></thead><tbody>';
	foreach ( prime_pages() as $key => $p ) {
		$exists = get_page_by_path( $p['path'] );
		printf(
			'<tr><td>%1$s %2$s</td><td><code>/%3$s/</code></td><td>%4$s</td><td>%5$s</td></tr>',
			esc_html( $p['menu'] ),
			$exists ? '✅' : '—',
			esc_html( $p['path'] ),
			esc_html( $p['meta_title'] ),
			esc_html( $p['focus_kw'] )
		);
	}
	echo '</tbody></table></div>';
}
