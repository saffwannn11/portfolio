<?php
/**
 * Default section layouts for every page.
 *
 * Each page is a list of [ widget-key, settings ]. On theme activation these
 * layouts are written into Elementor (inc/setup.php), so every section can be
 * edited, reordered, duplicated or removed in the Elementor editor. Without
 * Elementor the theme renders the same layouts directly.
 *
 * @package Prime
 */

defined( 'ABSPATH' ) || exit;

/**
 * Homepage layout.
 */
function prime_home_sections() {
	$services = prime_pages_in( 'service' );
	$cards    = array();
	foreach ( $services as $key => $p ) {
		$cards[] = array(
			'icon'   => $p['icon'],
			'title'  => $p['menu'],
			'text'   => $p['short'],
			'points' => implode( "\n", wp_list_pluck( array_slice( $p['features'], 0, 3 ), 1 ) ),
			'link'   => prime_link( prime_url( $key ) ),
		);
	}

	$img = function ( $id ) {
		return array( 'url' => 'https://images.unsplash.com/' . $id . '?auto=format&fit=crop&w=720&q=80', 'id' => '' );
	};

	$geo_cards = array();
	foreach ( prime_pages_in( 'location' ) as $key => $p ) {
		$geo_cards[] = array( 'icon' => 'pin', 'title' => 'Recruitment agency in ' . $p['menu'], 'subtitle' => implode( ' · ', array_slice( $p['cities'], 0, 3 ) ), 'link' => prime_link( prime_url( $key ) ) );
	}
	$geo_cards[] = array( 'icon' => 'globe', 'title' => 'Overseas recruitment', 'subtitle' => 'UAE · Saudi · Qatar · UK', 'link' => prime_link( prime_url( 'overseas' ) ) );

	$cities = array();
	foreach ( array(
		array( 'Delhi NCR', 77.21, 28.61, 'yes', '' ),
		array( 'Mumbai', 72.88, 19.07, '', 'yes' ),
		array( 'Bangalore', 77.59, 12.97, '', 'yes' ),
		array( 'Pune', 73.86, 18.52, '', '' ),
		array( 'Hyderabad', 78.49, 17.39, '', '' ),
		array( 'Chennai', 80.27, 13.08, '', '' ),
		array( 'Kolkata', 88.36, 22.57, '', '' ),
		array( 'Ahmedabad', 72.57, 23.02, '', 'yes' ),
		array( 'Jaipur', 75.79, 26.91, '', '' ),
		array( 'Lucknow', 80.95, 26.85, '', '' ),
		array( 'Kochi', 76.27, 9.93, '', '' ),
		array( 'Chandigarh', 76.78, 30.73, '', '' ),
		array( 'Indore', 75.86, 22.72, '', '' ),
		array( 'Bhubaneswar', 85.82, 20.30, '', '' ),
	) as $c ) {
		$cities[] = array( 'name' => $c[0], 'lon' => $c[1], 'lat' => $c[2], 'hub' => $c[3], 'left' => $c[4] );
	}

	$stats = array();
	for ( $i = 1; $i <= 4; $i++ ) {
		$stats[] = array(
			'number' => prime_opt( "stat_{$i}_num" ),
			'suffix' => prime_opt( "stat_{$i}_suffix" ),
			'label'  => prime_opt( "stat_{$i}_label" ),
			'ring'   => 35 + $i * 15,
		);
	}

	return array(
		array(
			'hero',
			array(
				'eyebrow'      => 'Now hiring across India & overseas',
				'title_1'      => 'Recruitment agency',
				'title_2'      => 'in India for teams',
				'title_3'      => 'that',
				'rotator'      => 'scale., build., lead., grow., win.',
				'lead'         => 'Prime Recruitments finds, interviews and shortlists the people your business needs — permanent, contract, executive and volume hiring, delivered by specialists who answer the phone.',
				'btn1_text'    => 'Hire talent',
				'btn1_link'    => prime_link( prime_url( 'employers' ) ),
				'btn2_text'    => 'Explore jobs',
				'btn2_link'    => prime_link( prime_url( 'jobs' ) ),
				'trust_title'  => 'Recruiter-screened shortlists',
				'trust_text'   => 'Typically within 48–72 hours of your brief',
				'console_file' => 'prime/match-engine.ts',
				'console_role' => 'Senior Backend Engineer',
				'console_city' => 'Bangalore',
				'console_exp'  => '5–8 yrs',
				'matches'      => array(
					array( 'name' => 'Candidate A', 'skills' => 'Java · Kafka · AWS', 'score' => 96 ),
					array( 'name' => 'Candidate R', 'skills' => 'Go · Postgres · GCP', 'score' => 91 ),
					array( 'name' => 'Candidate S', 'skills' => 'Node · Redis · K8s', 'score' => 88 ),
				),
				'chips'        => array(
					array( 'icon' => 'users', 'title' => 'Specialists', 'text' => 'for every function' ),
					array( 'icon' => 'shield', 'title' => 'Interviewed', 'text' => 'every candidate' ),
					array( 'icon' => 'globe', 'title' => 'Pan-India', 'text' => '+ overseas' ),
				),
			),
		),
		array(
			'marquee',
			array(
				'row_1' => implode( "\n", wp_list_pluck( $services, 'menu' ) ),
				'row_2' => "Mumbai\nDelhi NCR\nBangalore\nPune\nHyderabad\nChennai\nKolkata\nDubai",
			),
		),
		array(
			'statement',
			array(
				'label_num' => '01',
				'label'     => 'Our approach',
				'text'      => 'We don’t send CVs. We send people worth meeting — interviewed, skills-checked and salary-aligned — so your managers spend their time hiring, not sifting.',
				'sig_title' => 'The Prime promise',
				'sig_text'  => 'Quality over volume, on every brief',
				'link_text' => 'About Prime',
				'link'      => prime_link( prime_url( 'about' ) ),
			),
		),
		array(
			'services_slider',
			array(
				'label_num'      => '02',
				'label'          => 'Recruitment services',
				'title'          => 'Eight ways to hire. *One accountable partner.*',
				'cards'          => $cards,
				'card_link_text' => 'Explore',
				'cta_kicker'     => '// all services',
				'cta_title'      => 'Not sure which model fits?',
				'cta_text'       => 'Tell us the role. We’ll recommend the fastest, most cost-effective route.',
				'cta_link_text'  => 'Compare services',
				'cta_link'       => prime_link( prime_url( 'services' ) ),
			),
		),
		array( 'stats', array( 'items' => $stats ) ),
		array(
			'process',
			array(
				'label_num' => '03',
				'label'     => 'How it works',
				'title'     => 'From brief to *signed offer* in four steps',
				'text'      => 'A repeatable process that keeps hiring managers informed and candidates engaged at every stage.',
				'btn_text'  => 'Share a role',
				'btn_link'  => prime_link( prime_url( 'employers' ) ),
				'steps'     => prime_steps_items( prime_default_steps() ),
			),
		),
		array(
			'industries_reel',
			array(
				'label_num' => '04',
				'label'     => 'Industries',
				'title'     => 'Specialists who *speak your industry*',
				'link_text' => 'All industries',
				'link'      => prime_link( prime_url( 'industries' ) ),
				'rows'      => array(
					array( 'title' => 'Technology', 'link' => prime_link( prime_url( 'it' ) ), 'image' => $img( 'photo-1518770660439-4636190af475' ) ),
					array( 'title' => 'BFSI & Fintech', 'link' => prime_link( prime_url( 'industries' ) ), 'image' => $img( 'photo-1554224155-6726b3ff858f' ) ),
					array( 'title' => 'Healthcare & Pharma', 'link' => prime_link( prime_url( 'healthcare' ) ), 'image' => $img( 'photo-1576091160550-2173dba999ef' ) ),
					array( 'title' => 'Manufacturing', 'link' => prime_link( prime_url( 'industries' ) ), 'image' => $img( 'photo-1565043666747-69f6646db940' ) ),
					array( 'title' => 'Retail & E-commerce', 'link' => prime_link( prime_url( 'industries' ) ), 'image' => $img( 'photo-1441986300917-64674bd600d8' ) ),
					array( 'title' => 'Logistics', 'link' => prime_link( prime_url( 'industries' ) ), 'image' => $img( 'photo-1586528116311-ad8dd3c8310d' ) ),
					array( 'title' => 'Overseas & Gulf', 'link' => prime_link( prime_url( 'overseas' ) ), 'image' => $img( 'photo-1512453979798-5ea266f8880c' ) ),
				),
			),
		),
		array(
			'why_bento',
			array(
				'label_num' => '05',
				'label'     => 'Why Prime',
				'title'     => 'Built for hiring that *actually works*',
				'intro'     => 'Recruitment is a trust business. Here is what we do differently to earn it.',
				'xl_label'  => 'Precision shortlists',
				'xl_title'  => '3–5 interviewed candidates per role. Not 50 unread CVs.',
				'funnel'    => "Market mapped\nApproached\nInterviewed\nShortlist",
				'cards'     => array(
					array( 'icon' => 'chart', 'title' => 'Salary intelligence', 'text' => 'Live benchmarks with every brief, so offers land first time.', 'style' => 'light' ),
					array( 'icon' => 'shield', 'title' => 'Replacement guarantee', 'text' => 'If a permanent hire leaves early, we replace them free.', 'style' => 'dark' ),
					array( 'icon' => 'users', 'title' => 'Function specialists', 'text' => 'Tech recruiters for tech. Finance recruiters for finance.', 'style' => 'light' ),
					array( 'icon' => 'heart', 'title' => 'Candidates trust us', 'text' => 'Honest feedback and zero fees — so top people take our calls.', 'style' => 'blue' ),
				),
			),
		),
		array( 'gifting', prime_gifting_settings( true ) ),
		array(
			'testimonials',
			array(
				'label_num' => '07',
				'label'     => 'In their words',
				'items'     => array_map(
					function ( $t ) {
						return array( 'quote' => $t[0], 'name' => $t[1], 'role' => $t[2] );
					},
					prime_testimonials()
				),
			),
		),
		array(
			'geo',
			array(
				'label_num' => '08',
				'label'     => 'Where we hire',
				'title'     => 'Recruiting across India, *placing worldwide*',
				'text'      => 'Local consultants in India’s largest hiring markets, pan-India delivery for tier-2 cities and remote roles, and overseas placements across the Gulf, UK and Europe.',
				'cards'     => $geo_cards,
				'cities'    => $cities,
			),
		),
		array(
			'jobs',
			array(
				'label_num' => '09',
				'label'     => 'Featured roles',
				'title'     => 'Latest job vacancies *worth a look*',
				'link_text' => 'View all jobs',
				'link'      => prime_link( prime_url( 'jobs' ) ),
				'jobs'      => array_slice( prime_jobs_items(), 0, 6 ),
			),
		),
		array(
			'faq',
			array(
				'label_num' => '10',
				'label'     => 'Questions, answered',
				'title'     => 'Recruitment questions, *answered*',
				'intro'     => 'Straight answers to what employers and candidates ask us most.',
				'link_text' => 'Ask us anything',
				'link'      => prime_link( prime_url( 'contact' ) ),
				'items'     => prime_faq_items( prime_home_faqs() ),
			),
		),
		array( 'cta_form', prime_cta_defaults( 'employer' ) ),
	);
}

