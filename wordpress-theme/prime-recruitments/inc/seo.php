<?php
/**
 * SEO, AEO and GEO output:
 * - <title>, meta description, focus keywords, Open Graph / Twitter tags
 * - JSON-LD: EmploymentAgency, WebSite, WebPage, BreadcrumbList, Service, FAQPage
 * - "Prime SEO" meta box on pages (meta title, description, focus keyword)
 * - /llms.txt summary for AI answer engines
 *
 * If Yoast SEO, Rank Math, AIOSEO or SEOPress is active, the theme stops
 * printing its own title/description/OG tags (the plugin owns them) but keeps
 * the Service schema. FAQPage schema comes from the FAQ section/widget. On activation the theme pre-fills those
 * plugins' fields with the same values (see inc/setup.php).
 *
 * @package Prime
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether a dedicated SEO plugin is handling meta tags.
 */
function prime_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' );
}

/**
 * Resolved SEO values for the current request.
 */
function prime_seo() {
	static $seo = null;
	if ( null !== $seo ) {
		return $seo;
	}

	$seo = array(
		'title'    => '',
		'desc'     => get_bloginfo( 'description' ),
		'focus'    => '',
		'keywords' => array(),
		'url'      => home_url( add_query_arg( array() ) ),
		'type'     => 'website',
	);

	$post_id = is_singular() ? get_queried_object_id() : 0;

	if ( is_front_page() ) {
		$home            = prime_home_seo();
		$seo['title']    = $home['meta_title'];
		$seo['desc']     = $home['meta_desc'];
		$seo['focus']    = $home['focus_kw'];
		$seo['keywords'] = $home['keywords'];
		$seo['url']      = home_url( '/' );
	} elseif ( $page = prime_current_page() ) {
		$seo['title']    = $page['meta_title'];
		$seo['desc']     = $page['meta_desc'];
		$seo['focus']    = $page['focus_kw'];
		$seo['keywords'] = $page['keywords'];
	} elseif ( $post_id ) {
		$seo['desc'] = wp_trim_words( wp_strip_all_tags( get_the_excerpt( $post_id ) ), 28, '…' );
		$seo['type'] = 'article';
	}

	if ( $post_id ) {
		$seo['url'] = get_permalink( $post_id );
		// Editor overrides from the "Prime SEO" meta box.
		foreach ( array( 'title' => '_prime_seo_title', 'desc' => '_prime_seo_desc', 'focus' => '_prime_seo_focus' ) as $k => $meta_key ) {
			$v = get_post_meta( $post_id, $meta_key, true );
			if ( '' !== $v && false !== $v ) {
				$seo[ $k ] = $v;
			}
		}
	}

	return $seo;
}

/**
 * Document title.
 */
function prime_document_title( $title ) {
	if ( prime_seo_plugin_active() ) {
		return $title;
	}
	$seo = prime_seo();
	return $seo['title'] ? $seo['title'] : $title;
}
add_filter( 'pre_get_document_title', 'prime_document_title', 20 );

/**
 * Meta + Open Graph tags.
 */
function prime_meta_tags() {
	if ( prime_seo_plugin_active() ) {
		return;
	}
	$seo   = prime_seo();
	$title = $seo['title'] ? $seo['title'] : wp_get_document_title();
	$kw    = array_filter( array_merge( array( $seo['focus'] ), (array) $seo['keywords'] ) );

	echo "\n<!-- Prime SEO -->\n";
	if ( $seo['desc'] ) {
		printf( '<meta name="description" content="%s">' . "\n", esc_attr( $seo['desc'] ) );
	}
	if ( $kw ) {
		printf( '<meta name="keywords" content="%s">' . "\n", esc_attr( implode( ', ', $kw ) ) );
	}
	if ( ! is_singular() ) {
		// Core prints rel=canonical for singular content only.
		printf( '<link rel="canonical" href="%s">' . "\n", esc_url( $seo['url'] ) );
	}
	printf( '<meta property="og:type" content="%s">' . "\n", esc_attr( $seo['type'] ) );
	printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $title ) );
	printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $seo['desc'] ) );
	printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $seo['url'] ) );
	printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
	echo '<meta property="og:locale" content="en_IN">' . "\n";
	$img = is_singular() && has_post_thumbnail() ? get_the_post_thumbnail_url( null, 'large' ) : '';
	if ( $img ) {
		printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $img ) );
	}
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	printf( '<meta name="twitter:title" content="%s">' . "\n", esc_attr( $title ) );
	printf( '<meta name="twitter:description" content="%s">' . "\n", esc_attr( $seo['desc'] ) );
	echo '<meta name="geo.region" content="IN">' . "\n";
	echo "<!-- /Prime SEO -->\n";
}
add_action( 'wp_head', 'prime_meta_tags', 1 );

/**
 * JSON-LD structured data.
 */
