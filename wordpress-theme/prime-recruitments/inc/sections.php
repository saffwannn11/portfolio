<?php
/**
 * Section renderers.
 *
 * Every designed section of the site is one function that takes a settings
 * array. The same functions power the Elementor widgets (inc/elementor.php)
 * and the fallback rendering used when Elementor is not active, so the site
 * looks identical either way.
 *
 * @package Prime
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render a section by key.
 */
function prime_render_section( $key, $s ) {
	$fn = 'prime_sec_' . str_replace( '-', '_', $key );
	if ( function_exists( $fn ) ) {
		$fn( is_array( $s ) ? $s : array() );
	}
}

/** Setting value helper. */
function prime_v( $s, $k, $d = '' ) {
	return isset( $s[ $k ] ) && '' !== $s[ $k ] && null !== $s[ $k ] ? $s[ $k ] : $d;
}

/** Heading with *accent* support, already escaped. */
function prime_h( $text ) {
	return wp_kses_post( prime_heading_html( $text ) );
}

/** Two-digit index. */
function prime_nn( $i ) {
	return str_pad( (string) $i, 2, '0', STR_PAD_LEFT );
}

/**
 * Link attributes plus popup attributes when the link points at #enquiry.
 */
function prime_cta_attrs( $link, $preset = '' ) {
	$out = prime_link_attrs( $link );
	if ( '#enquiry' === substr( prime_href( $link ), -8 ) ) {
		$out .= ' data-modal="enquiry"';
		if ( $preset ) {
			$out .= ' data-preset="' . esc_attr( $preset ) . '"';
		}
	}
	return $out;
}

/** Section heading block: eyebrow, H2 and optional intro. */
function prime_shead( $s, $args = array() ) {
	$args = wp_parse_args( $args, array( 'light' => false, 'class' => '', 'intro' => 'intro', 'link' => true ) );
	$intro = $args['intro'] ? prime_v( $s, $args['intro'] ) : '';
	$link  = $args['link'] && prime_v( $s, 'link_text' );
	?>
	<div class="shead <?php echo esc_attr( $args['class'] ); ?> <?php echo ( $intro || $link ) ? 'shead--split' : ''; ?>">
		<div class="shead__main">
			<?php prime_label( '', prime_v( $s, 'label' ), $args['light'] ? 'eyebrow--light' : '' ); ?>
			<h2 class="h2" data-split><?php echo prime_h( prime_v( $s, 'title' ) ); // phpcs:ignore ?></h2>
		</div>
		<?php if ( $intro || $link ) : ?>
			<div class="shead__side" data-anim="fade-up">
				<?php if ( $intro ) : ?><p class="shead__intro"><?php echo esc_html( $intro ); ?></p><?php endif; ?>
				<?php if ( $link ) : ?><a class="link-arrow <?php echo $args['light'] ? 'link-arrow--light' : ''; ?>" <?php echo prime_link_attrs( prime_v( $s, 'link' ) ); // phpcs:ignore ?>><?php echo esc_html( $s['link_text'] ); ?> <?php echo prime_icon( 'arrow', 16 ); // phpcs:ignore ?></a><?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
	<?php
}

/* ==========================================================================
   HOMEPAGE SECTIONS
   ========================================================================== */

function prime_sec_hero( $s ) {
	$words = array_values( array_filter( array_map( 'trim', explode( ',', prime_v( $s, 'rotator', '' ) ) ) ) );
	$img   = prime_media_or_photo( prime_v( $s, 'image' ), 'windows' );
	$stats = (array) prime_v( $s, 'stats', array() );
	?>
	<section class="hero prime-sec" data-hero>
		<div class="container">
			<?php if ( prime_v( $s, 'eyebrow' ) ) : ?><p class="eyebrow" data-anim="fade-up"><?php echo esc_html( $s['eyebrow'] ); ?></p><?php endif; ?>
			<h1 class="hero__title">
				<?php foreach ( array( 'title_1', 'title_2', 'title_3' ) as $k ) : if ( ! prime_v( $s, $k ) ) { continue; } ?>
					<span class="hero__line" data-split><?php echo prime_h( $s[ $k ] ); // phpcs:ignore ?><?php if ( 'title_3' === $k && $words ) : ?> <span class="rotator" data-rotator="<?php echo esc_attr( implode( ',', $words ) ); ?>"><em class="accent"><?php echo esc_html( $words[0] ); ?></em></span><?php endif; ?></span>
				<?php endforeach; ?>
			</h1>
			<div class="hero__row">
				<?php if ( prime_v( $s, 'lead' ) ) : ?><p class="hero__lead" data-anim="fade-up"><?php echo esc_html( $s['lead'] ); ?></p><?php endif; ?>
				<div class="hero__actions" data-anim="fade-up">
					<?php if ( prime_v( $s, 'btn1_text' ) ) : ?><a class="btn btn--primary btn--lg" <?php echo prime_cta_attrs( prime_v( $s, 'btn1_link' ), 'employer' ); // phpcs:ignore ?>><span><?php echo esc_html( $s['btn1_text'] ); ?></span><?php echo prime_icon( 'arrow', 18 ); // phpcs:ignore ?></a><?php endif; ?>
					<?php if ( prime_v( $s, 'btn2_text' ) ) : ?><a class="btn btn--outline btn--lg" <?php echo prime_cta_attrs( prime_v( $s, 'btn2_link' ), 'candidate' ); // phpcs:ignore ?>><span><?php echo esc_html( $s['btn2_text'] ); ?></span></a><?php endif; ?>
				</div>
			</div>
			<div class="hero__img" data-parallax>
				<img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( prime_v( $s, 'image_alt', 'Prime Recruitments' ) ); ?>" width="1500" height="703" fetchpriority="high" decoding="async">
				<?php if ( prime_v( $s, 'tag' ) ) : ?><span class="hero__tag"><?php echo esc_html( $s['tag'] ); ?></span><?php endif; ?>
			</div>
			<?php if ( $stats ) : ?>
				<div class="hero__nums">
					<?php foreach ( array_slice( $stats, 0, 4 ) as $st ) : ?>
						<div class="hero__num">
							<p class="hero__num-val"><span data-count="<?php echo esc_attr( prime_v( $st, 'number' ) ); ?>"><?php echo esc_html( prime_v( $st, 'number' ) ); ?></span><em><?php echo esc_html( prime_v( $st, 'suffix' ) ); ?></em></p>
							<p class="hero__num-label"><?php echo esc_html( prime_v( $st, 'label' ) ); ?></p>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</section>
	<?php
}

function prime_sec_stats( $s ) {
	$dark = 'dark' === prime_v( $s, 'style' );
	?>
	<section class="stats prime-sec <?php echo $dark ? 'stats--dark' : ''; ?>">
		<div class="container">
			<?php if ( prime_v( $s, 'title' ) ) : ?><p class="stats__title" data-anim="fade-up"><?php echo esc_html( $s['title'] ); ?></p><?php endif; ?>
			<div class="stats__grid" data-anim="stagger">
				<?php foreach ( (array) prime_v( $s, 'items', array() ) as $it ) : ?>
					<div class="stat">
						<p class="stat__num"><span data-count="<?php echo esc_attr( prime_v( $it, 'number' ) ); ?>"><?php echo esc_html( prime_v( $it, 'number' ) ); ?></span><em><?php echo esc_html( prime_v( $it, 'suffix' ) ); ?></em></p>
						<p class="stat__label"><?php echo esc_html( prime_v( $it, 'label' ) ); ?></p>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php
}