/**
 * Layout for a landing page from inc/pages-data.php.
 */
function prime_page_sections( $key ) {
	$page = prime_page( $key );
	if ( ! $page ) {
		return array();
	}
	$type     = $page['type'];
	$is_cand  = in_array( $key, array( 'jobs', 'job-seekers' ), true );
	$cities   = isset( $page['cities'] ) ? $page['cities'] : prime_default_cities();
	$sec      = 0;
	$num      = function () use ( &$sec ) {
		$sec++;
		return prime_nn( $sec );
	};
	$out = array();

	$out[] = array(
		'banner',
		array(
			'variant'     => $page['banner'],
			'eyebrow'     => $page['eyebrow'],
			'title'       => $page['h1'],
			'crumb'       => $page['menu'],
			'lead'        => $page['lead'],
			'btn1_text'   => 'gifting' === $type ? 'Get a gifting quote' : ( $is_cand ? 'Submit your CV' : 'Get a shortlist' ),
			'btn1_link'   => prime_link( '#enquiry' ),
			'btn2_text'   => 'Read FAQs',
			'btn2_link'   => prime_link( '#faq' ),
			'chips'       => implode( "\n", $page['chips'] ),
			'show_answer' => 'yes',
			'answer_q'    => ucfirst( $page['focus_kw'] ) . '?',
			'answer_text' => $page['answer'],
		),
	);

	if ( 'contact' === $type ) {
		$out[] = array(
			'business_card',
			array(
				'label_num' => $num(),
				'label'     => 'Talk to us directly',
				'title'     => 'Your direct line to *Prime*',
				'text'      => 'Call, WhatsApp or email our team, or save our contact card to your phone. For hiring, job search and corporate gifting enquiries.',
			),
		);
	}

	if ( 'gifting' === $type ) {
		$out[] = array( 'gifting', prime_gifting_settings( false ) );
	}

	if ( ! empty( $page['intro'] ) && 'gifting' !== $type ) {
		$toc = array();
		if ( ! empty( $page['features'] ) || 'hub' === $type ) {
			$toc[] = array( 'text' => wp_strip_all_tags( str_replace( '*', '', $page['features_h'] ) ), 'anchor' => 'hub' === $type ? 'services-list' : 'features' );
		}
		if ( in_array( $type, array( 'service', 'location' ), true ) ) {
			$toc[] = array( 'text' => 'Our process', 'anchor' => 'process' );
		}
		$toc[] = array( 'text' => 'Key facts', 'anchor' => 'facts' );
		$toc[] = array( 'text' => 'FAQs', 'anchor' => 'faq' );
		$toc[] = array( 'text' => 'Get in touch', 'anchor' => 'enquiry' );

		$coverage = 'location' === $type ? $page['city'] : ( 'overseas' === $key ? 'India → Gulf, UK, Europe' : 'Pan-India + remote' );
		$out[]    = array(
			'intro',
			array(
				'label_num' => $num(),
				'label'     => 'Overview',
				'title'     => $page['intro_h'],
				'content'   => '<p>' . implode( "</p>\n<p>", array_map( 'esc_html', $page['intro'] ) ) . '</p>',
				'glance'    => array(
					array( 'label' => 'Service', 'value' => $page['menu'] ),
					array( 'label' => 'Best for', 'value' => $is_cand ? 'Job seekers' : 'Employers & HR teams' ),
					array( 'label' => 'Coverage', 'value' => $coverage ),
					array( 'label' => 'Response', 'value' => 'Within 1 working day' ),
				),
				'toc'       => $toc,
			),
		);
	}

	if ( 'hub' === $type ) {
		$cards = array();
		foreach ( prime_pages_in( 'service' ) as $sk => $sp ) {
			$cards[] = array( 'icon' => $sp['icon'], 'title' => $sp['menu'], 'text' => $sp['short'], 'keyword' => $sp['focus_kw'], 'link' => prime_link( prime_url( $sk ) ) );
		}
		$out[] = array( 'hub_grid', array( 'label_num' => $num(), 'label' => 'Service lines', 'title' => $page['features_h'], 'cards' => $cards ) );
	} elseif ( 'jobs' === $type ) {
		$out[] = array( 'jobs', array( 'label_num' => $num(), 'label' => 'Current openings', 'title' => 'Open roles *hiring now*', 'background' => 'paper', 'anchor' => 'openings', 'jobs' => prime_jobs_items() ) );
	}

	if ( ! empty( $page['features'] ) ) {
		$out[] = array(
			'features',
			array(
				'label_num' => $num(),
				'label'     => 'industries' === $type ? 'Sectors' : 'Capabilities',
				'title'     => $page['features_h'],
				'intro'     => $page['meta_desc'],
				'items'     => array_map(
					function ( $f ) {
						return array( 'icon' => $f[0], 'title' => $f[1], 'text' => $f[2] );
					},
					$page['features']
				),
			),
		);
	}

	if ( ! empty( $page['roles'] ) ) {
		$out[] = array( 'roles', array( 'label_num' => $num(), 'label' => 'Roles we hire', 'roles' => implode( "\n", $page['roles'] ) ) );
	}

	if ( in_array( $type, array( 'service', 'location', 'gifting' ), true ) ) {
		$out[] = array(
			'timeline',
			array(
				'label_num' => $num(),
				'label'     => 'Process',
				'title'     => 'location' === $type ? 'How we hire *in ' . $page['city'] . '*' : ( 'gifting' === $type ? 'From brief to doorstep in *four steps*' : 'How we deliver *' . $page['menu'] . '*' ),
				'steps'     => prime_steps_items( isset( $page['steps'] ) ? $page['steps'] : prime_default_steps() ),
			),
		);
	}

	$links = array();
	if ( 'location' !== $type ) {
		foreach ( prime_pages_in( 'location' ) as $lk => $l ) {
			$links[] = array( 'text' => ucfirst( $l['focus_kw'] ), 'link' => prime_link( prime_url( $lk ) ) );
		}
	}
	$out[] = array(
		'facts',
		array(
			'label_num'       => $num(),
			'label'           => 'Key facts',
			'title'           => $page['menu'] . ': the essentials',
			'facts'           => implode( "\n", $page['facts'] ),
			'areas_label_num' => $num(),
			'areas_label'     => 'overseas' === $key ? 'Countries we recruit for' : 'Areas served',
			'areas_title'     => 'location' === $type ? 'Covering every corner of *' . $page['city'] . '*' : ( 'overseas' === $key ? 'Placing Indian talent *worldwide*' : 'Hiring *across India*' ),
			'areas'           => implode( "\n", $cities ),
			'links'           => $links,
		),
	);

	$out[] = array(
		'faq',
		array(
			'label_num' => $num(),
			'label'     => 'Questions, answered',
			'title'     => ucfirst( $page['focus_kw'] ) . ': *FAQs*',
			'intro'     => 'Straight answers to what employers and candidates ask us most.',
			'link_text' => 'Ask us anything',
			'link'      => prime_link( prime_url( 'contact' ) ),
			'items'     => prime_faq_items( $page['faqs'] ),
		),
	);

	if ( ! empty( $page['related'] ) ) {
		$cards = array();
		foreach ( $page['related'] as $rk ) {
			$r = prime_page( $rk );
			if ( $r ) {
				$cards[] = array( 'icon' => $r['icon'], 'kicker' => $r['eyebrow'], 'title' => wp_strip_all_tags( str_replace( '*', '', $r['h1'] ) ), 'link' => prime_link( prime_url( $rk ) ) );
			}
		}
		$out[] = array( 'related', array( 'label_num' => $num(), 'label' => 'Keep exploring', 'cards' => $cards ) );
	}

	$preset       = 'gifting' === $type ? 'gifting' : ( $is_cand ? 'candidate' : 'employer' );
	$cta          = prime_cta_defaults( $preset );
	$cta['title'] = 'gifting' === $type ? 'Plan your next gifting *campaign.*' : ( $is_cand ? 'Your next role *starts here.*' : 'Ready to hire? *Let’s talk.*' );
	if ( 'contact' === $type ) {
		$cta['show_contacts'] = ''; // Already on the business card above.
	}
	$out[]        = array( 'cta_form', $cta );

	return $out;
}

