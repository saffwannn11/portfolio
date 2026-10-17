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
 * Logo, or a clearly marked placeholder slot until one is uploaded in
 * Appearance → Customize → Site Identity.
 */
function prime_logo( $context = 'header' ) {
	if ( has_custom_logo() ) {
		echo '<span class="brand-logo brand-logo--' . esc_attr( $context ) . '">';
		the_custom_logo();
		echo '</span>';
		return;
	}
	printf(
		'<a href="%1$s" class="brand-slot brand-slot--%2$s" aria-label="%3$s"><span class="brand-slot__mark" aria-hidden="true">P</span><span class="brand-slot__text">%4$s<em>Recruitments</em></span></a>',
		esc_url( home_url( '/' ) ),
		esc_attr( $context ),
		esc_attr( get_bloginfo( 'name' ) ),
		esc_html__( 'Prime', 'prime' )
	);
}

/**
 * Inline SVG icon set (stroke icons, inherit currentColor).
 */
function prime_icon( $name, $size = 22 ) {
	$paths = array(
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
	);
	$path = isset( $paths[ $name ] ) ? $paths[ $name ] : $paths['spark'];
	return sprintf(
		'<svg class="icon icon-%1$s" width="%2$d" height="%2$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">%3$s</svg>',
		esc_attr( $name ),
		(int) $size,
		$path
	);
}

/**
 * Section label in the "coded" mono style: <span>// 02</span> Label
 */
function prime_label( $num, $text, $extra_class = '' ) {
	printf(
		'<p class="label %3$s"><span class="label__num">// %1$s</span>%2$s</p>',
		esc_html( $num ),
		esc_html( $text ),
		esc_attr( $extra_class )
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
	foreach ( array( 'linkedin', 'instagram', 'x' ) as $net ) {
		$url = prime_opt( 'social_' . $net );
		if ( $url ) {
			$out .= sprintf( '<a href="%1$s" target="_blank" rel="noopener" aria-label="%2$s">%3$s</a>', esc_url( $url ), esc_attr( ucfirst( $net ) ), prime_icon( $net, 18 ) );
		}
	}
	return $out;
}