function prime_sec_about_split( $s ) {
	$img    = prime_media_or_photo( prime_v( $s, 'image' ), 'stairs' );
	$paras  = prime_lines( prime_v( $s, 'text' ) );
	$points = prime_lines( prime_v( $s, 'points' ) );
	$alt    = wp_strip_all_tags( str_replace( '*', '', prime_v( $s, 'title' ) ) );
	?>
	<section class="section split prime-sec <?php echo 'yes' === prime_v( $s, 'reverse' ) ? 'split--reverse' : ''; ?> <?php echo 'sand' === prime_v( $s, 'background' ) ? 'is-sand' : ( 'dark' === prime_v( $s, 'background' ) ? 'is-dark' : '' ); ?>" id="<?php echo esc_attr( prime_v( $s, 'anchor', 'about' ) ); ?>">
		<div class="container split__layout">
			<div class="split__media" data-anim="reveal">
				<div class="split__img" data-parallax><img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $alt ); ?>" loading="lazy" decoding="async"></div>
				<?php if ( prime_v( $s, 'badge_num' ) ) : ?>
					<div class="split__badge"><strong><?php echo esc_html( $s['badge_num'] ); ?></strong><span><?php echo esc_html( prime_v( $s, 'badge_text' ) ); ?></span></div>
				<?php endif; ?>
			</div>
			<div class="split__copy">
				<?php prime_label( '', prime_v( $s, 'label' ) ); ?>
				<h2 class="h2" data-split><?php echo prime_h( prime_v( $s, 'title' ) ); // phpcs:ignore ?></h2>
				<?php if ( $paras ) : ?><div class="split__text" data-anim="fade-up"><?php foreach ( $paras as $p ) : ?><p><?php echo esc_html( $p ); ?></p><?php endforeach; ?></div><?php endif; ?>
				<?php if ( $points ) : ?>
					<ul class="checks" data-anim="stagger"><?php foreach ( $points as $pt ) : ?><li><?php echo prime_icon( 'check', 16 ); // phpcs:ignore ?><span><?php echo esc_html( $pt ); ?></span></li><?php endforeach; ?></ul>
				<?php endif; ?>
				<div class="split__foot" data-anim="fade-up">
					<?php if ( prime_v( $s, 'sig_title' ) ) : ?>
						<div class="signature">
							<img src="<?php echo esc_url( prime_img( 'logo-mark-dark.png' ) ); ?>" width="44" height="40" alt="" loading="lazy" decoding="async">
							<span><strong><?php echo esc_html( $s['sig_title'] ); ?></strong><small><?php echo esc_html( prime_v( $s, 'sig_text' ) ); ?></small></span>
						</div>
					<?php endif; ?>
					<?php if ( prime_v( $s, 'link_text' ) ) : ?><a class="btn btn--dark" <?php echo prime_cta_attrs( prime_v( $s, 'link' ) ); // phpcs:ignore ?>><span><?php echo esc_html( $s['link_text'] ); ?></span><?php echo prime_icon( 'arrow', 18 ); // phpcs:ignore ?></a><?php endif; ?>
				</div>
			</div>
		</div>
	</section>
	<?php
}

function prime_sec_marquee( $s ) {
	$rows = array( prime_lines( prime_v( $s, 'row_1' ) ), prime_lines( prime_v( $s, 'row_2' ) ) );
	?>
	<section class="ticker prime-sec" aria-label="<?php esc_attr_e( 'Highlights', 'prime' ); ?>">
		<?php foreach ( $rows as $r => $items ) : if ( ! $items ) { continue; } ?>
			<div class="ticker__row" data-marquee="<?php echo 1 === $r ? '1' : '-1'; ?>">
				<div class="ticker__track">
					<?php for ( $k = 0; $k < 2; $k++ ) : foreach ( $items as $t ) : ?>
						<span><?php echo esc_html( $t ); ?></span><i aria-hidden="true"></i>
					<?php endforeach; endfor; ?>
				</div>
			</div>
		<?php endforeach; ?>
	</section>
	<?php
}

function prime_sec_statement( $s ) {
	?>
	<section class="section statement prime-sec">
		<div class="container statement__inner">
			<?php prime_label( '', prime_v( $s, 'label' ) ); ?>
			<p class="statement__text" data-scrub-text><?php echo prime_h( prime_v( $s, 'text' ) ); // phpcs:ignore ?></p>
			<div class="statement__foot">
				<?php if ( prime_v( $s, 'sig_title' ) ) : ?>
					<div class="signature" data-anim="fade-up">
						<img src="<?php echo esc_url( prime_img( 'logo-mark-dark.png' ) ); ?>" width="44" height="40" alt="" loading="lazy" decoding="async">
						<span><strong><?php echo esc_html( $s['sig_title'] ); ?></strong><small><?php echo esc_html( prime_v( $s, 'sig_text' ) ); ?></small></span>
					</div>
				<?php endif; ?>
				<?php if ( prime_v( $s, 'link_text' ) ) : ?><a class="link-arrow" <?php echo prime_link_attrs( prime_v( $s, 'link' ) ); // phpcs:ignore ?> data-anim="fade-up"><?php echo esc_html( $s['link_text'] ); ?> <?php echo prime_icon( 'arrow', 16 ); // phpcs:ignore ?></a><?php endif; ?>
			</div>
		</div>
	</section>
	<?php
}

/**
 * Services grid (widget key kept as services_slider so existing pages keep working).
 */
function prime_sec_services_slider( $s ) {
	$cards = (array) prime_v( $s, 'cards', array() );
	?>
	<section class="section services prime-sec" id="<?php echo esc_attr( prime_v( $s, 'anchor', 'services' ) ); ?>">
		<div class="container">
			<?php prime_shead( $s ); ?>
			<div class="svc-rows" data-rows>
				<?php foreach ( $cards as $n => $c ) : ?>
					<a class="svc-row" <?php echo prime_link_attrs( prime_v( $c, 'link' ) ); // phpcs:ignore ?>>
						<span class="svc-row__num"><?php echo esc_html( prime_nn( $n + 1 ) ); ?></span>
						<h3 class="svc-row__title"><?php echo esc_html( prime_v( $c, 'title' ) ); ?></h3>
						<p class="svc-row__text"><?php echo esc_html( prime_v( $c, 'text' ) ); ?></p>
						<span class="svc-row__arrow"><?php echo prime_icon( 'arrow', 20 ); // phpcs:ignore ?></span>
					</a>
				<?php endforeach; ?>
			</div>
			<?php if ( prime_v( $s, 'cta_title' ) ) : ?>
				<div class="svc-rows__foot" data-anim="fade-up">
					<p><strong><?php echo esc_html( $s['cta_title'] ); ?></strong> <?php echo esc_html( prime_v( $s, 'cta_text' ) ); ?></p>
					<a class="btn btn--primary" <?php echo prime_cta_attrs( prime_v( $s, 'cta_link' ) ); // phpcs:ignore ?>><span><?php echo esc_html( prime_v( $s, 'cta_link_text', __( 'Compare services', 'prime' ) ) ); ?></span><?php echo prime_icon( 'arrow', 16 ); // phpcs:ignore ?></a>
				</div>
			<?php endif; ?>
		</div>
	</section>
	<?php
}

function prime_sec_process( $s ) {
	?>
	<section class="section process prime-sec" data-process>
		<div class="container process__layout">
			<div class="process__head">
				<?php prime_label( '', prime_v( $s, 'label' ) ); ?>
				<h2 class="h2" data-split><?php echo prime_h( prime_v( $s, 'title' ) ); // phpcs:ignore ?></h2>
				<?php if ( prime_v( $s, 'text' ) ) : ?><p class="muted" data-anim="fade-up"><?php echo esc_html( $s['text'] ); ?></p><?php endif; ?>
				<?php if ( prime_v( $s, 'btn_text' ) ) : ?><a class="btn btn--dark" <?php echo prime_cta_attrs( prime_v( $s, 'btn_link' ) ); // phpcs:ignore ?> data-anim="fade-up"><span><?php echo esc_html( $s['btn_text'] ); ?></span><?php echo prime_icon( 'arrow', 18 ); // phpcs:ignore ?></a><?php endif; ?>
			</div>
			<ol class="process__steps">
				<span class="process__line" aria-hidden="true"><i data-process-line></i></span>
				<?php foreach ( (array) prime_v( $s, 'steps', array() ) as $i => $st ) : ?>
					<li class="process__step" data-step>
						<span class="process__num"><?php echo esc_html( prime_nn( $i + 1 ) ); ?></span>
						<div><h3><?php echo esc_html( prime_v( $st, 'title' ) ); ?></h3><p><?php echo esc_html( prime_v( $st, 'text' ) ); ?></p></div>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</section>
	<?php
}