/* ---------- small builders ---------- */

function prime_steps_items( $steps ) {
	return array_map(
		function ( $s ) {
			return array( 'title' => $s[0], 'text' => $s[1] );
		},
		$steps
	);
}

function prime_faq_items( $faqs ) {
	return array_map(
		function ( $f ) {
			return array( 'question' => $f[0], 'answer' => $f[1] );
		},
		$faqs
	);
}

function prime_jobs_items() {
	return array_map(
		function ( $j ) {
			return array( 'title' => $j[0], 'category' => $j[1], 'location' => $j[2], 'type' => $j[3], 'experience' => $j[4], 'summary' => $j[5], 'link' => prime_link( prime_url( 'job-seekers' ) . '#enquiry' ) );
		},
		prime_jobs()
	);
}

function prime_cta_defaults( $preset ) {
	return array(
		'label_num'     => 'Contact',
		'label'         => 'Start a conversation',
		'title'         => 'Tell us who you need. *We’ll take it from there.*',
		'lead'          => 'Share a role or your CV. A recruiter replies within one working day — no obligation, no spam.',
		'points'        => "A dedicated consultant from brief to joining\nTransparent, success-based pricing\nNever a fee for job seekers",
		'show_contacts' => 'yes',
		'preset'        => $preset,
	);
}

