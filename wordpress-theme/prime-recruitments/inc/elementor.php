<?php
/**
 * Elementor integration.
 *
 * Registers a "Prime Recruitments" widget category with one widget per
 * designed section. Every text, link, image, list and repeater item is an
 * Elementor control, and every widget has a Style tab (accent colour,
 * background, heading/text colour, heading typography, spacing) on top of
 * Elementor's standard Advanced tab.
 *
 * @package Prime
 */

defined( 'ABSPATH' ) || exit;

/**
 * Widget definitions: key => title, Elementor icon, fields.
 * Field: name => array( type, label [, extra args] ).
 * Repeater field: name => array( 'repeater', label, array( 'fields' => [...], 'title' => 'field' ) ).
 */
function prime_widget_schemas() {
	$label  = array( 'label' => array( 'text', 'Eyebrow label (small gold text above the heading)' ) );
	$title  = array( 'title' => array( 'text', 'Heading (wrap words in *asterisks* for the gold italic accent)' ) );
	$anchor = array( 'anchor' => array( 'text', 'Section anchor ID (for #links)' ) );
	$photos = array(
		'facade'   => 'Building facade (dark)',
		'tower'    => 'Tower & sky',
		'windows'  => 'Windows pattern',
		'geometry' => 'Geometric facade',
		'atrium'   => 'Atrium roof',
		'corridor' => 'Gallery corridor',
		'stairs'   => 'Spiral staircase',
	);
	$preset = array( 'select', 'Popup opens on (when the link is #enquiry)', array( 'options' => array( '' => 'Page default', 'employer' => 'I’m hiring', 'candidate' => 'I need a job', 'gifting' => 'Corporate gifting' ) ) );

	return array(
		'hero'            => array(
			'title'  => 'Hero (homepage)',
			'icon'   => 'eicon-header',
			'fields' => array(
				'image'       => array( 'media', 'Background photo (empty = built-in photo)' ),
				'eyebrow'     => array( 'text', 'Eyebrow' ),
				'title_1'     => array( 'text', 'Headline line 1' ),
				'title_2'     => array( 'text', 'Headline line 2' ),
				'title_3'     => array( 'text', 'Headline line 3 (before the rotating word)' ),
				'rotator'     => array( 'text', 'Rotating words (comma separated)' ),
				'lead'        => array( 'textarea', 'Intro text' ),
				'btn1_text'   => array( 'text', 'Primary button text' ),
				'btn1_link'   => array( 'url', 'Primary button link (#enquiry = popup)' ),
				'btn2_text'   => array( 'text', 'Secondary button text' ),
				'btn2_link'   => array( 'url', 'Secondary button link' ),
				'trust_title' => array( 'text', 'Trust line – bold part' ),
				'trust_text'  => array( 'text', 'Trust line – text' ),
				'panel_title' => array( 'text', 'Right card – title' ),
				'panel_text'  => array( 'text', 'Right card – text' ),
				'options'     => array( 'repeater', 'Right card – options', array( 'title' => 'title', 'fields' => array( 'icon' => array( 'icon', 'Icon' ), 'title' => array( 'text', 'Title' ), 'text' => array( 'text', 'Text' ), 'link' => array( 'url', 'Link (#enquiry = popup)' ), 'preset' => $preset ) ) ),
			),
		),
		'stats'           => array(
			'title'  => 'Stats band',
			'icon'   => 'eicon-counter',
			'fields' => array(
				'title' => array( 'text', 'Small heading (optional)' ),
				'style' => array( 'select', 'Style', array( 'options' => array( 'light' => 'Light', 'dark' => 'Dark' ) ) ),
				'items' => array( 'repeater', 'Stats', array( 'title' => 'label', 'fields' => array( 'number' => array( 'text', 'Number' ), 'suffix' => array( 'text', 'Suffix' ), 'label' => array( 'text', 'Label' ) ) ) ),
			),
		),
		'about_split'     => array(
			'title'  => 'Image + text (about)',
			'icon'   => 'eicon-image-box',
			'fields' => $label + $title + $anchor + array(
				'image'      => array( 'media', 'Photo (empty = built-in photo)' ),
				'reverse'    => array( 'switcher', 'Photo on the right' ),
				'background' => array( 'select', 'Background', array( 'options' => array( 'white' => 'White', 'sand' => 'Warm ivory' ) ) ),
				'badge_num'  => array( 'text', 'Photo badge – number' ),
				'badge_text' => array( 'text', 'Photo badge – text' ),
				'text'       => array( 'textarea', 'Text (one paragraph per line)' ),
				'points'     => array( 'textarea', 'Checklist (one per line)' ),
				'sig_title'  => array( 'text', 'Signature – title' ),
				'sig_text'   => array( 'text', 'Signature – text' ),
				'link_text'  => array( 'text', 'Button text' ),
				'link'       => array( 'url', 'Button link' ),
			),
		),
		'services_slider' => array(
			'title'  => 'Services grid',
			'icon'   => 'eicon-gallery-grid',
			'fields' => $label + $title + $anchor + array(
				'intro'          => array( 'textarea', 'Intro (right of the heading)' ),
				'cards'          => array( 'repeater', 'Service cards', array( 'title' => 'title', 'fields' => array( 'icon' => array( 'icon', 'Icon' ), 'title' => array( 'text', 'Title' ), 'text' => array( 'textarea', 'Text' ), 'link' => array( 'url', 'Link' ) ) ) ),
				'card_link_text' => array( 'text', 'Card link text' ),
				'cta_kicker'     => array( 'text', 'Dark card – kicker' ),
				'cta_title'      => array( 'text', 'Dark card – title' ),
				'cta_text'       => array( 'textarea', 'Dark card – text' ),
				'cta_link_text'  => array( 'text', 'Dark card – link text' ),
				'cta_link'       => array( 'url', 'Dark card – link' ),
			),
		),
		'process'         => array(
			'title'  => 'Process steps',
			'icon'   => 'eicon-time-line',
			'fields' => $label + $title + array(
				'text'     => array( 'textarea', 'Text' ),
				'btn_text' => array( 'text', 'Button text' ),
				'btn_link' => array( 'url', 'Button link' ),
				'steps'    => array( 'repeater', 'Steps', array( 'title' => 'title', 'fields' => array( 'title' => array( 'text', 'Title' ), 'text' => array( 'textarea', 'Text' ) ) ) ),
			),
		),
		'industries_reel' => array(
			'title'  => 'Industries hover list',
			'icon'   => 'eicon-bullet-list',
			'fields' => $label + $title + array(
				'intro'     => array( 'textarea', 'Intro (optional)' ),
				'link_text' => array( 'text', 'Link text' ),
				'link'      => array( 'url', 'Link' ),
				'rows'      => array( 'repeater', 'Rows', array( 'title' => 'title', 'fields' => array( 'title' => array( 'text', 'Title' ), 'link' => array( 'url', 'Link' ), 'image' => array( 'media', 'Hover image' ) ) ) ),
			),
		),
		'why_bento'       => array(
			'title'  => 'Why us grid',
			'icon'   => 'eicon-gallery-masonry',
			'fields' => $label + $title + array(
				'intro'    => array( 'textarea', 'Intro' ),
				'xl_label' => array( 'text', 'Large card – label' ),
				'xl_title' => array( 'textarea', 'Large card – title' ),
				'funnel'   => array( 'textarea', 'Large card – funnel steps (one per line)' ),
				'cards'    => array( 'repeater', 'Small cards (4)', array( 'title' => 'title', 'fields' => array( 'icon' => array( 'icon', 'Icon' ), 'title' => array( 'text', 'Title' ), 'text' => array( 'textarea', 'Text' ), 'style' => array( 'select', 'Style', array( 'options' => array( 'light' => 'White', 'dark' => 'Charcoal', 'blue' => 'Gold' ) ) ) ) ) ),
			),
		),
		'audience_split'  => array(
			'title'  => 'Employers / job seekers split',
			'icon'   => 'eicon-column',
			'fields' => array(
				'label'  => array( 'text', 'Section name (for screen readers)' ),
				'panels' => array( 'repeater', 'Panels (2)', array( 'title' => 'label', 'fields' => array( 'image' => array( 'media', 'Photo (empty = built-in)' ), 'label' => array( 'text', 'Eyebrow' ), 'title' => array( 'text', 'Heading' ), 'text' => array( 'textarea', 'Text' ), 'points' => array( 'textarea', 'Checklist (one per line)' ), 'btn_text' => array( 'text', 'Button text' ), 'btn_link' => array( 'url', 'Button link' ), 'preset' => $preset ) ) ),
			),
		),
		'testimonials'    => array(
			'title'  => 'Testimonials',
			'icon'   => 'eicon-testimonial-carousel',
			'fields' => $label + $title + array(
				'items' => array( 'repeater', 'Testimonials', array( 'title' => 'name', 'fields' => array( 'quote' => array( 'textarea', 'Quote' ), 'name' => array( 'text', 'Name / title' ), 'role' => array( 'text', 'Company / place' ) ) ) ),
			),
		),
		'geo'             => array(
			'title'  => 'Locations + India map',
			'icon'   => 'eicon-map-pin',
			'fields' => $label + $title + $anchor + array(
				'text'   => array( 'textarea', 'Text' ),
				'cards'  => array( 'repeater', 'Location cards', array( 'title' => 'title', 'fields' => array( 'icon' => array( 'icon', 'Icon' ), 'title' => array( 'text', 'Title' ), 'subtitle' => array( 'text', 'Subtitle' ), 'link' => array( 'url', 'Link' ) ) ) ),
				'cities' => array( 'repeater', 'Map cities', array( 'title' => 'name', 'fields' => array( 'name' => array( 'text', 'City' ), 'lon' => array( 'number', 'Longitude', array( 'step' => 0.01 ) ), 'lat' => array( 'number', 'Latitude', array( 'step' => 0.01 ) ), 'hub' => array( 'switcher', 'Hub (lines start here)' ), 'left' => array( 'switcher', 'Label on the left' ) ) ) ),
			),
		),
		'jobs'            => array(
			'title'  => 'Job listings (filterable)',
			'icon'   => 'eicon-posts-grid',
			'fields' => $label + $title + $anchor + array(
				'link_text'  => array( 'text', 'Link text' ),
				'link'       => array( 'url', 'Link' ),
				'background' => array( 'select', 'Background', array( 'options' => array( 'white' => 'White', 'paper' => 'Warm ivory' ) ) ),
				'all_label'  => array( 'text', '"All" filter label' ),
				'jobs'       => array( 'repeater', 'Jobs', array( 'title' => 'title', 'fields' => array( 'title' => array( 'text', 'Job title' ), 'category' => array( 'text', 'Category (used for filters)' ), 'location' => array( 'text', 'Location' ), 'type' => array( 'text', 'Type' ), 'experience' => array( 'text', 'Experience' ), 'summary' => array( 'textarea', 'Summary' ), 'ref' => array( 'text', 'Reference' ), 'link' => array( 'url', 'Apply link' ) ) ) ),
			),
		),
		'faq'             => array(
			'title'  => 'FAQ (with FAQ schema)',
			'icon'   => 'eicon-accordion',
			'fields' => $label + $title + array(
				'intro'     => array( 'textarea', 'Intro' ),
				'link_text' => array( 'text', 'Contact link text' ),
				'link'      => array( 'url', 'Contact link' ),
				'items'     => array( 'repeater', 'Questions', array( 'title' => 'question', 'fields' => array( 'question' => array( 'text', 'Question' ), 'answer' => array( 'textarea', 'Answer' ) ) ) ),
			),
		),
		'cta_form'        => array(
			'title'  => 'CTA + enquiry form',
			'icon'   => 'eicon-form-horizontal',
			'fields' => $label + $title + array(
				'lead'           => array( 'textarea', 'Text' ),
				'points'         => array( 'textarea', 'Checklist (one per line)' ),
				'show_contacts'  => array( 'switcher', 'Show email & phone' ),
				'preset'         => array( 'select', 'Form opens on', array( 'options' => array( 'employer' => 'I’m hiring', 'candidate' => 'I need a job', 'gifting' => 'Corporate gifting' ) ) ),
				'form_shortcode' => array( 'text', 'Form shortcode (optional – e.g. [fluentform id="3"]). Empty = Customizer form.' ),
			),
		),
		'cta_band'        => array(
			'title'  => 'Call-to-action band (photo)',
			'icon'   => 'eicon-call-to-action',
			'fields' => $label + $title + array(
				'image'         => array( 'media', 'Background photo (empty = built-in)' ),
				'text'          => array( 'textarea', 'Text' ),
				'btn_text'      => array( 'text', 'Button text' ),
				'btn_link'      => array( 'url', 'Button link (#enquiry = popup)' ),
				'preset'        => $preset,
				'show_call'     => array( 'switcher', 'Show call button' ),
				'show_whatsapp' => array( 'switcher', 'Show WhatsApp button' ),
			),
		),
		'banner'          => array(
			'title'  => 'Page banner',
			'icon'   => 'eicon-banner',
			'fields' => array(
				'variant'     => array( 'select', 'Background photo', array( 'options' => $photos ) ),
				'image'       => array( 'media', 'Your own photo (overrides the choice above)' ),
				'show_crumbs' => array( 'switcher', 'Show breadcrumb' ),
				'crumb'       => array( 'text', 'Breadcrumb label (empty = page title)' ),
				'eyebrow'     => array( 'text', 'Eyebrow' ),
				'title'       => array( 'textarea', 'H1 heading (*asterisks* = gold italic accent)' ),
				'lead'        => array( 'textarea', 'Intro text' ),
				'btn1_text'   => array( 'text', 'Primary button text' ),
				'btn1_link'   => array( 'url', 'Primary button link' ),
				'btn2_text'   => array( 'text', 'Secondary button text' ),
				'btn2_link'   => array( 'url', 'Secondary button link' ),
				'chips'       => array( 'textarea', 'Highlights (one per line)' ),
				'show_answer' => array( 'switcher', 'Show quick-answer card (AEO)' ),
				'answer_q'    => array( 'text', 'Quick answer – question' ),
				'answer_text' => array( 'textarea', 'Quick answer – answer (40–60 words)' ),
			),
		),
		'contact_cards'   => array(
			'title'  => 'Contact cards',
			'icon'   => 'eicon-info-circle-o',
			'fields' => array(
				'items' => array( 'repeater', 'Cards', array( 'title' => 'label', 'fields' => array( 'icon' => array( 'icon', 'Icon' ), 'label' => array( 'text', 'Label' ), 'value' => array( 'text', 'Value' ), 'link' => array( 'url', 'Link (optional)' ) ) ) ),
			),
		),
		'intro'           => array(
			'title'  => 'Intro + at a glance',
			'icon'   => 'eicon-text',
			'fields' => $label + $title + array(
				'content'      => array( 'wysiwyg', 'Text' ),
				'glance_title' => array( 'text', 'Side card title' ),
				'glance'       => array( 'repeater', 'At a glance rows', array( 'title' => 'label', 'fields' => array( 'label' => array( 'text', 'Label' ), 'value' => array( 'text', 'Value' ) ) ) ),
				'toc'          => array( 'repeater', 'On this page links', array( 'title' => 'text', 'fields' => array( 'text' => array( 'text', 'Text' ), 'anchor' => array( 'text', 'Anchor ID (e.g. faq)' ) ) ) ),
			),
		),
		'hub_grid'        => array(
			'title'  => 'Service card grid',
			'icon'   => 'eicon-apps',
			'fields' => $label + $title + $anchor + array(
				'cards' => array( 'repeater', 'Cards', array( 'title' => 'title', 'fields' => array( 'icon' => array( 'icon', 'Icon' ), 'title' => array( 'text', 'Title' ), 'text' => array( 'textarea', 'Text' ), 'keyword' => array( 'text', 'Keyword tag' ), 'link' => array( 'url', 'Link' ) ) ) ),
			),
		),
		'features'        => array(
			'title'  => 'Feature grid (dark)',
			'icon'   => 'eicon-info-box',
			'fields' => $label + $title + $anchor + array(
				'intro' => array( 'textarea', 'Intro' ),
				'items' => array( 'repeater', 'Features', array( 'title' => 'title', 'fields' => array( 'icon' => array( 'icon', 'Icon' ), 'title' => array( 'text', 'Title' ), 'text' => array( 'textarea', 'Text' ) ) ) ),
			),
		),
		'roles'           => array(
			'title'  => 'Roles ticker',
			'icon'   => 'eicon-tags',
			'fields' => $label + array( 'roles' => array( 'textarea', 'Roles (one per line)' ) ),
		),
		'timeline'        => array(
			'title'  => 'Process timeline',
			'icon'   => 'eicon-navigation-horizontal',
			'fields' => $label + $title + $anchor + array(
				'steps' => array( 'repeater', 'Steps', array( 'title' => 'title', 'fields' => array( 'title' => array( 'text', 'Title' ), 'text' => array( 'textarea', 'Text' ) ) ) ),
			),
		),
		'facts'           => array(
			'title'  => 'Key facts + areas served',
			'icon'   => 'eicon-checkbox',
			'fields' => $label + $anchor + array(
				'title'       => array( 'text', 'Facts heading' ),
				'facts'       => array( 'textarea', 'Key facts (one per line – short, quotable statements)' ),
				'areas_label' => array( 'text', 'Areas – eyebrow' ),
				'areas_title' => array( 'text', 'Areas – heading' ),
				'areas'       => array( 'textarea', 'Areas (one per line)' ),
				'links'       => array( 'repeater', 'Area links', array( 'title' => 'text', 'fields' => array( 'text' => array( 'text', 'Text' ), 'link' => array( 'url', 'Link' ) ) ) ),
			),
		),
		'gifting'         => array(
			'title'  => 'Corporate gifting showcase',
			'icon'   => 'eicon-gallery-justified',
			'fields' => $label + $title + $anchor + array(
				'text'        => array( 'textarea', 'Text (one paragraph per line)' ),
				'image'       => array( 'media', 'Left image (optional – empty shows the built-in gift illustration)' ),
				'list_title'  => array( 'text', 'List heading' ),
				'items'       => array( 'textarea', 'Gift list (one per line)' ),
				'ideal_title' => array( 'text', '"Ideal for" label' ),
				'ideal'       => array( 'textarea', 'Ideal for (one per line)' ),
				'badges'      => array( 'textarea', 'Badges (one per line)' ),
				'btn_text'    => array( 'text', 'Button text (opens the enquiry popup)' ),
				'btn_link'    => array( 'url', 'Button link (#enquiry = popup)' ),
				'btn2_text'   => array( 'text', 'Secondary link text' ),
				'btn2_link'   => array( 'url', 'Secondary link' ),
			),
		),
		'business_card'   => array(
			'title'  => 'Business card + quick contact',
			'icon'   => 'eicon-person',
			'fields' => $label + $title + $anchor + array(
				'text'   => array( 'textarea', 'Text' ),
				'person' => array( 'text', 'Name on card (empty = Customizer contact person)' ),
				'role'   => array( 'text', 'Role on card (empty = Customizer role)' ),
			),
		),
		'related'         => array(
			'title'  => 'Related pages',
			'icon'   => 'eicon-flip-box',
			'fields' => $label + array(
				'cards' => array( 'repeater', 'Cards', array( 'title' => 'title', 'fields' => array( 'icon' => array( 'icon', 'Icon' ), 'kicker' => array( 'text', 'Kicker' ), 'title' => array( 'text', 'Title' ), 'link' => array( 'url', 'Link' ) ) ) ),
			),
		),
		'statement'       => array(
			'title'  => 'Statement (large quote)',
			'icon'   => 'eicon-blockquote',
			'fields' => $label + array(
				'text'      => array( 'textarea', 'Statement (lights up word by word on scroll)' ),
				'sig_title' => array( 'text', 'Signature title' ),
				'sig_text'  => array( 'text', 'Signature text' ),
				'link_text' => array( 'text', 'Link text' ),
				'link'      => array( 'url', 'Link' ),
			),
		),
		'marquee'         => array(
			'title'  => 'Text ticker',
			'icon'   => 'eicon-animation-text',
			'fields' => array(
				'row_1' => array( 'textarea', 'Row 1 (one item per line)' ),
				'row_2' => array( 'textarea', 'Row 2 (one item per line)' ),
			),
		),
	);
}