function prime_sec_industries_reel( $s ) {
	?>
	<section class="section industries-reel prime-sec is-dark" data-reveal-list>
		<?php echo prime_blobs( 2 ); // phpcs:ignore ?>
		<div class="container">
			<?php prime_shead( $s, array( 'light' => true ) ); ?>
			<ul class="reel">
				<?php foreach ( (array) prime_v( $s, 'rows', array() ) as $i => $row ) : ?>
					<li>
						<a class="reel__row" <?php echo prime_link_attrs( prime_v( $row, 'link' ) ); // phpcs:ignore ?> data-img="<?php echo esc_url( prime_media_url( prime_v( $row, 'image' ) ) ); ?>">
							<span class="reel__num"><?php echo esc_html( prime_nn( $i + 1 ) ); ?></span>
							<span class="reel__title"><?php echo esc_html( prime_v( $row, 'title' ) ); ?></span>
							<span class="reel__arrow"><?php echo prime_icon( 'arrow-ur', 24 ); // phpcs:ignore ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<div class="reel__preview" aria-hidden="true"><img alt="" src="data:image/gif;base64,R0lGODlhAQABAAAAACw="></div>
	</section>
	<?php
}

function prime_sec_why_bento( $s ) {
	$funnel = prime_lines( prime_v( $s, 'funnel' ) );
	$styles = array( 'light' => '', 'dark' => 'bento__card--dark', 'blue' => 'bento__card--gold' );
	?>
	<section class="section why prime-sec is-sand">
		<div class="container">
			<?php prime_shead( $s ); ?>
			<div class="bento" data-anim="stagger">
				<article class="bento__card bento__card--xl">
					<?php prime_label( '', prime_v( $s, 'xl_label' ) ); ?>
					<h3><?php echo esc_html( prime_v( $s, 'xl_title' ) ); ?></h3>
					<div class="funnel" aria-hidden="true">
						<?php
						$n = count( $funnel );
						foreach ( $funnel as $i => $f ) :
							$w = $n > 1 ? 100 - ( 60 * $i / ( $n - 1 ) ) : 100;
							?>
							<div class="funnel__row <?php echo $i === $n - 1 ? 'funnel__row--hl' : ''; ?>" style="--w:<?php echo esc_attr( round( $w ) ); ?>%"><span><?php echo esc_html( $f ); ?></span></div>
						<?php endforeach; ?>
					</div>
				</article>
				<?php foreach ( array_slice( (array) prime_v( $s, 'cards', array() ), 0, 4 ) as $c ) : $st = prime_v( $c, 'style', 'light' ); ?>
					<article class="bento__card <?php echo esc_attr( isset( $styles[ $st ] ) ? $styles[ $st ] : '' ); ?>">
						<span class="bento__icon"><?php echo prime_icon( prime_v( $c, 'icon', 'spark' ), 24 ); // phpcs:ignore ?></span>
						<h3><?php echo esc_html( prime_v( $c, 'title' ) ); ?></h3>
						<p><?php echo esc_html( prime_v( $c, 'text' ) ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php
}

function prime_sec_audience_split( $s ) {
	$panels = array_slice( (array) prime_v( $s, 'panels', array() ), 0, 2 );
	if ( ! $panels ) {
		return;
	}
	$photos = array( 'tower', 'corridor' );
	?>
	<section class="audience prime-sec" aria-label="<?php echo esc_attr( prime_v( $s, 'label', __( 'How we help', 'prime' ) ) ); ?>">
		<div class="container audience__grid">
			<?php foreach ( $panels as $i => $p ) : $pts = prime_lines( prime_v( $p, 'points' ) ); ?>
				<article class="audience__panel">
					<img class="audience__img" src="<?php echo esc_url( prime_media_or_photo( prime_v( $p, 'image' ), $photos[ $i % 2 ] ) ); ?>" alt="" loading="lazy" decoding="async">
					<div class="audience__body" data-anim="fade-up">
						<?php prime_label( '', prime_v( $p, 'label' ), 'eyebrow--light' ); ?>
						<h2 class="audience__title"><?php echo prime_h( prime_v( $p, 'title' ) ); // phpcs:ignore ?></h2>
						<p><?php echo esc_html( prime_v( $p, 'text' ) ); ?></p>
						<?php if ( $pts ) : ?><ul class="checks checks--light"><?php foreach ( $pts as $pt ) : ?><li><?php echo prime_icon( 'check', 16 ); // phpcs:ignore ?><span><?php echo esc_html( $pt ); ?></span></li><?php endforeach; ?></ul><?php endif; ?>
						<?php if ( prime_v( $p, 'btn_text' ) ) : ?><a class="btn btn--light" <?php echo prime_cta_attrs( prime_v( $p, 'btn_link' ), prime_v( $p, 'preset' ) ); // phpcs:ignore ?>><span><?php echo esc_html( $p['btn_text'] ); ?></span><?php echo prime_icon( 'arrow', 18 ); // phpcs:ignore ?></a><?php endif; ?>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</section>
	<?php
}

function prime_sec_testimonials( $s ) {
	$items = (array) prime_v( $s, 'items', array() );
	?>
	<section class="section testimonials prime-sec is-sand" data-slider>
		<div class="container testimonials__inner">
			<div class="testimonials__head">
				<?php prime_label( '', prime_v( $s, 'label' ) ); ?>
				<?php if ( prime_v( $s, 'title' ) ) : ?><h2 class="h2" data-split><?php echo prime_h( $s['title'] ); // phpcs:ignore ?></h2><?php endif; ?>
			</div>
			<div class="testimonials__stage">
				<span class="testimonials__mark" aria-hidden="true">&ldquo;</span>
				<?php foreach ( $items as $i => $t ) : ?>
					<figure class="quote <?php echo 0 === $i ? 'is-active' : ''; ?>" data-slide>
						<blockquote><p data-scrub-text><?php echo prime_h( prime_v( $t, 'quote' ) ); // phpcs:ignore ?></p></blockquote>
						<figcaption><span class="quote__av"><?php echo esc_html( mb_substr( prime_v( $t, 'name', 'P' ), 0, 1 ) ); ?></span><span><strong><?php echo esc_html( prime_v( $t, 'name' ) ); ?></strong><small><?php echo esc_html( prime_v( $t, 'role' ) ); ?></small></span></figcaption>
					</figure>
				<?php endforeach; ?>
			</div>
			<?php if ( count( $items ) > 1 ) : ?>
				<div class="testimonials__nav">
					<button type="button" class="slider-nav__btn" data-prev aria-label="<?php esc_attr_e( 'Previous', 'prime' ); ?>"><?php echo prime_icon( 'arrow', 18 ); // phpcs:ignore ?></button>
					<div class="slider-progress">
						<?php foreach ( $items as $i => $t ) : ?>
							<button type="button" data-goto="<?php echo esc_attr( $i ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %d: slide */ __( 'Show testimonial %d', 'prime' ), $i + 1 ) ); ?>"><i></i></button>
						<?php endforeach; ?>
					</div>
					<button type="button" class="slider-nav__btn" data-next aria-label="<?php esc_attr_e( 'Next', 'prime' ); ?>"><?php echo prime_icon( 'arrow', 18 ); // phpcs:ignore ?></button>
				</div>
			<?php endif; ?>
		</div>
	</section>
	<?php
}

function prime_sec_geo( $s ) {
	$cities = (array) prime_v( $s, 'cities', array() );
	$pos    = function ( $c ) {
		// Approximate lon/lat → % inside the India frame (lon 67–93, lat 6–35).
		return array( ( (float) prime_v( $c, 'lon', 77 ) - 67 ) / 26 * 100, ( 35 - (float) prime_v( $c, 'lat', 20 ) ) / 29 * 100 );
	};
	$hub = null;
	foreach ( $cities as $c ) {
		if ( 'yes' === prime_v( $c, 'hub' ) ) {
			$hub = $c;
			break;
		}
	}
	?>
	<section class="section geo prime-sec" id="<?php echo esc_attr( prime_v( $s, 'anchor', 'locations' ) ); ?>">
		<div class="container geo__layout">
			<div class="geo__copy">
				<?php prime_label( '', prime_v( $s, 'label' ) ); ?>
				<h2 class="h2" data-split><?php echo prime_h( prime_v( $s, 'title' ) ); // phpcs:ignore ?></h2>
				<p class="muted" data-anim="fade-up"><?php echo esc_html( prime_v( $s, 'text' ) ); ?></p>
				<div class="geo__cards" data-anim="stagger">
					<?php foreach ( (array) prime_v( $s, 'cards', array() ) as $c ) : ?>
						<a class="geo-card" <?php echo prime_link_attrs( prime_v( $c, 'link' ) ); // phpcs:ignore ?>>
							<span class="geo-card__pin"><?php echo prime_icon( prime_v( $c, 'icon', 'pin' ), 18 ); // phpcs:ignore ?></span>
							<span><strong><?php echo esc_html( prime_v( $c, 'title' ) ); ?></strong><small><?php echo esc_html( prime_v( $c, 'subtitle' ) ); ?></small></span>
							<?php echo prime_icon( 'arrow', 18 ); // phpcs:ignore ?>
						</a>
					<?php endforeach; ?>
				</div>
			</div>
			<div class="geo__map" data-anim="rise" aria-hidden="true">
				<?php if ( $hub ) : list( $hx, $hy ) = $pos( $hub ); ?>
					<svg class="geo__lines" viewBox="0 0 100 100" preserveAspectRatio="none">
						<?php
						foreach ( $cities as $c ) {
							if ( $c === $hub ) {
								continue;
							}
							list( $x, $y ) = $pos( $c );
							printf( '<path d="M%1$.2f %2$.2f Q %5$.2f %6$.2f %3$.2f %4$.2f" />', $hx, $hy, $x, $y, ( $hx + $x ) / 2 + 6, ( $hy + $y ) / 2 - 6 );
						}
						?>
					</svg>
				<?php endif; ?>
				<?php foreach ( $cities as $i => $c ) : list( $x, $y ) = $pos( $c ); ?>
					<span class="city <?php echo 'yes' === prime_v( $c, 'hub' ) ? 'city--hub' : ''; ?> <?php echo 'yes' === prime_v( $c, 'left' ) ? 'city--left' : ''; ?>" style="left:<?php echo esc_attr( round( $x, 2 ) ); ?>%;top:<?php echo esc_attr( round( $y, 2 ) ); ?>%"><i></i><b><?php echo esc_html( prime_v( $c, 'name' ) ); ?></b></span>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php
}

function prime_sec_jobs( $s ) {
	$jobs = (array) prime_v( $s, 'jobs', array() );
	$cats = array();
	foreach ( $jobs as $j ) {
		$c = prime_v( $j, 'category' );
		if ( $c && ! in_array( $c, $cats, true ) ) {
			$cats[] = $c;
		}
	}
	$bg = 'paper' === prime_v( $s, 'background' ) ? 'is-sand' : '';
	?>
	<section class="section jobs-section prime-sec <?php echo esc_attr( $bg ); ?>" id="<?php echo esc_attr( prime_v( $s, 'anchor', 'jobs' ) ); ?>">
		<div class="container">
			<?php if ( prime_v( $s, 'title' ) ) : ?>
				<?php prime_shead( $s, array( 'intro' => '' ) ); ?>
			<?php endif; ?>
			<div class="jobs" data-jobs>
				<?php if ( count( $cats ) > 1 ) : ?>
					<div class="filter-tabs" role="group" aria-label="<?php esc_attr_e( 'Filter roles', 'prime' ); ?>">
						<button type="button" class="is-active" data-filter="all" aria-pressed="true"><?php echo esc_html( prime_v( $s, 'all_label', __( 'All roles', 'prime' ) ) ); ?></button>
						<?php foreach ( $cats as $c ) : ?><button type="button" data-filter="<?php echo esc_attr( sanitize_title( $c ) ); ?>" aria-pressed="false"><?php echo esc_html( $c ); ?></button><?php endforeach; ?>
					</div>
				<?php endif; ?>
				<div class="jobs__grid">
					<?php foreach ( $jobs as $i => $j ) : ?>
						<article class="job-card" data-cat="<?php echo esc_attr( sanitize_title( prime_v( $j, 'category' ) ) ); ?>">
							<div class="job-card__top"><span class="tag"><?php echo esc_html( prime_v( $j, 'category' ) ); ?></span><span class="job-card__id"><?php echo esc_html( prime_v( $j, 'ref', 'PR-' . ( 101 + $i ) ) ); ?></span></div>
							<h3><?php echo esc_html( prime_v( $j, 'title' ) ); ?></h3>
							<p><?php echo esc_html( prime_v( $j, 'summary' ) ); ?></p>
							<ul class="job-card__meta">
								<li><?php echo prime_icon( 'pin', 15 ); // phpcs:ignore ?><?php echo esc_html( prime_v( $j, 'location' ) ); ?></li>
								<li><?php echo prime_icon( 'clock', 15 ); // phpcs:ignore ?><?php echo esc_html( prime_v( $j, 'type' ) ); ?></li>
								<li><?php echo prime_icon( 'briefcase', 15 ); // phpcs:ignore ?><?php echo esc_html( prime_v( $j, 'experience' ) ); ?></li>
							</ul>
							<a class="job-card__apply" <?php echo prime_cta_attrs( prime_v( $j, 'link', prime_link( prime_url( 'job-seekers' ) . '#enquiry' ) ), 'candidate' ); // phpcs:ignore ?> aria-label="<?php echo esc_attr( sprintf( /* translators: %s: job */ __( 'Apply for %s', 'prime' ), prime_v( $j, 'title' ) ) ); ?>"><?php esc_html_e( 'Apply now', 'prime' ); ?> <?php echo prime_icon( 'arrow', 16 ); // phpcs:ignore ?></a>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</section>
	<?php
}

function prime_sec_faq( $s ) {
	$items = array_values(
		array_filter(
			(array) prime_v( $s, 'items', array() ),
			function ( $f ) {
				return '' !== prime_v( $f, 'question' );
			}
		)
	);
	if ( ! $items ) {
		return;
	}
	?>
	<section class="section faq-section prime-sec" id="faq">
		<div class="container faq-layout">
			<div class="faq-layout__head">
				<?php prime_label( '', prime_v( $s, 'label', __( 'Questions, answered', 'prime' ) ) ); ?>
				<h2 class="h2" data-split><?php echo prime_h( prime_v( $s, 'title', 'Frequently asked *questions*' ) ); // phpcs:ignore ?></h2>
				<?php if ( prime_v( $s, 'intro' ) ) : ?><p class="muted" data-anim="fade-up"><?php echo esc_html( $s['intro'] ); ?></p><?php endif; ?>
				<div class="faq-help" data-anim="fade-up">
					<p><strong><?php esc_html_e( 'Still have a question?', 'prime' ); ?></strong> <?php esc_html_e( 'Speak to our team directly.', 'prime' ); ?></p>
					<div class="faq-help__links">
						<?php if ( prime_tel_url() ) : ?><a href="<?php echo esc_attr( prime_tel_url() ); ?>"><?php echo prime_icon( 'phone', 16 ); // phpcs:ignore ?><?php echo esc_html( prime_opt( 'contact_phone' ) ); ?></a><?php endif; ?>
						<?php if ( prime_v( $s, 'link_text' ) ) : ?><a <?php echo prime_link_attrs( prime_v( $s, 'link' ) ); // phpcs:ignore ?>><?php echo prime_icon( 'mail', 16 ); // phpcs:ignore ?><?php echo esc_html( $s['link_text'] ); ?></a><?php endif; ?>
					</div>
				</div>
			</div>
			<div class="faq-list" data-anim="stagger">
				<?php foreach ( $items as $i => $f ) : ?>
					<details class="faq-item" <?php echo 0 === $i ? 'open' : ''; ?>>
						<summary><h3><?php echo esc_html( $f['question'] ); ?></h3><span class="faq-item__icon" aria-hidden="true"></span></summary>
						<div class="faq-item__body"><p><?php echo esc_html( prime_v( $f, 'answer' ) ); ?></p></div>
					</details>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
		// FAQPage schema generated from exactly what is on the page (AEO).
		$schema = array(
			'@context'   => 'https://schema.org',
			'@type'      => 'FAQPage',
			'mainEntity' => array_map(
				function ( $f ) {
					return array(
						'@type'          => 'Question',
						'name'           => wp_strip_all_tags( $f['question'] ),
						'acceptedAnswer' => array( '@type' => 'Answer', 'text' => wp_strip_all_tags( prime_v( $f, 'answer' ) ) ),
					);
				},
				$items
			),
		);
		echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>';
		?>
	</section>
	<?php
}

function prime_sec_cta_form( $s ) {
	$points = prime_lines( prime_v( $s, 'points' ) );
	?>
	<section class="cta-form prime-sec is-coral" id="enquiry">
		<?php echo prime_blobs( 3 ); // phpcs:ignore ?>
		<div class="container cta-form__layout">
			<div class="cta-form__copy">
				<?php prime_label( '', prime_v( $s, 'label', __( 'Start a conversation', 'prime' ) ) ); ?>
				<h2 class="h2" data-split><?php echo prime_h( prime_v( $s, 'title', 'Tell us who you need. *We’ll take it from there.*' ) ); // phpcs:ignore ?></h2>
				<?php if ( prime_v( $s, 'lead' ) ) : ?><p class="lead" data-anim="fade-up"><?php echo esc_html( $s['lead'] ); ?></p><?php endif; ?>
				<?php if ( $points ) : ?>
					<ul class="checks" data-anim="stagger">
						<?php foreach ( $points as $pt ) : ?><li><?php echo prime_icon( 'check', 16 ); // phpcs:ignore ?><span><?php echo esc_html( $pt ); ?></span></li><?php endforeach; ?>
					</ul>
				<?php endif; ?>
				<?php if ( 'yes' === prime_v( $s, 'show_contacts', 'yes' ) ) : ?>
					<div class="cta-form__contact" data-anim="fade-up">
						<a href="mailto:<?php echo esc_attr( prime_opt( 'contact_email' ) ); ?>"><?php echo prime_icon( 'mail', 18 ); // phpcs:ignore ?><span><small><?php esc_html_e( 'Email', 'prime' ); ?></small><?php echo esc_html( prime_opt( 'contact_email' ) ); ?></span></a>
						<?php if ( prime_tel_url() ) : ?><a href="<?php echo esc_attr( prime_tel_url() ); ?>"><?php echo prime_icon( 'phone', 18 ); // phpcs:ignore ?><span><small><?php esc_html_e( 'Call', 'prime' ); ?></small><?php echo esc_html( prime_opt( 'contact_phone' ) ); ?></span></a><?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
			<div class="cta-form__card" data-anim="rise">
				<p class="cta-form__card-title"><?php esc_html_e( 'Send an enquiry', 'prime' ); ?></p>
				<?php prime_form( prime_v( $s, 'preset', 'employer' ), prime_v( $s, 'form_shortcode' ), 'light' ); ?>
			</div>
		</div>
	</section>
	<?php
}

/* ==========================================================================
   INNER PAGE SECTIONS
   ========================================================================== */

function prime_sec_banner( $s ) {
	$variant = prime_v( $s, 'variant', 'facade' );
	$img     = prime_media_or_photo( prime_v( $s, 'image' ), $variant );
	$post_id = get_the_ID();
	$crumbs  = array();
	if ( $post_id ) {
		foreach ( array_reverse( get_post_ancestors( $post_id ) ) as $anc ) {
			$crumbs[] = array( get_the_title( $anc ), get_permalink( $anc ) );
		}
	}
	$current = prime_v( $s, 'crumb', $post_id ? get_the_title( $post_id ) : '' );
	$chips   = prime_lines( prime_v( $s, 'chips' ) );
	$answer  = 'yes' === prime_v( $s, 'show_answer', 'yes' ) && prime_v( $s, 'answer_text' );
	?>
	<section class="banner prime-sec">
		<div class="container">
			<?php if ( 'yes' === prime_v( $s, 'show_crumbs', 'yes' ) ) : ?>
				<nav class="crumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'prime' ); ?>" data-anim="fade-up">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'prime' ); ?></a><span aria-hidden="true">/</span>
					<?php foreach ( $crumbs as $c ) : ?><a href="<?php echo esc_url( $c[1] ); ?>"><?php echo esc_html( $c[0] ); ?></a><span aria-hidden="true">/</span><?php endforeach; ?>
					<span aria-current="page"><?php echo esc_html( $current ); ?></span>
				</nav>
			<?php endif; ?>
			<?php if ( prime_v( $s, 'eyebrow' ) ) : ?><p class="eyebrow" data-anim="fade-up"><?php echo esc_html( $s['eyebrow'] ); ?></p><?php endif; ?>
			<h1 class="banner__title" data-split><?php echo prime_h( prime_v( $s, 'title', $current ) ); // phpcs:ignore ?></h1>
			<div class="banner__row">
				<?php if ( prime_v( $s, 'lead' ) ) : ?><p class="banner__lead" data-anim="fade-up"><?php echo esc_html( $s['lead'] ); ?></p><?php endif; ?>
				<?php if ( prime_v( $s, 'btn1_text' ) || prime_v( $s, 'btn2_text' ) ) : ?>
					<div class="banner__actions" data-anim="fade-up">
						<?php if ( prime_v( $s, 'btn1_text' ) ) : ?><a class="btn btn--primary btn--lg" <?php echo prime_cta_attrs( prime_v( $s, 'btn1_link', prime_link( '#enquiry' ) ) ); // phpcs:ignore ?>><span><?php echo esc_html( $s['btn1_text'] ); ?></span><?php echo prime_icon( 'arrow', 18 ); // phpcs:ignore ?></a><?php endif; ?>
						<?php if ( prime_v( $s, 'btn2_text' ) ) : ?><a class="btn btn--outline btn--lg" <?php echo prime_link_attrs( prime_v( $s, 'btn2_link', prime_link( '#faq' ) ) ); // phpcs:ignore ?>><span><?php echo esc_html( $s['btn2_text'] ); ?></span></a><?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
			<?php if ( $chips ) : ?>
				<ul class="banner__chips" data-anim="stagger">
					<?php foreach ( $chips as $chip ) : ?><li><?php echo prime_icon( 'check', 15 ); // phpcs:ignore ?><?php echo esc_html( $chip ); ?></li><?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<div class="banner__band <?php echo $answer ? '' : 'banner__band--solo'; ?>">
				<?php if ( $answer ) : ?>
					<aside class="answer-card" data-anim="rise">
						<p class="answer-card__label"><?php esc_html_e( 'Quick answer', 'prime' ); ?></p>
						<?php if ( prime_v( $s, 'answer_q' ) ) : ?><p class="answer-card__q"><?php echo esc_html( $s['answer_q'] ); ?></p><?php endif; ?>
						<p class="answer-card__a"><?php echo esc_html( $s['answer_text'] ); ?></p>
					</aside>
				<?php endif; ?>
				<div class="banner__img" data-parallax><img src="<?php echo esc_url( $img ); ?>" alt="" fetchpriority="high" decoding="async"></div>
			</div>
		</div>
	</section>
	<?php
}

