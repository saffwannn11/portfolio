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

/* ==========================================================================
   HOMEPAGE SECTIONS
   ========================================================================== */

function prime_sec_hero( $s ) {
	$words = array_map( 'trim', explode( ',', prime_v( $s, 'rotator', 'scale.' ) ) );
	$code  = '<span class="c-k">const</span> role = {' . "\n"
		. '  title: <span class="c-s">"' . esc_html( prime_v( $s, 'console_role' ) ) . '"</span>,' . "\n"
		. '  city:  <span class="c-s">"' . esc_html( prime_v( $s, 'console_city' ) ) . '"</span>,' . "\n"
		. '  exp:   <span class="c-s">"' . esc_html( prime_v( $s, 'console_exp' ) ) . '"</span>' . "\n"
		. '};' . "\n"
		. '<span class="c-k">await</span> prime.<span class="c-f">search</span>(role);   <span class="c-c">// map market</span>' . "\n"
		. '<span class="c-k">await</span> prime.<span class="c-f">interview</span>();     <span class="c-c">// human screen</span>' . "\n"
		. '<span class="c-k">return</span> prime.<span class="c-f">shortlist</span>(<span class="c-n">' . count( (array) prime_v( $s, 'matches', array() ) ) . '</span>); <span class="c-ok">✓</span>';
	?>
	<section class="hero prime-sec" data-hero>
		<canvas class="hero__canvas" aria-hidden="true"></canvas>
		<div class="hero__bg" aria-hidden="true"><span class="hero__grid"></span><span class="aurora a1"></span><span class="aurora a2"></span><span class="hero__spot"></span></div>
		<div class="container hero__layout">
			<div class="hero__copy">
				<?php if ( prime_v( $s, 'eyebrow' ) ) : ?><p class="pill" data-anim="fade-up"><span class="pulse-dot"></span><?php echo esc_html( $s['eyebrow'] ); ?></p><?php endif; ?>
				<h1 class="hero__title">
					<?php if ( prime_v( $s, 'title_1' ) ) : ?><span class="hero__line" data-split><?php echo esc_html( $s['title_1'] ); ?></span><?php endif; ?>
					<?php if ( prime_v( $s, 'title_2' ) ) : ?><span class="hero__line" data-split><?php echo esc_html( $s['title_2'] ); ?></span><?php endif; ?>
					<span class="hero__line" data-anim="fade-up"><?php echo esc_html( prime_v( $s, 'title_3' ) ); ?> <span class="rotator" data-rotator="<?php echo esc_attr( implode( ',', $words ) ); ?>"><em class="accent"><?php echo esc_html( $words[0] ); ?></em></span></span>
				</h1>
				<p class="hero__lead" data-anim="fade-up"><?php echo esc_html( prime_v( $s, 'lead' ) ); ?></p>
				<div class="hero__actions" data-anim="fade-up">
					<?php if ( prime_v( $s, 'btn1_text' ) ) : ?><a class="btn btn--primary btn--lg" <?php echo prime_link_attrs( prime_v( $s, 'btn1_link' ) ); // phpcs:ignore ?> data-magnetic><span><?php echo esc_html( $s['btn1_text'] ); ?></span><?php echo prime_icon( 'arrow', 18 ); // phpcs:ignore ?></a><?php endif; ?>
					<?php if ( prime_v( $s, 'btn2_text' ) ) : ?><a class="btn btn--ghost btn--lg" <?php echo prime_link_attrs( prime_v( $s, 'btn2_link' ) ); // phpcs:ignore ?> data-magnetic><span><?php echo esc_html( $s['btn2_text'] ); ?></span><?php echo prime_icon( 'arrow-ur', 18 ); // phpcs:ignore ?></a><?php endif; ?>
				</div>
				<?php if ( prime_v( $s, 'trust_title' ) ) : ?>
					<div class="hero__trust" data-anim="fade-up">
						<span class="avatars" aria-hidden="true"><i>AK</i><i>RS</i><i>NP</i><i>+</i></span>
						<span><strong><?php echo esc_html( $s['trust_title'] ); ?></strong><small><?php echo esc_html( prime_v( $s, 'trust_text' ) ); ?></small></span>
					</div>
				<?php endif; ?>
			</div>

			<div class="hero__visual" data-anim="rise" data-tilt>
				<div class="console">
					<div class="console__bar">
						<span class="dots" aria-hidden="true"><i></i><i></i><i></i></span>
						<span class="mono"><?php echo esc_html( prime_v( $s, 'console_file', 'prime/match-engine.ts' ) ); ?></span>
						<span class="console__status"><span class="pulse-dot"></span>live</span>
					</div>
					<pre class="console__code mono" aria-hidden="true"><code data-typer><?php echo $code; // phpcs:ignore -- escaped above ?></code></pre>
					<div class="console__results">
						<?php foreach ( (array) prime_v( $s, 'matches', array() ) as $m ) : $score = max( 0, min( 100, (int) prime_v( $m, 'score', 90 ) ) ); ?>
							<div class="match"><span class="match__av"><?php echo esc_html( mb_substr( prime_v( $m, 'name', 'C' ), -1 ) ); ?></span><span class="match__info"><strong><?php echo esc_html( prime_v( $m, 'name' ) ); ?></strong><small><?php echo esc_html( prime_v( $m, 'skills' ) ); ?></small></span><span class="match__bar"><i style="--w:<?php echo esc_attr( $score ); ?>%"></i></span><b><?php echo esc_html( $score ); ?>%</b></div>
						<?php endforeach; ?>
					</div>
				</div>
				<?php foreach ( array_slice( (array) prime_v( $s, 'chips', array() ), 0, 3 ) as $i => $c ) : ?>
					<div class="float-chip float-chip--<?php echo (int) $i + 1; ?>"><?php echo prime_icon( prime_v( $c, 'icon', 'zap' ), 16 ); // phpcs:ignore ?><span><strong><?php echo esc_html( prime_v( $c, 'title' ) ); ?></strong><small><?php echo esc_html( prime_v( $c, 'text' ) ); ?></small></span></div>
				<?php endforeach; ?>
			</div>
		</div>
		<div class="hero__scroll" aria-hidden="true"><span class="mono">scroll</span><i></i></div>
	</section>
	<?php
}

