<?php
/**
 * Customizer: contact details, Fluent Forms, stats and socials.
 * Appearance → Customize → Prime Recruitments.
 *
 * @package Prime
 */

defined( 'ABSPATH' ) || exit;

/**
 * Defaults. Replace the placeholder contact details and stats with your real
 * figures before going live.
 */
function prime_opt_defaults() {
	return array(
		'brand_color'     => '#c9a227',
		'modal_image'     => '',
		'fluentform_id'   => '',
		'form_recipient'  => '',
		'logo_url'        => '',
		'logo_link'       => '',
		'logo_height'     => '46',
		'contact_person'  => 'Fahim Dalla',
		'contact_role'    => 'HR Manager',
		'contact_email'   => 'hr@primerecruitments.com',
		'careers_email'   => 'hr@primerecruitments.com',
		'contact_phone'   => '+91 82912 22211',
		'whatsapp'        => '918291222211',
		'whatsapp_text'   => 'Hi Prime Recruitments, I would like to know more about your services.',
		'website'         => 'www.primerecruitments.com',
		'contact_address' => 'Prime Recruitments, Pan India Recruitment Solutions',
		'contact_hours'   => 'Mon – Sat, 9:30 AM – 6:30 PM IST',
		'header_cta_text' => 'Hire Talent',
		'social_linkedin' => '',
		'social_instagram'=> '',
		'social_facebook' => '',
		'social_x'        => '',
		'social_youtube'  => '',
		'stat_1_num'      => '48',
		'stat_1_suffix'   => 'hrs',
		'stat_1_label'    => 'Target time to first shortlist',
		'stat_2_num'      => '18',
		'stat_2_suffix'   => '+',
		'stat_2_label'    => 'Industries and functions covered',
		'stat_3_num'      => '3',
		'stat_3_suffix'   => '-step',
		'stat_3_label'    => 'Screening before you meet anyone',
		'stat_4_num'      => '100',
		'stat_4_suffix'   => '%',
		'stat_4_label'    => 'Candidates personally interviewed',
	);
}

/**
 * Read a theme option with its default.
 */
function prime_opt( $key ) {
	$defaults = prime_opt_defaults();
	$default  = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
	return get_theme_mod( 'prime_' . $key, $default );
}

/**
 * Register Customizer controls.
 */