function prime_sec_contact_cards( $s ) {
	?>
	<section class="section contact-cards prime-sec">
		<div class="container">
			<div class="contact-cards__grid" data-anim="stagger">
				<?php foreach ( (array) prime_v( $s, 'items', array() ) as $c ) : $href = prime_href( prime_v( $c, 'link' ) ); $tag = $href ? 'a' : 'div'; ?>
					<<?php echo $tag; // phpcs:ignore ?> class="contact-card" <?php echo $href ? prime_link_attrs( $c['link'] ) : ''; // phpcs:ignore ?>>
						<span class="contact-card__icon"><?php echo prime_icon( prime_v( $c, 'icon', 'mail' ), 22 ); // phpcs:ignore ?></span>
						<small><?php echo esc_html( prime_v( $c, 'label' ) ); ?></small>
						<strong><?php echo esc_html( prime_v( $c, 'value' ) ); ?></strong>
					</<?php echo $tag; // phpcs:ignore ?>>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php
}

function prime_sec_intro( $s ) {
	$rows = (array) prime_v( $s, 'glance', array() );
	$toc  = (array) prime_v( $s, 'toc', array() );
	?>
	<section class="section intro prime-sec">
		<div class="container intro__layout <?php echo ( $rows || $toc ) ? '' : 'intro__layout--solo'; ?>">
			<div class="intro__main">
				<?php prime_label( '', prime_v( $s, 'label' ) ); ?>
				<h2 class="h2" data-split><?php echo prime_h( prime_v( $s, 'title' ) ); // phpcs:ignore ?></h2>
				<div class="intro__text" data-anim="fade-up"><?php echo wp_kses_post( wpautop( prime_v( $s, 'content' ) ) ); ?></div>
			</div>
			<?php if ( $rows || $toc ) : ?>
				<aside class="glance" data-anim="rise">
					<?php if ( $rows ) : ?>
						<p class="glance__title"><?php echo esc_html( prime_v( $s, 'glance_title', __( 'At a glance', 'prime' ) ) ); ?></p>
						<dl><?php foreach ( $rows as $r ) : ?><div><dt><?php echo esc_html( prime_v( $r, 'label' ) ); ?></dt><dd><?php echo esc_html( prime_v( $r, 'value' ) ); ?></dd></div><?php endforeach; ?></dl>
					<?php endif; ?>
					<?php if ( $toc ) : ?>
						<p class="glance__title"><?php esc_html_e( 'On this page', 'prime' ); ?></p>
						<ul class="glance__toc"><?php foreach ( $toc as $t ) : ?><li><a href="#<?php echo esc_attr( ltrim( prime_v( $t, 'anchor' ), '#' ) ); ?>"><?php echo esc_html( prime_v( $t, 'text' ) ); ?><?php echo prime_icon( 'arrow', 14 ); // phpcs:ignore ?></a></li><?php endforeach; ?></ul>
					<?php endif; ?>
				</aside>
			<?php endif; ?>
		</div>
	</section>
	<?php
}