function prime_sec_marquee( $s ) {
	$rows = array( prime_lines( prime_v( $s, 'row_1' ) ), prime_lines( prime_v( $s, 'row_2' ) ) );
	?>
	<section class="kinetic prime-sec" aria-label="<?php esc_attr_e( 'Highlights', 'prime' ); ?>">
		<?php foreach ( $rows as $r => $items ) : if ( ! $items ) { continue; } ?>
			<div class="kinetic__row <?php echo 1 === $r ? 'kinetic__row--outline' : ''; ?>" data-marquee="<?php echo 1 === $r ? '1' : '-1'; ?>">
				<div class="kinetic__track">
					<?php for ( $k = 0; $k < 2; $k++ ) : foreach ( $items as $t ) : ?>
						<span><?php echo esc_html( $t ); ?></span><i><?php echo 1 === $r ? '●' : '✦'; ?></i>
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
		<div class="container">
			<?php prime_label( prime_v( $s, 'label_num' ), prime_v( $s, 'label' ) ); ?>
			<p class="statement__text" data-scrub-text><?php echo esc_html( prime_v( $s, 'text' ) ); ?></p>
			<div class="statement__foot">
				<div class="statement__sig" data-anim="fade-up">
					<img class="statement__mark" src="<?php echo esc_url( PRIME_URI . '/assets/img/logo-mark.png' ); ?>" width="44" height="40" alt="" loading="lazy" decoding="async">
					<span><strong><?php echo esc_html( prime_v( $s, 'sig_title' ) ); ?></strong><small><?php echo esc_html( prime_v( $s, 'sig_text' ) ); ?></small></span>
				</div>
				<?php if ( prime_v( $s, 'link_text' ) ) : ?><a class="link-arrow" <?php echo prime_link_attrs( prime_v( $s, 'link' ) ); // phpcs:ignore ?> data-anim="fade-up"><?php echo esc_html( $s['link_text'] ); ?> <?php echo prime_icon( 'arrow', 16 ); // phpcs:ignore ?></a><?php endif; ?>
			</div>
		</div>
	</section>
	<?php
}

function prime_sec_services_slider( $s ) {
	$cards = (array) prime_v( $s, 'cards', array() );
	?>
	<section class="hscroll prime-sec" data-hscroll>
		<div class="hscroll__pin">
			<div class="container hscroll__head">
				<div>
					<?php prime_label( prime_v( $s, 'label_num' ), prime_v( $s, 'label' ), 'label--light' ); ?>
					<h2 class="h2" data-split><?php echo prime_h( prime_v( $s, 'title' ) ); // phpcs:ignore ?></h2>
				</div>
				<div class="hscroll__meta">
					<span class="mono hscroll__count"><b data-hcount>01</b> / <?php echo esc_html( prime_nn( count( $cards ) ) ); ?></span>
					<span class="hscroll__bar"><i data-hbar></i></span>
				</div>
			</div>
			<div class="hscroll__viewport">
				<div class="hscroll__track" data-htrack>
					<?php foreach ( $cards as $n => $c ) : ?>
						<a class="svc-card" <?php echo prime_link_attrs( prime_v( $c, 'link' ) ); // phpcs:ignore ?> data-spotlight>
							<span class="svc-card__num mono"><?php echo esc_html( prime_nn( $n + 1 ) ); ?></span>
							<span class="svc-card__icon"><?php echo prime_icon( prime_v( $c, 'icon', 'users' ), 26 ); // phpcs:ignore ?></span>
							<h3><?php echo esc_html( prime_v( $c, 'title' ) ); ?></h3>
							<p><?php echo esc_html( prime_v( $c, 'text' ) ); ?></p>
							<?php $pts = prime_lines( prime_v( $c, 'points' ) ); if ( $pts ) : ?>
								<ul><?php foreach ( $pts as $pt ) : ?><li><?php echo esc_html( $pt ); ?></li><?php endforeach; ?></ul>
							<?php endif; ?>
							<span class="svc-card__link"><?php echo esc_html( prime_v( $s, 'card_link_text', __( 'Explore', 'prime' ) ) ); ?> <?php echo prime_icon( 'arrow', 16 ); // phpcs:ignore ?></span>
						</a>
					<?php endforeach; ?>
					<?php if ( prime_v( $s, 'cta_title' ) ) : ?>
						<a class="svc-card svc-card--cta" <?php echo prime_link_attrs( prime_v( $s, 'cta_link' ) ); // phpcs:ignore ?>>
							<span class="mono"><?php echo esc_html( prime_v( $s, 'cta_kicker' ) ); ?></span>
							<h3><?php echo esc_html( $s['cta_title'] ); ?></h3>
							<p><?php echo esc_html( prime_v( $s, 'cta_text' ) ); ?></p>
							<span class="svc-card__link"><?php echo esc_html( prime_v( $s, 'cta_link_text' ) ); ?> <?php echo prime_icon( 'arrow', 16 ); // phpcs:ignore ?></span>
						</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</section>
	<?php
}

function prime_sec_stats( $s ) {
	?>
	<section class="section stats prime-sec">
		<div class="container">
			<div class="stats__grid">
				<?php foreach ( (array) prime_v( $s, 'items', array() ) as $it ) : $ring = max( 0, min( 100, (int) prime_v( $it, 'ring', 75 ) ) ) / 100; ?>
					<div class="stat" data-anim="fade-up">
						<svg class="stat__ring" viewBox="0 0 120 120" aria-hidden="true"><circle cx="60" cy="60" r="54"/><circle class="stat__ring-fill" cx="60" cy="60" r="54" style="--p:<?php echo esc_attr( $ring ); ?>"/></svg>
						<p class="stat__num"><span data-count="<?php echo esc_attr( prime_v( $it, 'number' ) ); ?>"><?php echo esc_html( prime_v( $it, 'number' ) ); ?></span><em><?php echo esc_html( prime_v( $it, 'suffix' ) ); ?></em></p>
						<p class="stat__label"><?php echo esc_html( prime_v( $it, 'label' ) ); ?></p>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php
}