function prime_schema() {
	$home    = home_url( '/' );
	$org_id  = $home . '#organization';
	$plugin  = prime_seo_plugin_active();
	$seo     = prime_seo();
	$graph   = array();
	$page    = prime_current_page();

	if ( ! $plugin ) {
		$org = array(
			'@type'       => 'EmploymentAgency',
			'@id'         => $org_id,
			'name'        => get_bloginfo( 'name' ),
			'url'         => $home,
			'description' => prime_home_seo()['meta_desc'],
			'email'       => prime_opt( 'contact_email' ),
			'telephone'   => prime_opt( 'contact_phone' ),
			'areaServed'  => array( '@type' => 'Country', 'name' => 'India' ),
			'knowsAbout'  => array( 'Recruitment', 'Executive search', 'IT recruitment', 'Contract staffing', 'Recruitment process outsourcing', 'Bulk hiring', 'Healthcare recruitment', 'Overseas recruitment' ),
		);
		$logo_id = get_theme_mod( 'custom_logo' );
		if ( $logo_id ) {
			$org['logo'] = wp_get_attachment_image_url( $logo_id, 'full' );
		}
		$same = array_filter( array( prime_opt( 'social_linkedin' ), prime_opt( 'social_instagram' ), prime_opt( 'social_x' ) ) );
		if ( $same ) {
			$org['sameAs'] = array_values( $same );
		}
		$graph[] = $org;

		if ( is_front_page() ) {
			$graph[] = array(
				'@type'     => 'WebSite',
				'@id'       => $home . '#website',
				'url'       => $home,
				'name'      => get_bloginfo( 'name' ),
				'publisher' => array( '@id' => $org_id ),
				'inLanguage'=> 'en-IN',
			);
		}

		if ( $page ) {
			$crumbs   = array( array( 'Home', $home ) );
			if ( ! empty( $page['parent'] ) ) {
				$parent   = prime_page( $page['parent'] );
				$crumbs[] = array( $parent['menu'], prime_url( $page['parent'] ) );
			}
			$crumbs[] = array( $page['menu'], $seo['url'] );
			$items    = array();
			foreach ( $crumbs as $i => $c ) {
				$items[] = array(
					'@type'    => 'ListItem',
					'position' => $i + 1,
					'name'     => $c[0],
					'item'     => $c[1],
				);
			}
			$graph[] = array(
				'@type'           => 'BreadcrumbList',
				'@id'             => $seo['url'] . '#breadcrumb',
				'itemListElement' => $items,
			);

			$page_types = array(
				'about'   => 'AboutPage',
				'contact' => 'ContactPage',
				'jobs'    => 'CollectionPage',
				'hub'     => 'CollectionPage',
			);
			$graph[] = array(
				'@type'       => isset( $page_types[ $page['type'] ] ) ? $page_types[ $page['type'] ] : 'WebPage',
				'@id'         => $seo['url'] . '#webpage',
				'url'         => $seo['url'],
				'name'        => $seo['title'],
				'description' => $seo['desc'],
				'inLanguage'  => 'en-IN',
				'about'       => array( '@id' => $org_id ),
				'breadcrumb'  => array( '@id' => $seo['url'] . '#breadcrumb' ),
				'keywords'    => implode( ', ', array_merge( array( $seo['focus'] ), $page['keywords'] ) ),
				'dateModified'=> get_the_modified_date( 'c' ),
			);
		}
	}

	if ( $page && in_array( $page['type'], array( 'service', 'location' ), true ) ) {
		$areas = isset( $page['cities'] ) ? $page['cities'] : prime_default_cities();
		$graph[] = array(
			'@type'       => 'Service',
			'@id'         => $seo['url'] . '#service',
			'name'        => 'location' === $page['type'] ? 'Recruitment services in ' . $page['city'] : $page['menu'],
			'serviceType' => $seo['focus'],
			'description' => $page['answer'],
			'provider'    => array(
				'@type' => 'EmploymentAgency',
				'@id'   => $org_id,
				'name'  => get_bloginfo( 'name' ),
			),
			'areaServed'  => array_map(
				function ( $a ) {
					return array( '@type' => 'Place', 'name' => $a );
				},
				$areas
			),
			'url'         => $seo['url'],
		);
	}

	// FAQPage schema is printed by the FAQ section itself, from the questions
	// actually on the page (so Elementor edits stay in sync).

	if ( ! $graph ) {
		return;
	}

	echo "\n" . '<script type="application/ld+json">' . wp_json_encode(
		array(
			'@context' => 'https://schema.org',
			'@graph'   => $graph,
		),
		JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
	) . "</script>\n";
}
add_action( 'wp_head', 'prime_schema', 30 );

/**
 * Homepage FAQs (shared by template and schema).
 */