/**
 * Default settings for a widget: the first layout in which it appears.
 * Used as Elementor control defaults, so a freshly dragged-in widget arrives
 * fully designed with sample content.
 */
function prime_widget_defaults( $widget ) {
	static $cache = null;
	if ( null === $cache ) {
		$cache   = array();
		$layouts = array( prime_home_sections() );
		foreach ( array( 'permanent', 'services', 'about', 'contact', 'jobs' ) as $k ) {
			$layouts[] = prime_page_sections( $k );
		}
		foreach ( $layouts as $layout ) {
			foreach ( $layout as $sec ) {
				if ( ! isset( $cache[ $sec[0] ] ) ) {
					$cache[ $sec[0] ] = $sec[1];
				}
			}
		}
	}
	$out = isset( $cache[ $widget ] ) ? $cache[ $widget ] : array();
	if ( 'banner' === $widget ) {
		unset( $out['crumb'] ); // New pages show their own title in the breadcrumb.
	}
	return $out;
}

/**
 * Corporate gifting section settings (homepage version links to the page).
 */
function prime_gifting_settings( $home ) {
	$g = prime_page( 'gifting' );
	return array(
		'label_num'   => $home ? '06' : '01',
		'label'       => 'Corporate gifting',
		'title'       => $g['intro_h'],
		'text'        => implode( "\n", $g['intro'] ),
		'list_title'  => 'Popular corporate gifts in India include:',
		'items'       => implode( "\n", $g['gifts'] ),
		'ideal_title' => 'Ideal for',
		'ideal'       => implode( "\n", $g['ideal'] ),
		'badges'      => implode( "\n", $g['badges'] ),
		'btn_text'    => 'Get a gifting quote',
		'btn_link'    => prime_link( '#enquiry' ),
		'btn2_text'   => $home ? 'Explore corporate gifting' : '',
		'btn2_link'   => prime_link( $home ? prime_url( 'gifting' ) : '' ),
	);
}