function prime_sec_process( $s ) {
	?>
	<section class="section process prime-sec" data-process>
		<div class="container process__layout">
			<div class="process__head">
				<?php prime_label( prime_v( $s, 'label_num' ), prime_v( $s, 'label' ) ); ?>
				<h2 class="h2" data-split><?php echo prime_h( prime_v( $s, 'title' ) ); // phpcs:ignore ?></h2>
				<p class="muted" data-anim="fade-up"><?php echo esc_html( prime_v( $s, 'text' ) ); ?></p>
				<?php if ( prime_v( $s, 'btn_text' ) ) : ?><a class="btn btn--dark" <?php echo prime_link_attrs( prime_v( $s, 'btn_link' ) ); // phpcs:ignore ?> data-anim="fade-up" data-magnetic><span><?php echo esc_html( $s['btn_text'] ); ?></span><?php echo prime_icon( 'arrow', 18 ); // phpcs:ignore ?></a><?php endif; ?>
			</div>
			<ol class="process__steps">
				<span class="process__line" aria-hidden="true"><i data-process-line></i></span>
				<?php foreach ( (array) prime_v( $s, 'steps', array() ) as $i => $st ) : ?>
					<li class="process__step" data-step>
						<span class="process__dot mono"><?php echo esc_html( prime_nn( $i + 1 ) ); ?></span>
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
	<section class="section industries-reel prime-sec" data-reveal-list>
		<div class="container">
			<div class="section-head">
				<div>
					<?php prime_label( prime_v( $s, 'label_num' ), prime_v( $s, 'label' ), 'label--light' ); ?>
					<h2 class="h2" data-split><?php echo prime_h( prime_v( $s, 'title' ) ); // phpcs:ignore ?></h2>
				</div>
				<?php if ( prime_v( $s, 'link_text' ) ) : ?><a class="link-arrow link-arrow--light" <?php echo prime_link_attrs( prime_v( $s, 'link' ) ); // phpcs:ignore ?>><?php echo esc_html( $s['link_text'] ); ?> <?php echo prime_icon( 'arrow', 16 ); // phpcs:ignore ?></a><?php endif; ?>
			</div>
			<ul class="reel">
				<?php foreach ( (array) prime_v( $s, 'rows', array() ) as $i => $row ) : ?>
					<li>
						<a class="reel__row" <?php echo prime_link_attrs( prime_v( $row, 'link' ) ); // phpcs:ignore ?> data-img="<?php echo esc_url( prime_media_url( prime_v( $row, 'image' ) ) ); ?>">
							<span class="reel__num mono"><?php echo esc_html( prime_nn( $i + 1 ) ); ?></span>
							<span class="reel__title"><?php echo esc_html( prime_v( $row, 'title' ) ); ?></span>
							<span class="reel__arrow"><?php echo prime_icon( 'arrow-ur', 28 ); // phpcs:ignore ?></span>
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
	$styles = array( 'light' => '', 'dark' => 'bento__card--dark', 'blue' => 'bento__card--blue' );
	?>
	<section class="section why prime-sec">
		<div class="container">
			<div class="section-head">
				<div>
					<?php prime_label( prime_v( $s, 'label_num' ), prime_v( $s, 'label' ) ); ?>
					<h2 class="h2" data-split><?php echo prime_h( prime_v( $s, 'title' ) ); // phpcs:ignore ?></h2>
				</div>
				<p class="muted section-head__intro" data-anim="fade-up"><?php echo esc_html( prime_v( $s, 'intro' ) ); ?></p>
			</div>
			<div class="bento" data-anim="stagger">
				<article class="bento__card bento__card--xl" data-spotlight>
					<?php prime_label( 'A', prime_v( $s, 'xl_label' ) ); ?>
					<h3><?php echo esc_html( prime_v( $s, 'xl_title' ) ); ?></h3>
					<div class="funnel" aria-hidden="true">
						<?php
						$n = count( $funnel );
						foreach ( $funnel as $i => $f ) :
							$w = $n > 1 ? 100 - ( 76 * $i / ( $n - 1 ) ) : 100;
							?>
							<div class="funnel__row <?php echo $i === $n - 1 ? 'funnel__row--hl' : ''; ?>" style="--w:<?php echo esc_attr( round( $w ) ); ?>%"><span><?php echo esc_html( $f ); ?></span></div>
						<?php endforeach; ?>
					</div>
				</article>
				<?php foreach ( array_slice( (array) prime_v( $s, 'cards', array() ), 0, 4 ) as $c ) : $st = prime_v( $c, 'style', 'light' ); ?>
					<article class="bento__card <?php echo esc_attr( isset( $styles[ $st ] ) ? $styles[ $st ] : '' ); ?>" data-spotlight>
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

function prime_sec_testimonials( $s ) {
	$items = (array) prime_v( $s, 'items', array() );
	?>
	<section class="section testimonials prime-sec" data-slider>
		<div class="container">
			<div class="testimonials__head">
				<?php prime_label( prime_v( $s, 'label_num' ), prime_v( $s, 'label' ), 'label--light' ); ?>
				<div class="slider-nav">
					<button type="button" class="slider-nav__btn" data-prev aria-label="<?php esc_attr_e( 'Previous', 'prime' ); ?>"><?php echo prime_icon( 'arrow', 18 ); // phpcs:ignore ?></button>
					<button type="button" class="slider-nav__btn" data-next aria-label="<?php esc_attr_e( 'Next', 'prime' ); ?>"><?php echo prime_icon( 'arrow', 18 ); // phpcs:ignore ?></button>
				</div>
			</div>
			<div class="testimonials__stage">
				<?php foreach ( $items as $i => $t ) : ?>
					<figure class="quote <?php echo 0 === $i ? 'is-active' : ''; ?>" data-slide>
						<blockquote><p>“<?php echo esc_html( prime_v( $t, 'quote' ) ); ?>”</p></blockquote>
						<figcaption><span class="quote__av"><?php echo esc_html( mb_substr( prime_v( $t, 'name', 'P' ), 0, 1 ) ); ?></span><span><strong><?php echo esc_html( prime_v( $t, 'name' ) ); ?></strong><small><?php echo esc_html( prime_v( $t, 'role' ) ); ?></small></span></figcaption>
					</figure>
				<?php endforeach; ?>
			</div>
			<div class="slider-progress">
				<?php foreach ( $items as $i => $t ) : ?>
					<button type="button" data-goto="<?php echo esc_attr( $i ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %d: slide */ __( 'Show testimonial %d', 'prime' ), $i + 1 ) ); ?>"><i></i></button>
				<?php endforeach; ?>
			</div>
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
	<section class="section geo prime-sec">
		<div class="container geo__layout">
			<div class="geo__copy">
				<?php prime_label( prime_v( $s, 'label_num' ), prime_v( $s, 'label' ) ); ?>
				<h2 class="h2" data-split><?php echo prime_h( prime_v( $s, 'title' ) ); // phpcs:ignore ?></h2>
				<p class="muted" data-anim="fade-up"><?php echo esc_html( prime_v( $s, 'text' ) ); ?></p>
				<div class="geo__cards" data-anim="stagger">
					<?php foreach ( (array) prime_v( $s, 'cards', array() ) as $c ) : ?>
						<a class="geo-card" <?php echo prime_link_attrs( prime_v( $c, 'link' ) ); // phpcs:ignore ?>>
							<span class="geo-card__pin"><?php echo prime_icon( prime_v( $c, 'icon', 'pin' ), 18 ); // phpcs:ignore ?></span>
							<span><strong><?php echo esc_html( prime_v( $c, 'title' ) ); ?></strong><small><?php echo esc_html( prime_v( $c, 'subtitle' ) ); ?></small></span>
							<?php echo prime_icon( 'arrow-ur', 18 ); // phpcs:ignore ?>
						</a>
					<?php endforeach; ?>
				</div>
			</div>
			<div class="geo__map" data-anim="rise" aria-hidden="true">
				<div class="geo__radar"></div>
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
					<span class="city <?php echo 'yes' === prime_v( $c, 'hub' ) ? 'city--hub' : ''; ?> <?php echo 'yes' === prime_v( $c, 'left' ) ? 'city--left' : ''; ?>" style="left:<?php echo esc_attr( round( $x, 2 ) ); ?>%;top:<?php echo esc_attr( round( $y, 2 ) ); ?>%;--d:<?php echo esc_attr( $i * 0.18 ); ?>s"><i></i><b><?php echo esc_html( prime_v( $c, 'name' ) ); ?></b></span>
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
	$bg = 'paper' === prime_v( $s, 'background' ) ? 'jobs-section--paper' : '';
	?>
	<section class="section jobs-section prime-sec <?php echo esc_attr( $bg ); ?>" id="<?php echo esc_attr( prime_v( $s, 'anchor', 'jobs' ) ); ?>">
		<div class="container">
			<?php if ( prime_v( $s, 'title' ) ) : ?>
				<div class="section-head">
					<div>
						<?php prime_label( prime_v( $s, 'label_num' ), prime_v( $s, 'label' ) ); ?>
						<h2 class="h2" data-split><?php echo prime_h( $s['title'] ); // phpcs:ignore ?></h2>
					</div>
					<?php if ( prime_v( $s, 'link_text' ) ) : ?><a class="link-arrow" <?php echo prime_link_attrs( prime_v( $s, 'link' ) ); // phpcs:ignore ?>><?php echo esc_html( $s['link_text'] ); ?> <?php echo prime_icon( 'arrow', 16 ); // phpcs:ignore ?></a><?php endif; ?>
				</div>
			<?php endif; ?>
			<div class="jobs" data-jobs>
				<?php if ( count( $cats ) > 1 ) : ?>
					<div class="filter-tabs" role="tablist" aria-label="<?php esc_attr_e( 'Filter roles', 'prime' ); ?>">
						<span class="filter-tabs__pill" aria-hidden="true"></span>
						<button type="button" class="is-active" data-filter="all"><?php echo esc_html( prime_v( $s, 'all_label', __( 'All roles', 'prime' ) ) ); ?></button>
						<?php foreach ( $cats as $c ) : ?><button type="button" data-filter="<?php echo esc_attr( sanitize_title( $c ) ); ?>"><?php echo esc_html( $c ); ?></button><?php endforeach; ?>
					</div>
				<?php endif; ?>
				<div class="jobs__grid">
					<?php foreach ( $jobs as $i => $j ) : ?>
						<article class="job-card" data-cat="<?php echo esc_attr( sanitize_title( prime_v( $j, 'category' ) ) ); ?>">
							<div class="job-card__top"><span class="tag"><?php echo esc_html( prime_v( $j, 'category' ) ); ?></span><span class="mono job-card__id"><?php echo esc_html( prime_v( $j, 'ref', '#PR-' . ( 101 + $i ) ) ); ?></span></div>
							<h3><?php echo esc_html( prime_v( $j, 'title' ) ); ?></h3>
							<p><?php echo esc_html( prime_v( $j, 'summary' ) ); ?></p>
							<ul class="job-card__meta">
								<li><?php echo prime_icon( 'pin', 15 ); // phpcs:ignore ?><?php echo esc_html( prime_v( $j, 'location' ) ); ?></li>
								<li><?php echo prime_icon( 'clock', 15 ); // phpcs:ignore ?><?php echo esc_html( prime_v( $j, 'type' ) ); ?></li>
								<li><?php echo prime_icon( 'briefcase', 15 ); // phpcs:ignore ?><?php echo esc_html( prime_v( $j, 'experience' ) ); ?></li>
							</ul>
							<a class="job-card__apply" <?php echo prime_link_attrs( prime_v( $j, 'link', prime_link( prime_url( 'job-seekers' ) . '#enquiry' ) ) ); // phpcs:ignore ?> aria-label="<?php echo esc_attr( sprintf( /* translators: %s: job */ __( 'Apply for %s', 'prime' ), prime_v( $j, 'title' ) ) ); ?>"><?php esc_html_e( 'Apply now', 'prime' ); ?> <?php echo prime_icon( 'arrow-ur', 16 ); // phpcs:ignore ?></a>
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
				<?php prime_label( prime_v( $s, 'label_num', 'FAQ' ), prime_v( $s, 'label', __( 'Questions, answered', 'prime' ) ) ); ?>
				<h2 class="h2" data-split><?php echo prime_h( prime_v( $s, 'title', 'Frequently asked *questions*' ) ); // phpcs:ignore ?></h2>
				<p class="muted" data-anim="fade-up"><?php echo esc_html( prime_v( $s, 'intro' ) ); ?></p>
				<?php if ( prime_v( $s, 'link_text' ) ) : ?><a class="link-arrow" <?php echo prime_link_attrs( prime_v( $s, 'link' ) ); // phpcs:ignore ?> data-anim="fade-up"><?php echo esc_html( $s['link_text'] ); ?> <?php echo prime_icon( 'arrow', 16 ); // phpcs:ignore ?></a><?php endif; ?>
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
	<section class="cta-form prime-sec" id="enquiry">
		<div class="cta-form__bg" aria-hidden="true"><span class="aurora a1"></span><span class="aurora a2"></span><span class="aurora a3"></span><span class="cta-form__grid"></span></div>
		<div class="container cta-form__layout">
			<div class="cta-form__copy">
				<?php prime_label( prime_v( $s, 'label_num', 'Contact' ), prime_v( $s, 'label', __( 'Start a conversation', 'prime' ) ), 'label--light' ); ?>
				<h2 class="h2" data-split><?php echo prime_h( prime_v( $s, 'title', 'Tell us who you need. *We’ll take it from there.*' ) ); // phpcs:ignore ?></h2>
				<p class="lead" data-anim="fade-up"><?php echo esc_html( prime_v( $s, 'lead' ) ); ?></p>
				<?php if ( $points ) : ?>
					<ul class="cta-form__points" data-anim="stagger">
						<?php foreach ( $points as $pt ) : ?><li><?php echo prime_icon( 'check', 18 ); // phpcs:ignore ?><?php echo esc_html( $pt ); ?></li><?php endforeach; ?>
					</ul>
				<?php endif; ?>
				<?php if ( 'yes' === prime_v( $s, 'show_contacts', 'yes' ) ) : ?>
					<div class="cta-form__contact" data-anim="fade-up">
						<a href="mailto:<?php echo esc_attr( prime_opt( 'contact_email' ) ); ?>"><?php echo prime_icon( 'mail', 18 ); // phpcs:ignore ?><span><small><?php esc_html_e( 'Email', 'prime' ); ?></small><?php echo esc_html( prime_opt( 'contact_email' ) ); ?></span></a>
						<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', prime_opt( 'contact_phone' ) ) ); ?>"><?php echo prime_icon( 'phone', 18 ); // phpcs:ignore ?><span><small><?php esc_html_e( 'Call', 'prime' ); ?></small><?php echo esc_html( prime_opt( 'contact_phone' ) ); ?></span></a>
					</div>
				<?php endif; ?>
			</div>
			<div class="cta-form__card" data-anim="rise">
				<div class="cta-form__card-head"><span class="dots" aria-hidden="true"><i></i><i></i><i></i></span><span class="mono">enquiry.submit()</span></div>
				<?php prime_form( prime_v( $s, 'preset', 'employer' ), prime_v( $s, 'form_shortcode' ) ); ?>
			</div>
		</div>
	</section>
	<?php
}

/* ==========================================================================
   INNER PAGE SECTIONS
   ========================================================================== */

function prime_sec_banner( $s ) {
	$variant  = prime_v( $s, 'variant', 'mesh' );
	$post_id  = get_the_ID();
	$crumbs   = array();
	if ( $post_id ) {
		foreach ( array_reverse( get_post_ancestors( $post_id ) ) as $anc ) {
			$crumbs[] = array( get_the_title( $anc ), get_permalink( $anc ) );
		}
	}
	$current = prime_v( $s, 'crumb', $post_id ? get_the_title( $post_id ) : '' );
	$chips   = prime_lines( prime_v( $s, 'chips' ) );
	?>
	<section class="banner banner--<?php echo esc_attr( $variant ); ?> prime-sec">
		<div class="banner__bg" aria-hidden="true">
			<?php if ( 'mesh' === $variant ) : ?>
				<span class="aurora a1"></span><span class="aurora a2"></span><span class="aurora a3"></span><span class="banner__noise"></span>
			<?php elseif ( 'grid' === $variant ) : ?>
				<span class="banner__perspective"></span><span class="banner__scan"></span><span class="aurora a2"></span>
			<?php elseif ( 'orbit' === $variant ) : ?>
				<span class="orbit-ring r1"><i></i></span><span class="orbit-ring r2"><i></i></span><span class="orbit-ring r3"><i></i></span><span class="aurora a1"></span>
			<?php elseif ( 'beam' === $variant ) : ?>
				<span class="beam b1"></span><span class="beam b2"></span><span class="beam b3"></span><span class="banner__grid"></span>
			<?php else : ?>
				<svg class="waves" viewBox="0 0 1440 320" preserveAspectRatio="none"><path class="w1" d="M0 200 C 240 120 480 280 720 200 S 1200 120 1440 200 V320 H0Z"/><path class="w2" d="M0 230 C 240 170 480 300 720 230 S 1200 160 1440 230 V320 H0Z"/><path class="w3" d="M0 260 C 240 220 480 310 720 260 S 1200 210 1440 260 V320 H0Z"/></svg><span class="aurora a1"></span>
			<?php endif; ?>
			<span class="banner__vignette"></span>
		</div>
		<div class="container banner__layout <?php echo 'yes' === prime_v( $s, 'show_answer', 'yes' ) ? '' : 'banner__layout--solo'; ?>">
			<div class="banner__copy">
				<?php if ( 'yes' === prime_v( $s, 'show_crumbs', 'yes' ) ) : ?>
					<nav class="crumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'prime' ); ?>" data-anim="fade-up">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'prime' ); ?></a><span>/</span>
						<?php foreach ( $crumbs as $c ) : ?><a href="<?php echo esc_url( $c[1] ); ?>"><?php echo esc_html( $c[0] ); ?></a><span>/</span><?php endforeach; ?>
						<span aria-current="page"><?php echo esc_html( $current ); ?></span>
					</nav>
				<?php endif; ?>
				<?php if ( prime_v( $s, 'eyebrow' ) ) : ?><p class="pill" data-anim="fade-up"><span class="pulse-dot"></span><?php echo esc_html( $s['eyebrow'] ); ?></p><?php endif; ?>
				<h1 class="banner__title" data-split><?php echo prime_h( prime_v( $s, 'title', $current ) ); // phpcs:ignore ?></h1>
				<?php if ( prime_v( $s, 'lead' ) ) : ?><p class="banner__lead" data-anim="fade-up"><?php echo esc_html( $s['lead'] ); ?></p><?php endif; ?>
				<?php if ( prime_v( $s, 'btn1_text' ) || prime_v( $s, 'btn2_text' ) ) : ?>
					<div class="banner__actions" data-anim="fade-up">
						<?php if ( prime_v( $s, 'btn1_text' ) ) : ?><a class="btn btn--primary btn--lg" <?php echo prime_link_attrs( prime_v( $s, 'btn1_link', '#enquiry' ) ); // phpcs:ignore ?> data-magnetic><span><?php echo esc_html( $s['btn1_text'] ); ?></span><?php echo prime_icon( 'arrow', 18 ); // phpcs:ignore ?></a><?php endif; ?>
						<?php if ( prime_v( $s, 'btn2_text' ) ) : ?><a class="btn btn--ghost btn--lg" <?php echo prime_link_attrs( prime_v( $s, 'btn2_link', '#faq' ) ); // phpcs:ignore ?> data-magnetic><span><?php echo esc_html( $s['btn2_text'] ); ?></span></a><?php endif; ?>
					</div>
				<?php endif; ?>
				<?php if ( $chips ) : ?>
					<ul class="banner__chips" data-anim="stagger">
						<?php foreach ( $chips as $chip ) : ?><li><?php echo prime_icon( 'check', 15 ); // phpcs:ignore ?><?php echo esc_html( $chip ); ?></li><?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
			<?php if ( 'yes' === prime_v( $s, 'show_answer', 'yes' ) && prime_v( $s, 'answer_text' ) ) : ?>
				<aside class="answer-card" data-anim="rise" data-tilt>
					<div class="answer-card__head"><span class="dots" aria-hidden="true"><i></i><i></i><i></i></span><span class="mono">quick-answer</span></div>
					<p class="answer-card__q"><?php echo prime_icon( 'search', 16 ); // phpcs:ignore ?><span><?php echo esc_html( prime_v( $s, 'answer_q' ) ); ?></span></p>
					<p class="answer-card__a"><?php echo esc_html( $s['answer_text'] ); ?></p>
					<div class="answer-card__foot mono"><span><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span><span><?php echo esc_html( sprintf( /* translators: %s: date */ __( 'Updated %s', 'prime' ), get_the_modified_date( 'M Y' ) ? get_the_modified_date( 'M Y' ) : gmdate( 'M Y' ) ) ); ?></span></div>
				</aside>
			<?php endif; ?>
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
					<<?php echo $tag; // phpcs:ignore ?> class="contact-card" <?php echo $href ? prime_link_attrs( $c['link'] ) : ''; // phpcs:ignore ?> data-spotlight>
						<?php echo prime_icon( prime_v( $c, 'icon', 'mail' ), 26 ); // phpcs:ignore ?>
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
				<?php prime_label( prime_v( $s, 'label_num' ), prime_v( $s, 'label' ) ); ?>
				<h2 class="h2" data-split><?php echo prime_h( prime_v( $s, 'title' ) ); // phpcs:ignore ?></h2>
				<div class="intro__text" data-anim="stagger"><?php echo wp_kses_post( wpautop( prime_v( $s, 'content' ) ) ); ?></div>
			</div>
			<?php if ( $rows || $toc ) : ?>
				<aside class="glance" data-anim="rise">
					<?php if ( $rows ) : ?>
						<p class="mono glance__title">// <?php echo esc_html( prime_v( $s, 'glance_title', __( 'at a glance', 'prime' ) ) ); ?></p>
						<dl><?php foreach ( $rows as $r ) : ?><div><dt><?php echo esc_html( prime_v( $r, 'label' ) ); ?></dt><dd><?php echo esc_html( prime_v( $r, 'value' ) ); ?></dd></div><?php endforeach; ?></dl>
					<?php endif; ?>
					<?php if ( $toc ) : ?>
						<p class="mono glance__title">// <?php esc_html_e( 'on this page', 'prime' ); ?></p>
						<ul class="glance__toc"><?php foreach ( $toc as $t ) : ?><li><a href="#<?php echo esc_attr( ltrim( prime_v( $t, 'anchor' ), '#' ) ); ?>"><?php echo esc_html( prime_v( $t, 'text' ) ); ?></a></li><?php endforeach; ?></ul>
					<?php endif; ?>
				</aside>
			<?php endif; ?>
		</div>
	</section>
	<?php
}

