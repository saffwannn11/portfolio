<?php
/**
 * Template helpers.
 *
 * @package Prime
 */

defined( 'ABSPATH' ) || exit;

/**
 * Landing page definition by key.
 */
function prime_page( $key ) {
	$pages = prime_pages();
	return isset( $pages[ $key ] ) ? $pages[ $key ] : null;
}

/**
 * URL of a landing page. Uses the real page if it exists so URLs stay correct
 * even if the permalink structure changes.
 */
function prime_url( $key ) {
	if ( 'home' === $key ) {
		return home_url( '/' );
	}
	$page = prime_page( $key );
	if ( ! $page ) {
		return home_url( '/' );
	}
	$post = get_page_by_path( $page['path'] );
	if ( $post ) {
		return get_permalink( $post );
	}
	return home_url( '/' . $page['path'] . '/' );
}

/**
 * Landing page definition for the page currently being viewed, or null.
 */
function prime_current_page() {
	if ( ! is_page() ) {
		return null;
	}
	$uri = get_page_uri( get_queried_object_id() );
	foreach ( prime_pages() as $key => $page ) {
		if ( $page['path'] === $uri ) {
			$page['key'] = $key;
			return $page;
		}
	}
	return null;
}

/**
 * Pages of a given group (service, location …) in definition order.
 */
function prime_pages_in( $group ) {
	return array_filter(
		prime_pages(),
		function ( $p ) use ( $group ) {
			return isset( $p['group'] ) && $p['group'] === $group;
		}
	);
}

/**
 * Brand logo. Priority:
 * 1. Logo uploaded in Appearance → Customize → Site Identity.
 * 2. Logo image URL pasted in Customize → Prime Recruitments → Logo.
 * 3. Built-in Prime Recruitments logo (from the business card).
 * The link target is configurable too (defaults to the homepage).
 */
function prime_logo( $context = 'header' ) {
	$link   = prime_opt( 'logo_link' ) ? prime_opt( 'logo_link' ) : home_url( '/' );
	$height = max( 24, min( 120, (int) prime_opt( 'logo_height' ) ) );
	$name   = get_bloginfo( 'name' );

	// Optional light logo for the dark footer.
	if ( 'footer' === $context && prime_opt( 'logo_footer_url' ) ) {
		printf(
			'<a href="%1$s" class="brand brand--footer brand--custom brand--light" rel="home"><img class="brand__img" src="%2$s" alt="%3$s" style="max-height:%4$dpx" loading="lazy" decoding="async"></a>',
			esc_url( $link ),
			esc_url( prime_opt( 'logo_footer_url' ) ),
			esc_attr( $name ),
			(int) $height
		);
		return;
	}
	if ( has_custom_logo() ) {
		$id  = get_theme_mod( 'custom_logo' );
		$img = wp_get_attachment_image( $id, 'full', false, array( 'class' => 'brand__img', 'alt' => $name, 'style' => 'max-height:' . $height . 'px', 'loading' => 'eager', 'decoding' => 'async' ) );
		printf( '<a href="%1$s" class="brand brand--%2$s brand--custom" rel="home">%3$s</a>', esc_url( $link ), esc_attr( $context ), $img ); // phpcs:ignore
		return;
	}
	$url = prime_opt( 'logo_url' );
	if ( $url ) {
		printf(
			'<a href="%1$s" class="brand brand--%2$s brand--custom" rel="home"><img class="brand__img" src="%3$s" alt="%4$s" style="max-height:%5$dpx" loading="eager" decoding="async"></a>',
			esc_url( $link ),
			esc_attr( $context ),
			esc_url( $url ),
			esc_attr( $name ),
			(int) $height
		);
		return;
	}
	printf(
		'<a href="%1$s" class="brand brand--%2$s" rel="home" aria-label="%3$s"><img class="brand__mark" src="%4$s" width="48" height="44" alt="" loading="eager" decoding="async"><span class="brand__text"><strong>PRIME</strong><em>RECRUITMENTS</em></span></a>',
		esc_url( $link ),
		esc_attr( $context ),
		esc_attr( $name ),
		esc_url( prime_img( 'footer' === $context ? 'logo-mark-light.png' : 'logo-mark-dark.png' ) )
	);
}