function prime_home_faqs() {
	return array(
		array( 'What is a recruitment agency?', 'A recruitment agency finds, screens and shortlists candidates for employers, and helps job seekers find suitable roles. Employers pay the agency a fee; candidates are not charged.' ),
		array( 'How much does a recruitment agency charge in India?', 'Permanent hiring fees in India typically range from about 8.33% to 20% of the candidate’s annual CTC, depending on seniority and difficulty. Contract staffing is billed as a monthly service fee on top of salary.' ),
		array( 'How fast can Prime Recruitments send candidates?', 'For standard roles we target a first shortlist of interviewed candidates within 48 to 72 hours of the brief.' ),
		array( 'Do job seekers pay Prime Recruitments?', 'No. Prime never charges candidates any registration or placement fee.' ),
		array( 'Which cities does Prime Recruitments cover?', 'We recruit across India, including Mumbai, Delhi NCR, Bangalore, Pune, Hyderabad, Chennai, Kolkata and Ahmedabad, as well as remote and overseas roles.' ),
		array( 'What types of hiring does Prime handle?', 'Permanent recruitment, executive search, IT recruitment, contract staffing, recruitment process outsourcing (RPO), bulk hiring, healthcare recruitment and overseas recruitment.' ),
	);
}

/* --------------------------------------------------------------------------
 * Meta box: Prime SEO
 * ------------------------------------------------------------------------ */

function prime_seo_metabox() {
	if ( prime_seo_plugin_active() ) {
		return; // Edit SEO in your SEO plugin instead.
	}
	add_meta_box( 'prime-seo', __( 'Prime SEO', 'prime' ), 'prime_seo_metabox_html', array( 'page', 'post' ), 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'prime_seo_metabox' );

function prime_seo_metabox_html( $post ) {
	wp_nonce_field( 'prime_seo_save', 'prime_seo_nonce' );
	$fields = array(
		'_prime_seo_title' => array( __( 'Meta title (50–60 characters)', 'prime' ), 'input' ),
		'_prime_seo_desc'  => array( __( 'Meta description (140–160 characters)', 'prime' ), 'textarea' ),
		'_prime_seo_focus' => array( __( 'Focus keyword', 'prime' ), 'input' ),
	);
	echo '<p style="margin-top:0;color:#646970">' . esc_html__( 'Leave a field empty to use the theme default for this page.', 'prime' ) . '</p>';
	foreach ( $fields as $key => $field ) {
		$val = get_post_meta( $post->ID, $key, true );
		echo '<p><label style="font-weight:600;display:block;margin-bottom:4px" for="' . esc_attr( $key ) . '">' . esc_html( $field[0] ) . '</label>';
		if ( 'textarea' === $field[1] ) {
			echo '<textarea class="widefat" rows="3" id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '">' . esc_textarea( $val ) . '</textarea>';
		} else {
			echo '<input class="widefat" type="text" id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" value="' . esc_attr( $val ) . '">';
		}
		echo '</p>';
	}
}

function prime_seo_metabox_save( $post_id ) {
	if ( ! isset( $_POST['prime_seo_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['prime_seo_nonce'] ) ), 'prime_seo_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	foreach ( array( '_prime_seo_title', '_prime_seo_desc', '_prime_seo_focus' ) as $key ) {
		if ( isset( $_POST[ $key ] ) ) {
			update_post_meta( $post_id, $key, sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) );
		}
	}
}
add_action( 'save_post', 'prime_seo_metabox_save' );

/* --------------------------------------------------------------------------
 * /llms.txt – a plain-text site summary for AI answer engines (GEO)
 * ------------------------------------------------------------------------ */

function prime_llms_txt() {
	$path = isset( $_SERVER['REQUEST_URI'] ) ? wp_parse_url( esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ), PHP_URL_PATH ) : '';
	$home_path = wp_parse_url( home_url( '/' ), PHP_URL_PATH );
	if ( rtrim( (string) $home_path, '/' ) . '/llms.txt' !== $path ) {
		return;
	}
	status_header( 200 );
	header( 'Content-Type: text/plain; charset=utf-8' );
	$home = prime_home_seo();
	echo '# ' . esc_html( get_bloginfo( 'name' ) ) . "\n\n";
	echo '> ' . esc_html( $home['meta_desc'] ) . "\n\n";
	echo "## Key pages\n\n";
	foreach ( prime_pages() as $key => $p ) {
		echo '- [' . esc_html( $p['menu'] ) . '](' . esc_url( prime_url( $key ) ) . '): ' . esc_html( $p['answer'] ) . "\n";
	}
	echo "\n## Contact\n\n";
	echo '- Email: ' . esc_html( prime_opt( 'contact_email' ) ) . "\n";
	echo '- Phone: ' . esc_html( prime_opt( 'contact_phone' ) ) . "\n";
	exit;
}
add_action( 'template_redirect', 'prime_llms_txt', 0 );