function prime_sec_hub_grid( $s ) {
	?>
	<section class="section hub prime-sec" id="<?php echo esc_attr( prime_v( $s, 'anchor', 'services-list' ) ); ?>">
		<div class="container">
			<div class="section-head"><div>
				<?php prime_label( prime_v( $s, 'label_num' ), prime_v( $s, 'label' ) ); ?>
				<h2 class="h2" data-split><?php echo prime_h( prime_v( $s, 'title' ) ); // phpcs:ignore ?></h2>
			</div></div>
			<div class="hub__grid" data-anim="stagger">
				<?php foreach ( (array) prime_v( $s, 'cards', array() ) as $n => $c ) : ?>
					<a class="hub-card" <?php echo prime_link_attrs( prime_v( $c, 'link' ) ); // phpcs:ignore ?> data-spotlight>
						<span class="hub-card__top"><span class="svc-card__icon"><?php echo prime_icon( prime_v( $c, 'icon', 'layers' ), 24 ); // phpcs:ignore ?></span><span class="mono"><?php echo esc_html( prime_nn( $n + 1 ) ); ?></span></span>
						<h3><?php echo esc_html( prime_v( $c, 'title' ) ); ?></h3>
						<p><?php echo esc_html( prime_v( $c, 'text' ) ); ?></p>
						<?php if ( prime_v( $c, 'keyword' ) ) : ?><span class="hub-card__kw mono"><?php echo esc_html( $c['keyword'] ); ?></span><?php endif; ?>
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
	<section class="section features prime-sec" id="<?php echo esc_attr( prime_v( $s, 'anchor', 'features' ) ); ?>">
		<div class="container">
			<div class="section-head">
				<div>
					<?php prime_label( prime_v( $s, 'label_num' ), prime_v( $s, 'label' ), 'label--light' ); ?>
					<h2 class="h2" data-split><?php echo prime_h( prime_v( $s, 'title' ) ); // phpcs:ignore ?></h2>
				</div>
				<?php if ( prime_v( $s, 'intro' ) ) : ?><p class="section-head__intro" data-anim="fade-up"><?php echo esc_html( $s['intro'] ); ?></p><?php endif; ?>
			</div>
			<div class="feature-grid <?php echo count( $items ) > 6 ? 'feature-grid--dense' : ''; ?>" data-anim="stagger">
				<?php foreach ( $items as $i => $f ) : ?>
					<article class="feature" data-spotlight>
						<span class="feature__num mono"><?php echo esc_html( prime_nn( $i + 1 ) ); ?></span>
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
		<div class="container roles__head"><?php prime_label( prime_v( $s, 'label_num' ), prime_v( $s, 'label' ) ); ?></div>
		<div class="roles__marquee" data-marquee="-1">
			<div class="kinetic__track">
				<?php for ( $r = 0; $r < 2; $r++ ) : foreach ( $roles as $role ) : ?>
					<span class="role-chip"><?php echo prime_icon( 'arrow-ur', 16 ); // phpcs:ignore ?><?php echo esc_html( $role ); ?></span>
				<?php endforeach; endfor; ?>
			</div>
		</div>
	</section>
	<?php
}