/**
 * URL of a bundled theme image.
 */
function prime_img( $file ) {
	return PRIME_URI . '/assets/img/' . $file;
}

/**
 * Bundled photography (CC0, monochrome) used by banners and image sections.
 * Old banner style keys map onto photos so existing pages keep working.
 */
function prime_photos() {
	return array(
		'facade'   => 'photo-facade.webp',
		'tower'    => 'photo-tower.webp',
		'windows'  => 'photo-windows.webp',
		'geometry' => 'photo-geometry.webp',
		'atrium'   => 'photo-atrium.webp',
		'corridor' => 'photo-corridor.webp',
		'stairs'   => 'photo-stairs.webp',
	);
}

function prime_photo( $key ) {
	$legacy = array( 'mesh' => 'facade', 'grid' => 'windows', 'orbit' => 'tower', 'beam' => 'atrium', 'wave' => 'geometry' );
	$key    = isset( $legacy[ $key ] ) ? $legacy[ $key ] : $key;
	$all    = prime_photos();
	// Red duotone versions match the brand gradient.
	return prime_img( str_replace( 'photo-', 'duo-', isset( $all[ $key ] ) ? $all[ $key ] : $all['facade'] ) );
}

/**
 * Decorative liquid blobs (morph and drift in CSS).
 */
function prime_blobs( $count = 3 ) {
	$out = '<div class="liquid" aria-hidden="true">';
	for ( $i = 1; $i <= $count; $i++ ) {
		$out .= '<span class="blob blob--' . $i . '"></span>';
	}
	return $out . '</div>';
}

/**
 * Media setting with a bundled photo fallback.
 */
function prime_media_or_photo( $v, $photo ) {
	$url = prime_media_url( $v );
	return $url ? $url : prime_photo( $photo );
}

/**
 * Inline SVG icon set (stroke icons, inherit currentColor).
 */