/**
 * Category.
 */
function prime_elementor_category( $manager ) {
	$manager->add_category(
		'prime',
		array(
			'title' => __( 'Prime Recruitments', 'prime' ),
			'icon'  => 'eicon-star',
		)
	);
}
add_action( 'elementor/elements/categories_registered', 'prime_elementor_category' );

/**
 * Widgets.
 */
function prime_elementor_widgets( $widgets_manager ) {
	require_once PRIME_DIR . '/inc/elementor-widgets.php';
	foreach ( array_keys( prime_widget_schemas() ) as $key ) {
		$class = 'Prime_Widget_' . str_replace( ' ', '_', ucwords( str_replace( '_', ' ', $key ) ) );
		if ( class_exists( $class ) ) {
			$widgets_manager->register( new $class() );
		}
	}
}
add_action( 'elementor/widgets/register', 'prime_elementor_widgets' );

/**
 * Is this post built with Elementor and Elementor active?
 */
function prime_is_elementor_page( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	if ( ! $post_id || ! did_action( 'elementor/loaded' ) || ! class_exists( '\Elementor\Plugin' ) ) {
		return false;
	}
	$doc = \Elementor\Plugin::$instance->documents->get( $post_id );
	return $doc && $doc->is_built_with_elementor();
}

/**
 * Render a page's saved Elementor layout without Elementor (fallback), or the
 * default layout if none is saved. Keeps the site working if Elementor is off.
 */