function prime_sec_hub_grid( $s ) {
	?>
	<section class="section services prime-sec is-sand" id="<?php echo esc_attr( prime_v( $s, 'anchor', 'services-list' ) ); ?>">
		<div class="container">
			<?php prime_shead( $s, array( 'intro' => '' ) ); ?>
			<div class="svc-grid svc-grid--hub" data-anim="stagger">
				<?php foreach ( (array) prime_v( $s, 'cards', array() ) as $c ) : ?>
					<a class="svc-card" <?php echo prime_link_attrs( prime_v( $c, 'link' ) ); // phpcs:ignore ?>>
						<span class="svc-card__icon"><?php echo prime_icon( prime_v( $c, 'icon', 'layers' ), 26 ); // phpcs:ignore ?></span>
						<h3><?php echo esc_html( prime_v( $c, 'title' ) ); ?></h3>
						<p><?php echo esc_html( prime_v( $c, 'text' ) ); ?></p>
						<?php if ( prime_v( $c, 'keyword' ) ) : ?><span class="svc-card__kw"><?php echo esc_html( $c['keyword'] ); ?></span><?php endif; ?>
						<span class="svc-card__link"><?php esc_html_e( 'Learn more', 'prime' ); ?> <?php echo prime_icon( 'arrow', 16 ); // phpcs:ignore ?></span>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php
}