function prime_icon_paths() {
	return array(
		'arrow'     => '<path d="M5 12h14M13 6l6 6-6 6"/>',
		'arrow-ur'  => '<path d="M7 17 17 7M8 7h9v9"/>',
		'check'     => '<path d="m5 12 5 5L20 7"/>',
		'plus'      => '<path d="M12 5v14M5 12h14"/>',
		'search'    => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
		'users'     => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0"/><path d="M16 4.5a3.5 3.5 0 0 1 0 7M18.5 20a6.5 6.5 0 0 0-3-5.5"/>',
		'crown'     => '<path d="m3 8 4.5 4L12 5l4.5 7L21 8l-2 11H5Z"/>',
		'code'      => '<path d="m8 8-5 4 5 4M16 8l5 4-5 4M14 4l-4 16"/>',
		'clock'     => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
		'layers'    => '<path d="m12 3 9 5-9 5-9-5Z"/><path d="m3 13 9 5 9-5"/>',
		'zap'       => '<path d="M13 2 4 14h7l-1 8 9-12h-7Z"/>',
		'heart'     => '<path d="M12 20s-7-4.4-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.6-7 10-7 10Z"/>',
		'globe'     => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.7 3.8 5.7 3.8 9S14.5 18.3 12 21c-2.5-2.7-3.8-5.7-3.8-9S9.5 5.7 12 3Z"/>',
		'briefcase' => '<rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M3 13h18"/>',
		'shield'    => '<path d="M12 3 4 6v6c0 5 3.5 8 8 9 4.5-1 8-4 8-9V6Z"/><path d="m9 12 2 2 4-4"/>',
		'chart'     => '<path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/>',
		'target'    => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1"/>',
		'pin'       => '<path d="M12 21s-7-6.1-7-11.5A7 7 0 0 1 19 9.5C19 14.9 12 21 12 21Z"/><circle cx="12" cy="9.5" r="2.5"/>',
		'mail'      => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
		'phone'     => '<path d="M5 3h4l2 5-2.5 1.5a11 11 0 0 0 6 6L16 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 5a2 2 0 0 1 2-2Z"/>',
		'spark'     => '<path d="M12 3v4M12 17v4M3 12h4M17 12h4M5.6 5.6l2.8 2.8M15.6 15.6l2.8 2.8M5.6 18.4l2.8-2.8M15.6 8.4l2.8-2.8"/>',
		'file'      => '<path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9Z"/><path d="M14 3v6h6M8 13h8M8 17h5"/>',
		'building'  => '<rect x="4" y="3" width="16" height="18" rx="1"/><path d="M9 7h1M14 7h1M9 11h1M14 11h1M9 15h1M14 15h1M10 21v-3h4v3"/>',
		'linkedin'  => '<rect x="3" y="3" width="18" height="18" rx="3"/><path d="M8 10v7M8 7v.01M12 17v-4a2 2 0 0 1 4 0v4M12 10v7"/>',
		'instagram' => '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><path d="M17.5 6.5v.01"/>',
		'x'         => '<path d="M4 4l16 16M20 4 4 20"/>',
		'facebook'  => '<path d="M14 8h3V4h-3a4 4 0 0 0-4 4v3H7v4h3v6h4v-6h3l1-4h-4V8Z"/>',
		'youtube'   => '<rect x="2.5" y="5" width="19" height="14" rx="4"/><path d="m10 9 5 3-5 3Z"/>',
		'whatsapp'  => '<path d="M4 20l1.3-3.9A8 8 0 1 1 8 18.7Z"/><path d="M9 9.5c0 3 2.5 5.5 5.5 5.5l1-1.5-2-1-1 1a4 4 0 0 1-2-2l1-1-1-2Z"/>',
		'gift'      => '<rect x="3" y="8" width="18" height="4" rx="1"/><path d="M12 8v13M5 12v9h14v-9M12 8S10.5 3.5 8 4.5 8.5 8 12 8ZM12 8s1.5-4.5 4-3.5S15.5 8 12 8Z"/>',
		'truck'     => '<path d="M3 6h11v10H3ZM14 9h4l3 3v4h-7"/><circle cx="7" cy="17.5" r="1.8"/><circle cx="17" cy="17.5" r="1.8"/>',
		'star'      => '<path d="m12 3 2.7 5.6 6.1.8-4.5 4.2 1.1 6.1L12 16.8l-5.4 2.9 1.1-6.1-4.5-4.2 6.1-.8Z"/>',
		'download'  => '<path d="M12 4v11M7 10l5 5 5-5M5 20h14"/>',
		'palette'   => '<path d="M12 3a9 9 0 1 0 0 18c1.2 0 1.6-1 1.2-1.9-.5-1 .2-2.1 1.3-2.1H17a4 4 0 0 0 4-4c0-5.5-4-10-9-10Z"/><circle cx="7.5" cy="11" r="1"/><circle cx="10" cy="7" r="1"/><circle cx="15" cy="7.5" r="1"/>',
		'calendar'  => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/>',
	);
}

/**
 * Icon names for Elementor select controls.
 */
function prime_icon_options() {
	$out = array();
	foreach ( array_keys( prime_icon_paths() ) as $k ) {
		$out[ $k ] = ucwords( str_replace( '-', ' ', $k ) );
	}
	return $out;
}

/**
 * Inline SVG icon (stroke icon, inherits currentColor).
 */
function prime_icon( $name, $size = 22 ) {
	$paths = prime_icon_paths();
	$path = isset( $paths[ $name ] ) ? $paths[ $name ] : $paths['spark'];
	return sprintf(
		'<svg class="icon icon-%1$s" width="%2$d" height="%2$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">%3$s</svg>',
		esc_attr( $name ),
		(int) $size,
		$path
	);
}

/**
 * Section eyebrow: a short gold rule followed by the label. The number
 * argument is kept for backwards compatibility and no longer printed.
 */