function prime_render_saved_layout( $post_id, $default_sections ) {
	$raw  = get_post_meta( $post_id, '_elementor_data', true );
	$data = $raw ? json_decode( is_string( $raw ) ? $raw : wp_json_encode( $raw ), true ) : null;
	if ( ! is_array( $data ) || ! $data ) {
		foreach ( $default_sections as $sec ) {
			prime_render_section( $sec[0], $sec[1] );
		}
		return;
	}
	prime_render_elements( $data );
}

function prime_render_elements( $elements ) {
	foreach ( $elements as $el ) {
		if ( isset( $el['elType'] ) && 'widget' === $el['elType'] ) {
			$type     = isset( $el['widgetType'] ) ? $el['widgetType'] : '';
			$settings = isset( $el['settings'] ) && is_array( $el['settings'] ) ? $el['settings'] : array();
			if ( 0 === strpos( $type, 'prime-' ) ) {
				$key = str_replace( '-', '_', substr( $type, 6 ) );
				prime_render_section( $key, array_merge( prime_widget_defaults( $key ), $settings ) );
			} elseif ( in_array( $type, array( 'text-editor', 'html', 'heading', 'shortcode' ), true ) ) {
				$html = isset( $settings['editor'] ) ? $settings['editor'] : ( isset( $settings['html'] ) ? $settings['html'] : ( isset( $settings['title'] ) ? '<h2>' . $settings['title'] . '</h2>' : ( isset( $settings['shortcode'] ) ? do_shortcode( $settings['shortcode'] ) : '' ) ) );
				echo '<section class="section"><div class="container prose">' . wp_kses_post( $html ) . '</div></section>';
			}
		} elseif ( ! empty( $el['elements'] ) ) {
			prime_render_elements( $el['elements'] );
		}
	}
}