function prime_sec_timeline( $s ) {
	?>
	<section class="section timeline prime-sec" id="<?php echo esc_attr( prime_v( $s, 'anchor', 'process' ) ); ?>">
		<div class="container">
			<div class="section-head"><div>
				<?php prime_label( prime_v( $s, 'label_num' ), prime_v( $s, 'label' ) ); ?>
				<h2 class="h2" data-split><?php echo prime_h( prime_v( $s, 'title' ) ); // phpcs:ignore ?></h2>
			</div></div>
			<ol class="timeline__track" data-timeline>
				<span class="timeline__line" aria-hidden="true"><i></i></span>
				<?php foreach ( (array) prime_v( $s, 'steps', array() ) as $i => $st ) : ?>
					<li class="timeline__step">
						<span class="timeline__dot mono"><?php echo esc_html( prime_nn( $i + 1 ) ); ?></span>
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
	<section class="section facts prime-sec" id="<?php echo esc_attr( prime_v( $s, 'anchor', 'facts' ) ); ?>">
		<div class="container facts__layout">
			<?php if ( $facts ) : ?>
				<div class="facts__sheet" data-anim="rise">
					<div class="facts__sheet-head"><span class="dots" aria-hidden="true"><i></i><i></i><i></i></span><span class="mono">key-facts.json</span></div>
					<?php prime_label( prime_v( $s, 'label_num' ), prime_v( $s, 'label', __( 'Key facts', 'prime' ) ) ); ?>
					<h2 class="h3"><?php echo esc_html( prime_v( $s, 'title' ) ); ?></h2>
					<ul class="facts__list" data-anim="stagger">
						<?php foreach ( $facts as $i => $fact ) : ?><li><span class="mono">fact_<?php echo esc_html( prime_nn( $i + 1 ) ); ?></span><p><?php echo esc_html( $fact ); ?></p></li><?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>
			<div class="facts__areas">
				<?php prime_label( prime_v( $s, 'areas_label_num' ), prime_v( $s, 'areas_label', __( 'Areas served', 'prime' ) ) ); ?>
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
	<section class="section related prime-sec">
		<div class="container">
			<?php prime_label( prime_v( $s, 'label_num' ), prime_v( $s, 'label', __( 'Keep exploring', 'prime' ) ) ); ?>
			<div class="related__grid" data-anim="stagger">
				<?php foreach ( (array) prime_v( $s, 'cards', array() ) as $c ) : ?>
					<a class="related-card" <?php echo prime_link_attrs( prime_v( $c, 'link' ) ); // phpcs:ignore ?>>
						<span class="related-card__icon"><?php echo prime_icon( prime_v( $c, 'icon', 'layers' ), 22 ); // phpcs:ignore ?></span>
						<span class="mono"><?php echo esc_html( prime_v( $c, 'kicker' ) ); ?></span>
						<strong><?php echo esc_html( prime_v( $c, 'title' ) ); ?></strong>
						<span class="related-card__arrow"><?php echo prime_icon( 'arrow-ur', 22 ); // phpcs:ignore ?></span>
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
						<span class="gift-art__glow"></span>
						<svg class="gift-art__box" viewBox="0 0 240 240">
							<defs>
								<linearGradient id="gaGold" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#f1d27a"/><stop offset="1" stop-color="#b8901c"/></linearGradient>
								<linearGradient id="gaNavy" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#fbf7ee"/><stop offset="1" stop-color="#ddd2b9"/></linearGradient>
							</defs>
							<rect x="44" y="104" width="152" height="112" rx="10" fill="url(#gaNavy)"/>
							<rect x="34" y="78" width="172" height="36" rx="9" fill="#f1ead9"/>
							<rect x="108" y="78" width="24" height="138" fill="url(#gaGold)"/>
							<path d="M120 78c-10-26-46-38-52-18-5 16 26 20 52 18Z" fill="none" stroke="url(#gaGold)" stroke-width="10" stroke-linejoin="round"/>
							<path d="M120 78c10-26 46-38 52-18 5 16-26 20-52 18Z" fill="none" stroke="url(#gaGold)" stroke-width="10" stroke-linejoin="round"/>
							<circle cx="120" cy="78" r="10" fill="#f1d27a"/>
						</svg>
						<span class="gift-art__chip gift-art__chip--1"><?php echo prime_icon( 'palette', 16 ); // phpcs:ignore ?><span><strong><?php esc_html_e( 'Your logo', 'prime' ); ?></strong><small><?php esc_html_e( 'on every gift', 'prime' ); ?></small></span></span>
						<span class="gift-art__chip gift-art__chip--2"><?php echo prime_icon( 'truck', 16 ); // phpcs:ignore ?><span><strong><?php esc_html_e( 'Pan-India', 'prime' ); ?></strong><small><?php esc_html_e( 'delivery', 'prime' ); ?></small></span></span>
						<svg class="gift-art__stamp" viewBox="0 0 120 120"><defs><path id="gaCircle" d="M60 60m-46 0a46 46 0 1 1 92 0a46 46 0 1 1-92 0"/></defs><text><textPath href="#gaCircle">CUSTOMISED • BULK ORDERS • PAN-INDIA • </textPath></text></svg>
					</div>
				<?php endif; ?>
			</div>
			<div class="gifting__copy">
				<?php prime_label( prime_v( $s, 'label_num' ), prime_v( $s, 'label', __( 'Corporate gifting', 'prime' ) ) ); ?>
				<h2 class="h2" data-split><?php echo prime_h( prime_v( $s, 'title' ) ); // phpcs:ignore ?></h2>
				<div class="gifting__text" data-anim="fade-up"><?php foreach ( $paras as $p ) : ?><p><?php echo esc_html( $p ); ?></p><?php endforeach; ?></div>
				<?php if ( $items ) : ?>
					<h3 class="gifting__subtitle"><?php echo esc_html( prime_v( $s, 'list_title', __( 'Popular corporate gifts in India include:', 'prime' ) ) ); ?></h3>
					<ul class="gift-list" data-anim="stagger"><?php foreach ( $items as $it ) : ?><li><?php echo prime_icon( 'check', 16 ); // phpcs:ignore ?><span><?php echo esc_html( $it ); ?></span></li><?php endforeach; ?></ul>
				<?php endif; ?>
				<?php if ( $ideal ) : ?>
					<div class="gifting__ideal" data-anim="fade-up"><span class="mono"><?php echo esc_html( prime_v( $s, 'ideal_title', __( 'Ideal for', 'prime' ) ) ); ?></span><ul><?php foreach ( $ideal as $i ) : ?><li><?php echo esc_html( $i ); ?></li><?php endforeach; ?></ul></div>
				<?php endif; ?>
				<?php if ( $badges ) : ?>
					<ul class="gift-badges" data-anim="stagger"><?php foreach ( $badges as $k => $b ) : ?><li><?php echo prime_icon( $icons[ $k % 4 ], 18 ); // phpcs:ignore ?><?php echo esc_html( $b ); ?></li><?php endforeach; ?></ul>
				<?php endif; ?>
				<div class="gifting__actions" data-anim="fade-up">
					<?php if ( prime_v( $s, 'btn_text' ) ) : ?><a class="btn btn--dark btn--lg" <?php echo prime_link_attrs( prime_v( $s, 'btn_link', '#enquiry' ) ); // phpcs:ignore ?> data-modal="enquiry" data-preset="gifting" data-magnetic><span><?php echo esc_html( $s['btn_text'] ); ?></span><?php echo prime_icon( 'gift', 18 ); // phpcs:ignore ?></a><?php endif; ?>
					<?php if ( prime_v( $s, 'btn2_text' ) ) : ?><a class="link-arrow" <?php echo prime_link_attrs( prime_v( $s, 'btn2_link' ) ); // phpcs:ignore ?>><?php echo esc_html( $s['btn2_text'] ); ?> <?php echo prime_icon( 'arrow', 16 ); // phpcs:ignore ?></a><?php endif; ?>
				</div>
			</div>
		</div>
	</section>
	<?php
}