function prime_sec_features( $s ) {
	$items = (array) prime_v( $s, 'items', array() );
	?>
	<section class="section features prime-sec is-dark" id="<?php echo esc_attr( prime_v( $s, 'anchor', 'features' ) ); ?>">
		<?php echo prime_blobs( 2 ); // phpcs:ignore ?>
		<div class="container">
			<?php prime_shead( $s, array( 'light' => true ) ); ?>
			<div class="feature-grid <?php echo count( $items ) > 6 ? 'feature-grid--dense' : ''; ?>" data-anim="stagger">
				<?php foreach ( $items as $f ) : ?>
					<article class="feature">
						<span class="feature__icon"><?php echo prime_icon( prime_v( $f, 'icon', 'spark' ), 24 ); // phpcs:ignore ?></span>
						<h3><?php echo esc_html( prime_v( $f, 'title' ) ); ?></h3>
						<p><?php echo esc_html( prime_v( $f, 'text' ) ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php
}

function prime_sec_roles( $s ) {
	$roles = prime_lines( prime_v( $s, 'roles' ) );
	if ( ! $roles ) {
		return;
	}
	?>
	<section class="roles prime-sec" aria-label="<?php echo esc_attr( prime_v( $s, 'label' ) ); ?>">
		<div class="container roles__head"><?php prime_label( '', prime_v( $s, 'label' ) ); ?></div>
		<div class="roles__marquee" data-marquee="-1">
			<div class="ticker__track">
				<?php for ( $r = 0; $r < 2; $r++ ) : foreach ( $roles as $role ) : ?>
					<span class="role-chip"><?php echo esc_html( $role ); ?></span>
				<?php endforeach; endfor; ?>
			</div>
		</div>
	</section>
	<?php
}

function prime_sec_cta_band( $s ) {
	$img = prime_media_or_photo( prime_v( $s, 'image' ), 'windows' );
	?>
	<section class="cta-band prime-sec">
		<div class="container"><div class="cta-band__box">
		<?php echo prime_blobs( 2 ); // phpcs:ignore ?>
		<div class="cta-band__media morph" aria-hidden="true"><img src="<?php echo esc_url( $img ); ?>" alt="" loading="lazy" decoding="async"></div>
		<div class="cta-band__inner">
			<div class="cta-band__copy">
				<?php prime_label( '', prime_v( $s, 'label' ), 'eyebrow--light' ); ?>
				<h2 class="h2" data-split><?php echo prime_h( prime_v( $s, 'title' ) ); // phpcs:ignore ?></h2>
				<?php if ( prime_v( $s, 'text' ) ) : ?><p data-anim="fade-up"><?php echo esc_html( $s['text'] ); ?></p><?php endif; ?>
			</div>
			<div class="cta-band__actions" data-anim="fade-up">
				<?php if ( prime_v( $s, 'btn_text' ) ) : ?><a class="btn btn--light btn--lg" <?php echo prime_cta_attrs( prime_v( $s, 'btn_link', prime_link( '#enquiry' ) ), prime_v( $s, 'preset' ) ); // phpcs:ignore ?>><span><?php echo esc_html( $s['btn_text'] ); ?></span><?php echo prime_icon( 'arrow', 18 ); // phpcs:ignore ?></a><?php endif; ?>
				<?php if ( 'yes' === prime_v( $s, 'show_call', 'yes' ) && prime_tel_url() ) : ?><a class="btn btn--glass btn--lg" href="<?php echo esc_attr( prime_tel_url() ); ?>"><?php echo prime_icon( 'phone', 18 ); // phpcs:ignore ?><span><?php echo esc_html( prime_opt( 'contact_phone' ) ); ?></span></a><?php endif; ?>
				<?php if ( 'yes' === prime_v( $s, 'show_whatsapp', 'yes' ) && prime_whatsapp_url() ) : ?><a class="btn btn--whatsapp btn--lg" href="<?php echo esc_url( prime_whatsapp_url() ); ?>" target="_blank" rel="noopener"><?php echo prime_icon( 'whatsapp', 18 ); // phpcs:ignore ?><span>WhatsApp</span></a><?php endif; ?>
			</div>
		</div>
		</div></div>
	</section>
	<?php
}

function prime_sec_timeline( $s ) {
	?>
	<section class="section timeline prime-sec" id="<?php echo esc_attr( prime_v( $s, 'anchor', 'process' ) ); ?>">
		<div class="container">
			<?php prime_shead( $s, array( 'intro' => '', 'link' => false ) ); ?>
			<ol class="timeline__track" data-timeline>
				<span class="timeline__line" aria-hidden="true"><i></i></span>
				<?php foreach ( (array) prime_v( $s, 'steps', array() ) as $i => $st ) : ?>
					<li class="timeline__step">
						<span class="timeline__dot"><?php echo esc_html( prime_nn( $i + 1 ) ); ?></span>
						<h3><?php echo esc_html( prime_v( $st, 'title' ) ); ?></h3>
						<p><?php echo esc_html( prime_v( $st, 'text' ) ); ?></p>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</section>
	<?php
}

function prime_sec_facts( $s ) {
	$facts = prime_lines( prime_v( $s, 'facts' ) );
	$areas = prime_lines( prime_v( $s, 'areas' ) );
	$links = (array) prime_v( $s, 'links', array() );
	?>
	<section class="section facts prime-sec is-sand" id="<?php echo esc_attr( prime_v( $s, 'anchor', 'facts' ) ); ?>">
		<div class="container facts__layout">
			<?php if ( $facts ) : ?>
				<div class="facts__sheet" data-anim="rise">
					<?php prime_label( '', prime_v( $s, 'label', __( 'Key facts', 'prime' ) ) ); ?>
					<h2 class="h3"><?php echo esc_html( prime_v( $s, 'title' ) ); ?></h2>
					<ol class="facts__list" data-anim="stagger">
						<?php foreach ( $facts as $i => $fact ) : ?><li><span class="facts__num"><?php echo esc_html( prime_nn( $i + 1 ) ); ?></span><p><?php echo esc_html( $fact ); ?></p></li><?php endforeach; ?>
					</ol>
				</div>
			<?php endif; ?>
			<div class="facts__areas">
				<?php prime_label( '', prime_v( $s, 'areas_label', __( 'Areas served', 'prime' ) ) ); ?>
				<h2 class="h3" data-split><?php echo prime_h( prime_v( $s, 'areas_title' ) ); // phpcs:ignore ?></h2>
				<ul class="area-chips" data-anim="stagger">
					<?php foreach ( $areas as $c ) : ?><li><?php echo prime_icon( 'pin', 14 ); // phpcs:ignore ?><?php echo esc_html( $c ); ?></li><?php endforeach; ?>
				</ul>
				<?php if ( $links ) : ?>
					<div class="area-links" data-anim="fade-up">
						<?php foreach ( $links as $l ) : ?><a class="link-arrow" <?php echo prime_link_attrs( prime_v( $l, 'link' ) ); // phpcs:ignore ?>><?php echo esc_html( prime_v( $l, 'text' ) ); ?> <?php echo prime_icon( 'arrow', 14 ); // phpcs:ignore ?></a><?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</section>
	<?php
}

function prime_sec_related( $s ) {
	?>
	<section class="section related prime-sec is-sand">
		<div class="container">
			<?php prime_label( '', prime_v( $s, 'label', __( 'Keep exploring', 'prime' ) ) ); ?>
			<div class="related__grid" data-anim="stagger">
				<?php foreach ( (array) prime_v( $s, 'cards', array() ) as $c ) : ?>
					<a class="related-card" <?php echo prime_link_attrs( prime_v( $c, 'link' ) ); // phpcs:ignore ?>>
						<span class="related-card__icon"><?php echo prime_icon( prime_v( $c, 'icon', 'layers' ), 22 ); // phpcs:ignore ?></span>
						<span class="related-card__kicker"><?php echo esc_html( prime_v( $c, 'kicker' ) ); ?></span>
						<strong><?php echo esc_html( prime_v( $c, 'title' ) ); ?></strong>
						<span class="related-card__arrow"><?php echo prime_icon( 'arrow', 20 ); // phpcs:ignore ?></span>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php
}

/* ==========================================================================
   CORPORATE GIFTING + BUSINESS CARD
   ========================================================================== */

function prime_sec_gifting( $s ) {
	$items  = prime_lines( prime_v( $s, 'items' ) );
	$ideal  = prime_lines( prime_v( $s, 'ideal' ) );
	$badges = prime_lines( prime_v( $s, 'badges' ) );
	$paras  = prime_lines( prime_v( $s, 'text' ) );
	$photo  = prime_media_url( prime_v( $s, 'image' ) );
	$icons  = array( 'layers', 'palette', 'star', 'truck' );
	?>
	<section class="section gifting prime-sec" id="<?php echo esc_attr( prime_v( $s, 'anchor', 'corporate-gifting' ) ); ?>">
		<div class="container gifting__layout">
			<div class="gifting__visual" data-anim="rise">
				<?php if ( $photo ) : ?>
					<img class="gifting__photo" src="<?php echo esc_url( $photo ); ?>" alt="<?php echo esc_attr( wp_strip_all_tags( str_replace( '*', '', prime_v( $s, 'title' ) ) ) ); ?>" loading="lazy" decoding="async">
				<?php else : ?>
					<div class="gift-art" aria-hidden="true">
						<span class="gift-art__blob morph"></span>
						<svg class="gift-art__box" viewBox="0 0 240 240">
							<defs>
								<linearGradient id="gaGold" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#FB7185"/><stop offset="1" stop-color="#F43F5E"/></linearGradient>
								<linearGradient id="gaBox" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#2a2a2a"/><stop offset="1" stop-color="#111111"/></linearGradient>
							</defs>
							<rect x="44" y="104" width="152" height="112" rx="6" fill="url(#gaBox)"/>
							<rect x="34" y="78" width="172" height="36" rx="5" fill="#1c1c1c"/>
							<rect x="108" y="78" width="24" height="138" fill="url(#gaGold)"/>
							<path d="M120 78c-10-26-46-38-52-18-5 16 26 20 52 18Z" fill="none" stroke="url(#gaGold)" stroke-width="10" stroke-linejoin="round"/>
							<path d="M120 78c10-26 46-38 52-18 5 16-26 20-52 18Z" fill="none" stroke="url(#gaGold)" stroke-width="10" stroke-linejoin="round"/>
							<circle cx="120" cy="78" r="10" fill="#FB7185"/>
						</svg>
						<span class="gift-art__chip gift-art__chip--1"><?php echo prime_icon( 'palette', 16 ); // phpcs:ignore ?><span><strong><?php esc_html_e( 'Your logo', 'prime' ); ?></strong><small><?php esc_html_e( 'on every gift', 'prime' ); ?></small></span></span>
						<span class="gift-art__chip gift-art__chip--2"><?php echo prime_icon( 'truck', 16 ); // phpcs:ignore ?><span><strong><?php esc_html_e( 'Pan-India', 'prime' ); ?></strong><small><?php esc_html_e( 'delivery', 'prime' ); ?></small></span></span>
						<svg class="gift-art__stamp" viewBox="0 0 120 120"><defs><path id="gaCircle" d="M60 60m-46 0a46 46 0 1 1 92 0a46 46 0 1 1-92 0"/></defs><text><textPath href="#gaCircle">CUSTOMISED • BULK ORDERS • PAN-INDIA • </textPath></text></svg>
					</div>
				<?php endif; ?>
			</div>
			<div class="gifting__copy">
				<?php prime_label( '', prime_v( $s, 'label', __( 'Corporate gifting', 'prime' ) ) ); ?>
				<h2 class="h2" data-split><?php echo prime_h( prime_v( $s, 'title' ) ); // phpcs:ignore ?></h2>
				<div class="gifting__text" data-anim="fade-up"><?php foreach ( $paras as $p ) : ?><p><?php echo esc_html( $p ); ?></p><?php endforeach; ?></div>
				<?php if ( $items ) : ?>
					<h3 class="gifting__subtitle"><?php echo esc_html( prime_v( $s, 'list_title', __( 'Popular corporate gifts in India include:', 'prime' ) ) ); ?></h3>
					<ul class="gift-list" data-anim="stagger"><?php foreach ( $items as $it ) : ?><li><?php echo prime_icon( 'check', 15 ); // phpcs:ignore ?><span><?php echo esc_html( $it ); ?></span></li><?php endforeach; ?></ul>
				<?php endif; ?>
				<?php if ( $ideal ) : ?>
					<div class="gifting__ideal" data-anim="fade-up"><span class="gifting__ideal-title"><?php echo esc_html( prime_v( $s, 'ideal_title', __( 'Ideal for', 'prime' ) ) ); ?></span><ul><?php foreach ( $ideal as $i ) : ?><li><?php echo esc_html( $i ); ?></li><?php endforeach; ?></ul></div>
				<?php endif; ?>
				<?php if ( $badges ) : ?>
					<ul class="gift-badges" data-anim="stagger"><?php foreach ( $badges as $k => $b ) : ?><li><?php echo prime_icon( $icons[ $k % 4 ], 18 ); // phpcs:ignore ?><?php echo esc_html( $b ); ?></li><?php endforeach; ?></ul>
				<?php endif; ?>
				<div class="gifting__actions" data-anim="fade-up">
					<?php if ( prime_v( $s, 'btn_text' ) ) : ?><a class="btn btn--dark btn--lg" <?php echo prime_link_attrs( prime_v( $s, 'btn_link', prime_link( '#enquiry' ) ) ); // phpcs:ignore ?> data-modal="enquiry" data-preset="gifting"><span><?php echo esc_html( $s['btn_text'] ); ?></span><?php echo prime_icon( 'gift', 18 ); // phpcs:ignore ?></a><?php endif; ?>
					<?php if ( prime_v( $s, 'btn2_text' ) ) : ?><a class="link-arrow" <?php echo prime_link_attrs( prime_v( $s, 'btn2_link' ) ); // phpcs:ignore ?>><?php echo esc_html( $s['btn2_text'] ); ?> <?php echo prime_icon( 'arrow', 16 ); // phpcs:ignore ?></a><?php endif; ?>
				</div>
			</div>
		</div>
	</section>
	<?php
}

/**
 * The business card, recreated in HTML.
 */
function prime_business_card_html( $s = array() ) {
	$name  = prime_v( $s, 'person', prime_opt( 'contact_person' ) );
	$role  = prime_v( $s, 'role', prime_opt( 'contact_role' ) );
	$email = prime_opt( 'contact_email' );
	$phone = prime_opt( 'contact_phone' );
	$site  = prime_opt( 'website' );
	$addr  = prime_opt( 'contact_address' );
	ob_start();
	?>
	<div class="bcard">
		<div class="bcard__brand">
			<img src="<?php echo esc_url( prime_img( 'logo-mark-light.png' ) ); ?>" width="96" height="88" alt="" loading="lazy" decoding="async">
			<strong>PRIME<br>RECRUITMENTS</strong>
			<small><?php esc_html_e( 'Connecting talent with opportunity', 'prime' ); ?></small>
		</div>
		<div class="bcard__info">
			<?php if ( $name ) : ?><p class="bcard__name"><?php echo esc_html( $name ); ?></p><?php endif; ?>
			<?php if ( $role ) : ?><p class="bcard__role"><?php echo esc_html( $role ); ?></p><?php endif; ?>
			<ul>
				<?php if ( $email ) : ?><li><span><?php echo prime_icon( 'mail', 13 ); // phpcs:ignore ?></span><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></li><?php endif; ?>
				<?php if ( $phone ) : ?><li><span><?php echo prime_icon( 'phone', 13 ); // phpcs:ignore ?></span><a href="<?php echo esc_attr( prime_tel_url() ); ?>"><?php echo esc_html( $phone ); ?></a></li><?php endif; ?>
				<?php if ( $site ) : ?><li><span><?php echo prime_icon( 'globe', 13 ); // phpcs:ignore ?></span><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( $site ); ?></a></li><?php endif; ?>
				<?php if ( $addr ) : ?><li><span><?php echo prime_icon( 'pin', 13 ); // phpcs:ignore ?></span><span><?php echo esc_html( $addr ); ?></span></li><?php endif; ?>
			</ul>
		</div>
	</div>
	<?php
	return ob_get_clean();
}

function prime_sec_business_card( $s ) {
	?>
	<section class="section bcard-section prime-sec is-sand" id="<?php echo esc_attr( prime_v( $s, 'anchor', 'business-card' ) ); ?>">
		<div class="container bcard-section__layout">
			<div class="bcard-section__copy">
				<?php prime_label( '', prime_v( $s, 'label', __( 'Talk to us directly', 'prime' ) ) ); ?>
				<h2 class="h2" data-split><?php echo prime_h( prime_v( $s, 'title', 'Your direct line to *Prime*' ) ); // phpcs:ignore ?></h2>
				<p class="muted" data-anim="fade-up"><?php echo esc_html( prime_v( $s, 'text' ) ); ?></p>
				<div class="bcard-actions" data-anim="stagger">
					<?php if ( prime_tel_url() ) : ?><a class="btn btn--dark" href="<?php echo esc_attr( prime_tel_url() ); ?>"><?php echo prime_icon( 'phone', 18 ); // phpcs:ignore ?><span><?php esc_html_e( 'Call now', 'prime' ); ?></span></a><?php endif; ?>
					<?php if ( prime_whatsapp_url() ) : ?><a class="btn btn--whatsapp" href="<?php echo esc_url( prime_whatsapp_url() ); ?>" target="_blank" rel="noopener"><?php echo prime_icon( 'whatsapp', 18 ); // phpcs:ignore ?><span>WhatsApp</span></a><?php endif; ?>
					<a class="btn btn--outline" href="mailto:<?php echo esc_attr( prime_opt( 'contact_email' ) ); ?>"><?php echo prime_icon( 'mail', 18 ); // phpcs:ignore ?><span><?php esc_html_e( 'Email', 'prime' ); ?></span></a>
					<a class="btn btn--outline" href="<?php echo esc_url( home_url( '/prime-contact.vcf' ) ); ?>" download><?php echo prime_icon( 'download', 18 ); // phpcs:ignore ?><span><?php esc_html_e( 'Save contact', 'prime' ); ?></span></a>
				</div>
				<?php if ( prime_socials() ) : ?><div class="bcard-social" data-anim="fade-up"><span><?php esc_html_e( 'Follow us', 'prime' ); ?></span><div class="social"><?php echo prime_socials(); // phpcs:ignore ?></div></div><?php endif; ?>
			</div>
			<div class="bcard-section__card" data-anim="rise"><?php echo prime_business_card_html( $s ); // phpcs:ignore ?></div>
		</div>
	</section>
	<?php
}