/**
 * Build Elementor JSON for a list of sections: one full-width, gap-less
 * section per widget so each appears as its own row in the Navigator.
 */
function prime_build_elementor_data( $sections ) {
	$id   = function () {
		return substr( md5( uniqid( '', true ) . wp_rand() ), 0, 7 );
	};
	$data = array();
	foreach ( $sections as $sec ) {
		list( $key, $settings ) = $sec;
		foreach ( $settings as $k => $v ) {
			if ( is_array( $v ) && isset( $v[0] ) && is_array( $v[0] ) ) {
				foreach ( $settings[ $k ] as $i => $item ) {
					$settings[ $k ][ $i ]['_id'] = $id();
				}
			}
		}
		$data[] = array(
			'id'       => $id(),
			'elType'   => 'section',
			'isInner'  => false,
			'settings' => array(
				'layout'      => 'full_width',
				'gap'         => 'no',
				'css_classes' => 'prime-wrap',
				'_title'      => prime_widget_schemas()[ $key ]['title'],
			),
			'elements' => array(
				array(
					'id'       => $id(),
					'elType'   => 'column',
					'isInner'  => false,
					'settings' => array( '_column_size' => 100, '_inline_size' => null ),
					'elements' => array(
						array(
							'id'         => $id(),
							'elType'     => 'widget',
							'widgetType' => 'prime-' . str_replace( '_', '-', $key ),
							'isInner'    => false,
							'settings'   => $settings,
							'elements'   => array(),
						),
					),
				),
			),
		);
	}
	return $data;
}