/**
 * The business card, recreated in HTML (also used inside the enquiry popup).
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
	<div class="bcard" data-tilt>
		<div class="bcard__brand">
			<img src="<?php echo esc_url( PRIME_URI . '/assets/img/logo-mark.png' ); ?>" width="96" height="88" alt="" loading="lazy" decoding="async">
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
	<section class="section bcard-section prime-sec" id="<?php echo esc_attr( prime_v( $s, 'anchor', 'business-card' ) ); ?>">
		<div class="container bcard-section__layout">
			<div class="bcard-section__copy">
				<?php prime_label( prime_v( $s, 'label_num' ), prime_v( $s, 'label', __( 'Talk to us directly', 'prime' ) ) ); ?>
				<h2 class="h2" data-split><?php echo prime_h( prime_v( $s, 'title', 'Your direct line to *Prime*' ) ); // phpcs:ignore ?></h2>
				<p class="muted" data-anim="fade-up"><?php echo esc_html( prime_v( $s, 'text' ) ); ?></p>
				<div class="bcard-actions" data-anim="stagger">
					<?php if ( prime_tel_url() ) : ?><a class="btn btn--dark" href="<?php echo esc_attr( prime_tel_url() ); ?>"><?php echo prime_icon( 'phone', 18 ); // phpcs:ignore ?><span><?php esc_html_e( 'Call now', 'prime' ); ?></span></a><?php endif; ?>
					<?php if ( prime_whatsapp_url() ) : ?><a class="btn btn--whatsapp" href="<?php echo esc_url( prime_whatsapp_url() ); ?>" target="_blank" rel="noopener"><?php echo prime_icon( 'whatsapp', 18 ); // phpcs:ignore ?><span>WhatsApp</span></a><?php endif; ?>
					<a class="btn btn--outline" href="mailto:<?php echo esc_attr( prime_opt( 'contact_email' ) ); ?>"><?php echo prime_icon( 'mail', 18 ); // phpcs:ignore ?><span><?php esc_html_e( 'Email', 'prime' ); ?></span></a>
					<a class="btn btn--outline" href="<?php echo esc_url( home_url( '/prime-contact.vcf' ) ); ?>" download><?php echo prime_icon( 'download', 18 ); // phpcs:ignore ?><span><?php esc_html_e( 'Save contact', 'prime' ); ?></span></a>
				</div>
				<?php if ( prime_socials() ) : ?><div class="bcard-social" data-anim="fade-up"><span class="mono"><?php esc_html_e( 'Follow us', 'prime' ); ?></span><div class="footer-social"><?php echo prime_socials(); // phpcs:ignore ?></div></div><?php endif; ?>
			</div>
			<div class="bcard-section__card" data-anim="rise"><?php echo prime_business_card_html( $s ); // phpcs:ignore ?></div>
		</div>
	</section>
	<?php
}
