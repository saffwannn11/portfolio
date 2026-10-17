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
	update_option( 'prime_theme_version', PRIME_VERSION );
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
		$action = sanitize_key( wp_unslash( $_POST['prime_setup'] ) );
		if ( 'fluent' === $action ) {
			set_theme_mod( 'prime_fluentform_id', '' );
			prime_ff_reset_attempt();
			prime_ff_maybe_create();
			$created = array();
		} else {
			$created = prime_install_pages( 'rebuild' === $action );
		}
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
	if ( class_exists( '\\FluentForm\\App\\Services\\Form\\FormService' ) ) {
		echo '<button class="button" name="prime_setup" value="fluent">' . esc_html__( 'Create the Fluent Forms enquiry form', 'prime' ) . '</button> ';
	}
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

/* --------------------------------------------------------------------------
 * Theme updates: run when a newer version of the theme is uploaded over an
 * existing one (after_switch_theme does not fire on updates).
 * ------------------------------------------------------------------------ */

/**
 * True when a page's Elementor layout is still exactly what the theme wrote,
 * i.e. nobody has edited it, so it is safe to replace with the new design.
 */
function prime_layout_untouched( $post_id ) {
	$data = (string) get_post_meta( $post_id, '_elementor_data', true );
	if ( '' === $data ) {
		return true;
	}
	$sig = get_post_meta( $post_id, '_prime_layout_sig', true );
	if ( $sig ) {
		return md5( $data ) === $sig;
	}
	// Layouts written by 1.1.0 have no signature: treat the page as untouched
	// only if it was never saved after the theme created it.
	$post = get_post( $post_id );
	return $post && $post->post_modified === $post->post_date && ! wp_get_post_revisions( $post_id, array( 'fields' => 'ids' ) );
}

/**
 * Replace placeholder contact details left in Elementor layouts.
 */
function prime_replace_placeholders( $post_id ) {
	$data = (string) get_post_meta( $post_id, '_elementor_data', true );
	if ( '' === $data ) {
		return;
	}
	$tel  = preg_replace( '/[^0-9+]/', '', prime_opt( 'contact_phone' ) );
	$map  = array(
		'+91 00000 00000'                           => prime_opt( 'contact_phone' ),
		'tel:+910000000000'                         => 'tel:' . $tel,
		'hello@primerecruitments.com'               => prime_opt( 'contact_email' ),
		'careers@primerecruitments.com'             => prime_opt( 'careers_email' ),
		'Add your office address, City, State, PIN' => prime_opt( 'contact_address' ),
	);
	$new = str_replace( array_keys( $map ), array_values( $map ), $data );
	if ( $new !== $data ) {
		update_post_meta( $post_id, '_elementor_data', wp_slash( $new ) );
	}
}

/**
 * Insert the corporate gifting section into an edited homepage (before the
 * testimonials, or before the FAQ, or before the final CTA).
 */
function prime_insert_gifting_section( $post_id ) {
	$data = json_decode( (string) get_post_meta( $post_id, '_elementor_data', true ), true );
	if ( ! is_array( $data ) || false !== strpos( wp_json_encode( $data ), '"prime-gifting"' ) ) {
		return;
	}
	$new = prime_build_elementor_data( array( array( 'gifting', prime_gifting_settings( true ) ) ) );
	$pos = count( $data );
	foreach ( array( 'prime-testimonials', 'prime-faq', 'prime-cta-form' ) as $before ) {
		foreach ( $data as $i => $el ) {
			if ( false !== strpos( wp_json_encode( $el ), '"' . $before . '"' ) ) {
				$pos = $i;
				break 2;
			}
		}
	}
	array_splice( $data, $pos, 0, $new );
	update_post_meta( $post_id, '_elementor_data', wp_slash( wp_json_encode( $data ) ) );
}

function prime_maybe_upgrade() {
	if ( PRIME_VERSION === get_option( 'prime_theme_version' ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	update_option( 'prime_theme_version', PRIME_VERSION );

	prime_install_pages(); // Creates new pages (e.g. Corporate Gifting); never overwrites.

	$targets = array();
	foreach ( prime_pages() as $key => $page ) {
		$post = get_page_by_path( $page['path'] );
		if ( $post ) {
			$targets[ $post->ID ] = $key;
		}
	}
	$home_id = (int) get_option( 'page_on_front' );
	if ( $home_id && 'home' === get_post_field( 'post_name', $home_id ) ) {
		$targets[ $home_id ] = 'home';
	}

	$updated = 0;
	foreach ( $targets as $id => $key ) {
		if ( prime_layout_untouched( $id ) ) {
			prime_write_elementor_layout( $id, 'home' === $key ? prime_home_sections() : prime_page_sections( $key ) );
			$updated++;
		} else {
			prime_replace_placeholders( $id );
			if ( 'home' === $key ) {
				prime_insert_gifting_section( $id );
			}
			delete_post_meta( $id, '_elementor_css' );
			delete_post_meta( $id, '_elementor_element_cache' );
		}
	}
	prime_clear_elementor_cache();
	set_transient( 'prime_upgrade_notice', $updated, 300 );
}
add_action( 'admin_init', 'prime_maybe_upgrade', 5 );

function prime_upgrade_notice() {
	$n = get_transient( 'prime_upgrade_notice' );
	if ( false === $n ) {
		return;
	}
	delete_transient( 'prime_upgrade_notice' );
	echo '<div class="notice notice-success is-dismissible"><p><strong>Prime Recruitments updated to ' . esc_html( PRIME_VERSION ) . '.</strong> ' . esc_html__( 'New pages were added and unedited pages received the new design. Pages you edited in Elementor were kept – the new Corporate Gifting section was added to your homepage.', 'prime' ) . '</p></div>';
}
add_action( 'admin_notices', 'prime_upgrade_notice' );