/**
 * Write an Elementor layout to a page.
 */
function prime_write_elementor_layout( $post_id, $sections ) {
	$json = wp_json_encode( prime_build_elementor_data( $sections ) );
	// Keep the previous layout if it was edited, so a reset can be undone.
	$old = (string) get_post_meta( $post_id, '_elementor_data', true );
	if ( '' !== $old && ! prime_layout_untouched( $post_id ) ) {
		update_post_meta( $post_id, '_prime_layout_backup', wp_slash( $old ) );
	}
	update_post_meta( $post_id, '_elementor_data', wp_slash( $json ) );
	update_post_meta( $post_id, '_prime_layout_sig', md5( $json ) );
	update_post_meta( $post_id, '_elementor_edit_mode', 'builder' );
	update_post_meta( $post_id, '_elementor_template_type', 'wp-page' );
	update_post_meta( $post_id, '_elementor_version', defined( 'ELEMENTOR_VERSION' ) ? ELEMENTOR_VERSION : '3.25.0' );
	update_post_meta( $post_id, '_wp_page_template', 'default' );
	delete_post_meta( $post_id, '_elementor_css' );
	delete_post_meta( $post_id, '_elementor_page_assets' );
	delete_post_meta( $post_id, '_elementor_element_cache' );
}

/**
 * Clear Elementor's generated CSS after layouts change.
 */