function prime_customize_register( $wp_customize ) {
	$wp_customize->add_panel(
		'prime_panel',
		array(
			'title'    => __( 'Prime Recruitments', 'prime' ),
			'priority' => 30,
		)
	);

	$sections = array(
		'prime_logo'    => array(
			'title'  => __( 'Logo', 'prime' ),
			'fields' => array(
				'logo_url'    => array( __( 'Logo image (upload or paste a URL). Use a light/transparent logo – the header and footer are dark.', 'prime' ), 'image', '' ),
				'logo_link'   => array( __( 'Logo link URL (empty = homepage)', 'prime' ), 'url', '' ),
				'logo_height' => array( __( 'Logo height in px (24–120)', 'prime' ), 'number', '' ),
			),
		),
		'prime_colors'  => array(
			'title'  => __( 'Brand colour', 'prime' ),
			'fields' => array(
				'brand_color' => array( __( 'Accent colour (buttons, links, highlights)', 'prime' ), 'color', '' ),
			),
		),
		'prime_form'    => array(
			'title'  => __( 'Contact form (Fluent Forms)', 'prime' ),
			'fields' => array(
				'fluentform_id'  => array( __( 'Fluent Forms form ID', 'prime' ), 'text', __( 'Filled in automatically: when Fluent Forms is active the theme creates a "Prime Recruitments Enquiry" form and connects it. To use a different form, paste its ID (the number in [fluentform id="3"]). Empty = built-in form.', 'prime' ) ),
				'form_recipient' => array( __( 'Built-in form: send enquiries to', 'prime' ), 'email', __( 'Defaults to the site admin email.', 'prime' ) ),
				'modal_image'    => array( __( 'Popup – left side image (optional, shown behind a navy overlay)', 'prime' ), 'image', '' ),
			),
		),
		'prime_contact' => array(
			'title'  => __( 'Contact details', 'prime' ),
			'fields' => array(
				'contact_person'  => array( __( 'Contact person (business card)', 'prime' ), 'text', '' ),
				'contact_role'    => array( __( 'Contact person – role', 'prime' ), 'text', '' ),
				'website'         => array( __( 'Website (shown on the business card)', 'prime' ), 'text', '' ),
				'contact_email'   => array( __( 'Email', 'prime' ), 'email', '' ),
				'careers_email'   => array( __( 'Careers / CV email', 'prime' ), 'email', '' ),
				'contact_phone'   => array( __( 'Phone', 'prime' ), 'text', '' ),
				'whatsapp'        => array( __( 'WhatsApp number (digits only, with country code, e.g. 918291222211)', 'prime' ), 'text', '' ),
				'whatsapp_text'   => array( __( 'WhatsApp pre-filled message', 'prime' ), 'text', '' ),
				'contact_address' => array( __( 'Office address', 'prime' ), 'textarea', '' ),
				'contact_hours'   => array( __( 'Office hours', 'prime' ), 'text', '' ),
				'header_cta_text' => array( __( 'Header button text', 'prime' ), 'text', '' ),
			),
		),
		'prime_stats'   => array(
			'title'  => __( 'Homepage stats', 'prime' ),
			'fields' => array(),
		),
		'prime_social'  => array(
			'title'  => __( 'Social links', 'prime' ),
			'fields' => array(
				'social_linkedin'  => array( 'LinkedIn URL', 'url', '' ),
				'social_instagram' => array( 'Instagram URL', 'url', '' ),
				'social_facebook'  => array( 'Facebook URL', 'url', '' ),
				'social_x'         => array( 'X / Twitter URL', 'url', '' ),
				'social_youtube'   => array( 'YouTube URL', 'url', '' ),
			),
		),
	);

	for ( $i = 1; $i <= 4; $i++ ) {
		/* translators: %d: stat number */
		$sections['prime_stats']['fields'][ "stat_{$i}_num" ]    = array( sprintf( __( 'Stat %d – number', 'prime' ), $i ), 'text', '' );
		$sections['prime_stats']['fields'][ "stat_{$i}_suffix" ] = array( sprintf( __( 'Stat %d – suffix', 'prime' ), $i ), 'text', '' );
		$sections['prime_stats']['fields'][ "stat_{$i}_label" ]  = array( sprintf( __( 'Stat %d – label', 'prime' ), $i ), 'text', '' );
	}

	$defaults = prime_opt_defaults();
	foreach ( $sections as $section_id => $section ) {
		$wp_customize->add_section(
			$section_id,
			array(
				'title' => $section['title'],
				'panel' => 'prime_panel',
			)
		);
		foreach ( $section['fields'] as $key => $field ) {
			list( $label, $type, $desc ) = $field;
			$sanitize = 'sanitize_text_field';
			if ( 'email' === $type ) {
				$sanitize = 'sanitize_email';
			} elseif ( 'url' === $type ) {
				$sanitize = 'esc_url_raw';
			} elseif ( 'textarea' === $type ) {
				$sanitize = 'sanitize_textarea_field';
			} elseif ( 'color' === $type ) {
				$sanitize = 'sanitize_hex_color';
			} elseif ( 'image' === $type ) {
				$sanitize = 'esc_url_raw';
			} elseif ( 'number' === $type ) {
				$sanitize = 'absint';
			}
			$wp_customize->add_setting(
				'prime_' . $key,
				array(
					'default'           => isset( $defaults[ $key ] ) ? $defaults[ $key ] : '',
					'sanitize_callback' => $sanitize,
				)
			);
			if ( 'color' === $type ) {
				$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'prime_' . $key, array( 'label' => $label, 'section' => $section_id ) ) );
				continue;
			}
			if ( 'image' === $type ) {
				$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'prime_' . $key, array( 'label' => $label, 'section' => $section_id ) ) );
				continue;
			}
			$wp_customize->add_control(
				'prime_' . $key,
				array(
					'label'       => $label,
					'description' => $desc,
					'section'     => $section_id,
					'type'        => $type,
				)
			);
		}
	}
}
add_action( 'customize_register', 'prime_customize_register' );

/**
 * Output the brand colour as CSS variables.
 */
function prime_brand_color_css() {
	$c = sanitize_hex_color( prime_opt( 'brand_color' ) );
	if ( $c && '#c9a227' !== strtolower( $c ) ) {
		wp_add_inline_style( 'prime-main', ':root{--gold:' . $c . ';--blue:' . $c . ';--gold-2:' . $c . ';}' );
	}
}
add_action( 'wp_enqueue_scripts', 'prime_brand_color_css', 20 );