function prime_label( $num, $text, $extra_class = '' ) {
	if ( '' === trim( (string) $text ) ) {
		return;
	}
	printf(
		'<p class="eyebrow %2$s">%1$s</p>',
		esc_html( $text ),
		esc_attr( str_replace( 'label--light', 'eyebrow--light', $extra_class ) )
	);
}

/**
 * Prints a heading where text wrapped in *asterisks* is set in the serif accent.
 */
function prime_heading_html( $text ) {
	$safe = esc_html( $text );
	return preg_replace( '/\*(.+?)\*/', '<em class="accent">$1</em>', $safe );
}

/**
 * Social links from the Customizer.
 */
function prime_socials() {
	$out = '';
	foreach ( array( 'linkedin', 'instagram', 'facebook', 'x', 'youtube' ) as $net ) {
		$url = prime_opt( 'social_' . $net );
		if ( $url ) {
			$out .= sprintf( '<a href="%1$s" target="_blank" rel="noopener" aria-label="%2$s">%3$s</a>', esc_url( $url ), esc_attr( 'x' === $net ? 'X (Twitter)' : ( 'youtube' === $net ? 'YouTube' : ( 'linkedin' === $net ? 'LinkedIn' : ucfirst( $net ) ) ) ), prime_icon( $net, 18 ) );
		}
	}
	return $out;
}

/**
 * URL from an Elementor URL control value (array) or a plain string.
 */
function prime_href( $v ) {
	if ( is_array( $v ) ) {
		$v = isset( $v['url'] ) ? $v['url'] : '';
	}
	return (string) $v;
}

/**
 * Image URL from an Elementor media control value or a plain string.
 */
function prime_media_url( $v ) {
	if ( is_array( $v ) ) {
		if ( ! empty( $v['id'] ) ) {
			$img = wp_get_attachment_image_url( (int) $v['id'], 'large' );
			if ( $img ) {
				return $img;
			}
		}
		return isset( $v['url'] ) ? (string) $v['url'] : '';
	}
	return (string) $v;
}

/**
 * Link attributes (href + target/rel) from a URL control value.
 */
function prime_link_attrs( $v ) {
	$out = 'href="' . esc_url( prime_href( $v ) ) . '"';
	if ( is_array( $v ) && ! empty( $v['is_external'] ) ) {
		$out .= ' target="_blank" rel="noopener' . ( ! empty( $v['nofollow'] ) ? ' nofollow' : '' ) . '"';
	} elseif ( is_array( $v ) && ! empty( $v['nofollow'] ) ) {
		$out .= ' rel="nofollow"';
	}
	return $out;
}

/**
 * Non-empty trimmed lines from a textarea value.
 */
function prime_lines( $text ) {
	return array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) $text ) ), 'strlen' ) );
}

/**
 * Elementor-style URL value.
 */
function prime_link( $url ) {
	return array( 'url' => $url, 'is_external' => '', 'nofollow' => '' );
}

/**
 * WhatsApp chat URL with the pre-filled message.
 */
function prime_whatsapp_url( $text = '' ) {
	$num = preg_replace( '/\D/', '', (string) prime_opt( 'whatsapp' ) );
	if ( ! $num ) {
		return '';
	}
	$text = $text ? $text : prime_opt( 'whatsapp_text' );
	return 'https://wa.me/' . $num . ( $text ? '?text=' . rawurlencode( $text ) : '' );
}

/**
 * tel: URL for the main phone number.
 */
function prime_tel_url() {
	$tel = preg_replace( '/[^0-9+]/', '', (string) prime_opt( 'contact_phone' ) );
	return $tel ? 'tel:' . $tel : '';
}

/**
 * Logo mark used by the preloader: the uploaded/pasted logo if any,
 * otherwise the built-in light mark.
 */
function prime_mark_url() {
	if ( has_custom_logo() ) {
		$img = wp_get_attachment_image_url( get_theme_mod( 'custom_logo' ), 'medium' );
		if ( $img ) {
			return $img;
		}
	}
	return prime_opt( 'logo_url' ) ? prime_opt( 'logo_url' ) : prime_img( 'logo-mark-dark.png' );
}