function prime_clear_elementor_cache() {
	if ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance->files_manager ) ) {
		\Elementor\Plugin::$instance->files_manager->clear_cache();
	}
}

/**
 * Elementor settings that suit the theme (set once, on activation).
 */
function prime_configure_elementor() {
	$cpt = get_option( 'elementor_cpt_support', array( 'page', 'post' ) );
	if ( ! in_array( 'page', (array) $cpt, true ) ) {
		$cpt[] = 'page';
		update_option( 'elementor_cpt_support', $cpt );
	}
	// Let the theme's fonts and colours lead instead of Elementor's defaults.
	update_option( 'elementor_disable_color_schemes', 'yes' );
	update_option( 'elementor_disable_typography_schemes', 'yes' );
	// Skip Elementor's onboarding redirect.
	delete_transient( 'elementor_activation_redirect' );
}
add_action( 'elementor/loaded', function () {
	if ( get_option( 'prime_elementor_configured' ) ) {
		return;
	}
	prime_configure_elementor();
	update_option( 'prime_elementor_configured', 1 );
} );

/**
 * Admin notice recommending Elementor.
 */
function prime_elementor_notice() {
	if ( did_action( 'elementor/loaded' ) || ! current_user_can( 'install_plugins' ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( $screen && in_array( $screen->id, array( 'update', 'plugin-install' ), true ) ) {
		return;
	}
	echo '<div class="notice notice-info"><p><strong>Prime Recruitments:</strong> ' . esc_html__( 'Install the free Elementor plugin to edit every page visually. Your pages are already built for it — they will open in Elementor with all sections ready.', 'prime' ) . ' <a class="button button-primary" href="' . esc_url( admin_url( 'plugin-install.php?s=elementor&tab=search&type=term' ) ) . '">' . esc_html__( 'Install Elementor', 'prime' ) . '</a></p></div>';
}
add_action( 'admin_notices', 'prime_elementor_notice' );
