<?php
/**
 * Homepage.
 *
 * @package Prime
 */

defined( 'ABSPATH' ) || exit;
get_header();

$services = prime_pages_in( 'service' );
$industry_images = array(
	array( 'Technology', 'it', 'photo-1518770660439-4636190af475' ),
	array( 'BFSI & Fintech', 'industries', 'photo-1554224155-6726b3ff858f' ),
	array( 'Healthcare & Pharma', 'healthcare', 'photo-1576091160550-2173dba999ef' ),
	array( 'Manufacturing', 'industries', 'photo-1565043666747-69f6646db940' ),
	array( 'Retail & E-commerce', 'industries', 'photo-1441986300917-64674bd600d8' ),
	array( 'Logistics', 'industries', 'photo-1586528116311-ad8dd3c8310d' ),
	array( 'Overseas & Gulf', 'overseas', 'photo-1512453979798-5ea266f8880c' ),
);
// Approximate lon/lat → % positions inside the India map frame (lon 67–93, lat 6–35).
$cities = array(
	array( 'Delhi NCR', 77.21, 28.61, 'delhi' ),
	array( 'Mumbai', 72.88, 19.07, 'mumbai', 'l' ),
	array( 'Bangalore', 77.59, 12.97, 'bangalore', 'l' ),
	array( 'Pune', 73.86, 18.52, '' ),
	array( 'Hyderabad', 78.49, 17.39, '' ),
	array( 'Chennai', 80.27, 13.08, '' ),
	array( 'Kolkata', 88.36, 22.57, '' ),
	array( 'Ahmedabad', 72.57, 23.02, '', 'l' ),
	array( 'Jaipur', 75.79, 26.91, '' ),
	array( 'Lucknow', 80.95, 26.85, '' ),
	array( 'Kochi', 76.27, 9.93, '' ),
	array( 'Chandigarh', 76.78, 30.73, '' ),
	array( 'Indore', 75.86, 22.72, '' ),
	array( 'Bhubaneswar', 85.82, 20.30, '' ),
);
?>

<!-- ============ 01 HERO ============ -->
<section class="hero" data-hero>
	<canvas class="hero__canvas" aria-hidden="true"></canvas>
	<div class="hero__bg" aria-hidden="true">
		<span class="hero__grid"></span>
		<span class="aurora a1"></span><span class="aurora a2"></span>
		<span class="hero__spot"></span>
	</div>

	<div class="container hero__layout">
		<div class="hero__copy">
			<p class="pill" data-anim="fade-up"><span class="pulse-dot"></span><?php esc_html_e( 'Now hiring across India & overseas', 'prime' ); ?></p>
			<h1 class="hero__title">
				<span class="hero__line" data-split><?php esc_html_e( 'Recruitment agency', 'prime' ); ?></span>
				<span class="hero__line" data-split><?php esc_html_e( 'in India for teams', 'prime' ); ?></span>
				<span class="hero__line" data-anim="fade-up"><?php esc_html_e( 'that', 'prime' ); ?> <span class="rotator" data-rotator="<?php echo esc_attr( 'scale.,build.,lead.,grow.,win.' ); ?>"><em class="accent">scale.</em></span></span>
			</h1>
			<p class="hero__lead" data-anim="fade-up"><?php esc_html_e( 'Prime Recruitments finds, interviews and shortlists the people your business needs — permanent, contract, executive and volume hiring, delivered by specialists who answer the phone.', 'prime' ); ?></p>
			<div class="hero__actions" data-anim="fade-up">
				<a class="btn btn--primary btn--lg" href="<?php echo esc_url( prime_url( 'employers' ) ); ?>" data-magnetic><span><?php esc_html_e( 'Hire talent', 'prime' ); ?></span><?php echo prime_icon( 'arrow', 18 ); // phpcs:ignore ?></a>
				<a class="btn btn--ghost btn--lg" href="<?php echo esc_url( prime_url( 'jobs' ) ); ?>" data-magnetic><span><?php esc_html_e( 'Explore jobs', 'prime' ); ?></span><?php echo prime_icon( 'arrow-ur', 18 ); // phpcs:ignore ?></a>
			</div>
			<div class="hero__trust" data-anim="fade-up">
				<span class="avatars" aria-hidden="true"><i>AK</i><i>RS</i><i>NP</i><i>+</i></span>
				<span><strong><?php esc_html_e( 'Recruiter-screened shortlists', 'prime' ); ?></strong><small><?php esc_html_e( 'Typically within 48–72 hours of your brief', 'prime' ); ?></small></span>
			</div>
		</div>

		<div class="hero__visual" data-anim="rise" data-tilt>
			<div class="console">
				<div class="console__bar">
					<span class="dots" aria-hidden="true"><i></i><i></i><i></i></span>
					<span class="mono">prime/match-engine.ts</span>
					<span class="console__status"><span class="pulse-dot"></span>live</span>
				</div>
				<pre class="console__code mono" aria-hidden="true"><code data-typer><span class="c-k">const</span> role = {
  title: <span class="c-s">"Senior Backend Engineer"</span>,
  city:  <span class="c-s">"Bangalore"</span>,
  exp:   <span class="c-s">"5–8 yrs"</span>
};
<span class="c-k">await</span> prime.<span class="c-f">search</span>(role);   <span class="c-c">// map market</span>
<span class="c-k">await</span> prime.<span class="c-f">interview</span>();     <span class="c-c">// human screen</span>
<span class="c-k">return</span> prime.<span class="c-f">shortlist</span>(<span class="c-n">4</span>); <span class="c-ok">✓</span></code></pre>
				<div class="console__results">
					<div class="match"><span class="match__av">A</span><span class="match__info"><strong>Candidate A</strong><small>Java · Kafka · AWS</small></span><span class="match__bar"><i style="--w:96%"></i></span><b>96%</b></div>
					<div class="match"><span class="match__av">R</span><span class="match__info"><strong>Candidate R</strong><small>Go · Postgres · GCP</small></span><span class="match__bar"><i style="--w:91%"></i></span><b>91%</b></div>
					<div class="match"><span class="match__av">S</span><span class="match__info"><strong>Candidate S</strong><small>Node · Redis · K8s</small></span><span class="match__bar"><i style="--w:88%"></i></span><b>88%</b></div>
				</div>
			</div>
			<div class="float-chip float-chip--1"><?php echo prime_icon( 'zap', 16 ); // phpcs:ignore ?><span><strong>48–72h</strong><small><?php esc_html_e( 'first shortlist', 'prime' ); ?></small></span></div>
			<div class="float-chip float-chip--2"><?php echo prime_icon( 'shield', 16 ); // phpcs:ignore ?><span><strong><?php esc_html_e( 'Interviewed', 'prime' ); ?></strong><small><?php esc_html_e( 'every candidate', 'prime' ); ?></small></span></div>
			<div class="float-chip float-chip--3"><?php echo prime_icon( 'globe', 16 ); // phpcs:ignore ?><span><strong><?php esc_html_e( 'Pan-India', 'prime' ); ?></strong><small><?php esc_html_e( '+ overseas', 'prime' ); ?></small></span></div>
		</div>
	</div>

	<div class="hero__scroll" aria-hidden="true"><span class="mono"><?php esc_html_e( 'scroll', 'prime' ); ?></span><i></i></div>
</section>

<!-- ============ 02 KINETIC MARQUEE ============ -->
<section class="kinetic" aria-label="<?php esc_attr_e( 'What we do', 'prime' ); ?>">
	<div class="kinetic__row" data-marquee="-1">
		<div class="kinetic__track">
			<?php for ( $r = 0; $r < 2; $r++ ) : foreach ( $services as $p ) : ?>
				<span><?php echo esc_html( $p['menu'] ); ?></span><i>✦</i>
			<?php endforeach; endfor; ?>
		</div>
	</div>
	<div class="kinetic__row kinetic__row--outline" data-marquee="1">
		<div class="kinetic__track">
			<?php for ( $r = 0; $r < 2; $r++ ) : foreach ( array( 'Mumbai', 'Delhi NCR', 'Bangalore', 'Pune', 'Hyderabad', 'Chennai', 'Kolkata', 'Dubai' ) as $c ) : ?>
				<span><?php echo esc_html( $c ); ?></span><i>●</i>
			<?php endforeach; endfor; ?>
		</div>
	</div>
</section>

<!-- ============ 03 STATEMENT (scroll-scrubbed text) ============ -->
<section class="section statement">
	<div class="container">
		<?php prime_label( '01', __( 'Our approach', 'prime' ) ); ?>
		<p class="statement__text" data-scrub-text><?php esc_html_e( 'We don’t send CVs. We send people worth meeting — interviewed, skills-checked and salary-aligned — so your managers spend their time hiring, not sifting.', 'prime' ); ?></p>
		<div class="statement__foot">
			<div class="statement__sig" data-anim="fade-up">
				<span class="brand-slot__mark" aria-hidden="true">P</span>
				<span><strong><?php esc_html_e( 'The Prime promise', 'prime' ); ?></strong><small><?php esc_html_e( 'Quality over volume, on every brief', 'prime' ); ?></small></span>
			</div>
			<a class="link-arrow" href="<?php echo esc_url( prime_url( 'about' ) ); ?>" data-anim="fade-up"><?php esc_html_e( 'About Prime', 'prime' ); ?> <?php echo prime_icon( 'arrow', 16 ); // phpcs:ignore ?></a>
		</div>
	</div>
</section>

<!-- ============ 04 SERVICES (pinned horizontal scroll) ============ -->
<section class="hscroll" data-hscroll>
	<div class="hscroll__pin">
		<div class="container hscroll__head">
			<div>
				<?php prime_label( '02', __( 'Recruitment services', 'prime' ), 'label--light' ); ?>
				<h2 class="h2" data-split><?php echo wp_kses_post( prime_heading_html( 'Eight ways to hire. *One accountable partner.*' ) ); ?></h2>
			</div>
			<div class="hscroll__meta">
				<span class="mono hscroll__count"><b data-hcount>01</b> / <?php echo esc_html( str_pad( (string) count( $services ), 2, '0', STR_PAD_LEFT ) ); ?></span>
				<span class="hscroll__bar"><i data-hbar></i></span>
			</div>
		</div>
		<div class="hscroll__viewport">
			<div class="hscroll__track" data-htrack>
				<?php $n = 0; foreach ( $services as $key => $p ) : $n++; ?>
					<a class="svc-card" href="<?php echo esc_url( prime_url( $key ) ); ?>" data-spotlight>
						<span class="svc-card__num mono"><?php echo esc_html( str_pad( (string) $n, 2, '0', STR_PAD_LEFT ) ); ?></span>
						<span class="svc-card__icon"><?php echo prime_icon( $p['icon'], 26 ); // phpcs:ignore ?></span>
						<h3><?php echo esc_html( $p['menu'] ); ?></h3>
						<p><?php echo esc_html( $p['short'] ); ?></p>
						<ul>
							<?php foreach ( array_slice( $p['features'], 0, 3 ) as $f ) : ?>
								<li><?php echo esc_html( $f[1] ); ?></li>
							<?php endforeach; ?>
						</ul>
						<span class="svc-card__link"><?php esc_html_e( 'Explore', 'prime' ); ?> <?php echo prime_icon( 'arrow', 16 ); // phpcs:ignore ?></span>
					</a>
				<?php endforeach; ?>
				<a class="svc-card svc-card--cta" href="<?php echo esc_url( prime_url( 'services' ) ); ?>">
					<span class="mono">// all services</span>
					<h3><?php esc_html_e( 'Not sure which model fits?', 'prime' ); ?></h3>
					<p><?php esc_html_e( 'Tell us the role. We’ll recommend the fastest, most cost-effective route.', 'prime' ); ?></p>
					<span class="svc-card__link"><?php esc_html_e( 'Compare services', 'prime' ); ?> <?php echo prime_icon( 'arrow', 16 ); // phpcs:ignore ?></span>
				</a>
			</div>
		</div>
	</div>
</section>

<!-- ============ 05 STATS (counters + rings) ============ -->
<section class="section stats">
	<div class="container">
		<div class="stats__grid">
			<?php for ( $i = 1; $i <= 4; $i++ ) : ?>
				<div class="stat" data-anim="fade-up">
					<svg class="stat__ring" viewBox="0 0 120 120" aria-hidden="true"><circle cx="60" cy="60" r="54"/><circle class="stat__ring-fill" cx="60" cy="60" r="54" style="--p:<?php echo esc_attr( 0.35 + $i * 0.15 ); ?>"/></svg>
					<p class="stat__num"><span data-count="<?php echo esc_attr( prime_opt( "stat_{$i}_num" ) ); ?>">0</span><em><?php echo esc_html( prime_opt( "stat_{$i}_suffix" ) ); ?></em></p>
					<p class="stat__label"><?php echo esc_html( prime_opt( "stat_{$i}_label" ) ); ?></p>
				</div>
			<?php endfor; ?>
		</div>
	</div>
</section>

<!-- ============ 06 PROCESS (sticky + line draw) ============ -->
<section class="section process" data-process>
	<div class="container process__layout">
		<div class="process__head">
			<?php prime_label( '03', __( 'How it works', 'prime' ) ); ?>
			<h2 class="h2" data-split><?php echo wp_kses_post( prime_heading_html( 'From brief to *signed offer* in four steps' ) ); ?></h2>
			<p class="muted" data-anim="fade-up"><?php esc_html_e( 'A repeatable process that keeps hiring managers informed and candidates engaged at every stage.', 'prime' ); ?></p>
			<a class="btn btn--dark" href="<?php echo esc_url( prime_url( 'employers' ) ); ?>" data-anim="fade-up" data-magnetic><span><?php esc_html_e( 'Share a role', 'prime' ); ?></span><?php echo prime_icon( 'arrow', 18 ); // phpcs:ignore ?></a>
		</div>
		<ol class="process__steps">
			<span class="process__line" aria-hidden="true"><i data-process-line></i></span>
			<?php foreach ( prime_default_steps() as $i => $s ) : ?>
				<li class="process__step" data-step>
					<span class="process__dot mono"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
					<div>
						<h3><?php echo esc_html( $s[0] ); ?></h3>
						<p><?php echo esc_html( $s[1] ); ?></p>
					</div>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>

<!-- ============ 07 INDUSTRIES (hover image reveal) ============ -->
<section class="section industries-reel" data-reveal-list>
	<div class="container">
		<div class="section-head">
			<div>
				<?php prime_label( '04', __( 'Industries', 'prime' ), 'label--light' ); ?>
				<h2 class="h2" data-split><?php echo wp_kses_post( prime_heading_html( 'Specialists who *speak your industry*' ) ); ?></h2>
			</div>
			<a class="link-arrow link-arrow--light" href="<?php echo esc_url( prime_url( 'industries' ) ); ?>"><?php esc_html_e( 'All industries', 'prime' ); ?> <?php echo prime_icon( 'arrow', 16 ); // phpcs:ignore ?></a>
		</div>
		<ul class="reel">
			<?php foreach ( $industry_images as $i => $row ) : ?>
				<li>
					<a class="reel__row" href="<?php echo esc_url( prime_url( $row[1] ) ); ?>" data-img="<?php echo esc_url( 'https://images.unsplash.com/' . $row[2] . '?auto=format&fit=crop&w=720&q=80' ); ?>">
						<span class="reel__num mono"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
						<span class="reel__title"><?php echo esc_html( $row[0] ); ?></span>
						<span class="reel__arrow"><?php echo prime_icon( 'arrow-ur', 28 ); // phpcs:ignore ?></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<div class="reel__preview" aria-hidden="true"><img alt="" src="data:image/gif;base64,R0lGODlhAQABAAAAACw=" loading="lazy"></div>
</section>

<!-- ============ 08 WHY PRIME (spotlight bento) ============ -->
<section class="section why">
	<div class="container">
		<div class="section-head">
			<div>
				<?php prime_label( '05', __( 'Why Prime', 'prime' ) ); ?>
				<h2 class="h2" data-split><?php echo wp_kses_post( prime_heading_html( 'Built for hiring that *actually works*' ) ); ?></h2>
			</div>
			<p class="muted section-head__intro" data-anim="fade-up"><?php esc_html_e( 'Recruitment is a trust business. Here is what we do differently to earn it.', 'prime' ); ?></p>
		</div>
		<div class="bento" data-anim="stagger">
			<article class="bento__card bento__card--xl" data-spotlight>
				<?php prime_label( 'A', __( 'Precision shortlists', 'prime' ) ); ?>
				<h3><?php esc_html_e( '3–5 interviewed candidates per role. Not 50 unread CVs.', 'prime' ); ?></h3>
				<div class="funnel" aria-hidden="true">
					<div class="funnel__row" style="--w:100%"><span><?php esc_html_e( 'Market mapped', 'prime' ); ?></span></div>
					<div class="funnel__row" style="--w:72%"><span><?php esc_html_e( 'Approached', 'prime' ); ?></span></div>
					<div class="funnel__row" style="--w:46%"><span><?php esc_html_e( 'Interviewed', 'prime' ); ?></span></div>
					<div class="funnel__row funnel__row--hl" style="--w:24%"><span><?php esc_html_e( 'Shortlist', 'prime' ); ?></span></div>
				</div>
			</article>
			<article class="bento__card" data-spotlight>
				<span class="bento__icon"><?php echo prime_icon( 'chart', 24 ); // phpcs:ignore ?></span>
				<h3><?php esc_html_e( 'Salary intelligence', 'prime' ); ?></h3>
				<p><?php esc_html_e( 'Live benchmarks with every brief, so offers land first time.', 'prime' ); ?></p>
			</article>
			<article class="bento__card bento__card--dark" data-spotlight>
				<span class="bento__icon"><?php echo prime_icon( 'shield', 24 ); // phpcs:ignore ?></span>
				<h3><?php esc_html_e( 'Replacement guarantee', 'prime' ); ?></h3>
				<p><?php esc_html_e( 'If a permanent hire leaves early, we replace them free.', 'prime' ); ?></p>
			</article>
			<article class="bento__card" data-spotlight>
				<span class="bento__icon"><?php echo prime_icon( 'users', 24 ); // phpcs:ignore ?></span>
				<h3><?php esc_html_e( 'Function specialists', 'prime' ); ?></h3>
				<p><?php esc_html_e( 'Tech recruiters for tech. Finance recruiters for finance.', 'prime' ); ?></p>
			</article>
			<article class="bento__card bento__card--blue" data-spotlight>
				<span class="bento__icon"><?php echo prime_icon( 'heart', 24 ); // phpcs:ignore ?></span>
				<h3><?php esc_html_e( 'Candidates trust us', 'prime' ); ?></h3>
				<p><?php esc_html_e( 'Honest feedback and zero fees — so top people take our calls.', 'prime' ); ?></p>
			</article>
		</div>
	</div>
</section>

<!-- ============ 09 TESTIMONIALS (slider) ============ -->
<section class="section testimonials" data-slider>
	<div class="container">
		<div class="testimonials__head">
			<?php prime_label( '06', __( 'In their words', 'prime' ), 'label--light' ); ?>
			<div class="slider-nav">
				<button type="button" class="slider-nav__btn" data-prev aria-label="<?php esc_attr_e( 'Previous', 'prime' ); ?>"><?php echo prime_icon( 'arrow', 18 ); // phpcs:ignore ?></button>
				<button type="button" class="slider-nav__btn" data-next aria-label="<?php esc_attr_e( 'Next', 'prime' ); ?>"><?php echo prime_icon( 'arrow', 18 ); // phpcs:ignore ?></button>
			</div>
		</div>
		<div class="testimonials__stage">
			<?php foreach ( prime_testimonials() as $i => $t ) : ?>
				<figure class="quote <?php echo 0 === $i ? 'is-active' : ''; ?>" data-slide>
					<blockquote><p>“<?php echo esc_html( $t[0] ); ?>”</p></blockquote>
					<figcaption><span class="quote__av"><?php echo esc_html( substr( $t[1], 0, 1 ) ); ?></span><span><strong><?php echo esc_html( $t[1] ); ?></strong><small><?php echo esc_html( $t[2] ); ?></small></span></figcaption>
				</figure>
			<?php endforeach; ?>
		</div>
		<div class="slider-progress">
			<?php foreach ( prime_testimonials() as $i => $t ) : ?>
				<button type="button" data-goto="<?php echo esc_attr( $i ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %d: slide */ __( 'Show testimonial %d', 'prime' ), $i + 1 ) ); ?>"><i></i></button>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<!-- ============ 10 GEO / LOCATIONS (city constellation) ============ -->
<section class="section geo">
	<div class="container geo__layout">
		<div class="geo__copy">
			<?php prime_label( '07', __( 'Where we hire', 'prime' ) ); ?>
			<h2 class="h2" data-split><?php echo wp_kses_post( prime_heading_html( 'Recruiting across India, *placing worldwide*' ) ); ?></h2>
			<p class="muted" data-anim="fade-up"><?php esc_html_e( 'Local consultants in India’s largest hiring markets, pan-India delivery for tier-2 cities and remote roles, and overseas placements across the Gulf, UK and Europe.', 'prime' ); ?></p>
			<div class="geo__cards" data-anim="stagger">
				<?php foreach ( prime_pages_in( 'location' ) as $key => $p ) : ?>
					<a class="geo-card" href="<?php echo esc_url( prime_url( $key ) ); ?>">
						<span class="geo-card__pin"><?php echo prime_icon( 'pin', 18 ); // phpcs:ignore ?></span>
						<span><strong><?php echo esc_html( sprintf( /* translators: %s city */ __( 'Recruitment agency in %s', 'prime' ), $p['menu'] ) ); ?></strong><small><?php echo esc_html( implode( ' · ', array_slice( $p['cities'], 0, 3 ) ) ); ?></small></span>
						<?php echo prime_icon( 'arrow-ur', 18 ); // phpcs:ignore ?>
					</a>
				<?php endforeach; ?>
				<a class="geo-card" href="<?php echo esc_url( prime_url( 'overseas' ) ); ?>">
					<span class="geo-card__pin"><?php echo prime_icon( 'globe', 18 ); // phpcs:ignore ?></span>
					<span><strong><?php esc_html_e( 'Overseas recruitment', 'prime' ); ?></strong><small>UAE · Saudi · Qatar · UK</small></span>
					<?php echo prime_icon( 'arrow-ur', 18 ); // phpcs:ignore ?>
				</a>
			</div>
		</div>
		<div class="geo__map" data-anim="rise" aria-hidden="true">
			<div class="geo__radar"></div>
			<svg class="geo__lines" viewBox="0 0 100 100" preserveAspectRatio="none">
				<?php
				$hub = $cities[0];
				$pos = function ( $c ) {
					return array( ( $c[1] - 67 ) / 26 * 100, ( 35 - $c[2] ) / 29 * 100 );
				};
				list( $hx, $hy ) = $pos( $hub );
				foreach ( array_slice( $cities, 1 ) as $c ) {
					list( $x, $y ) = $pos( $c );
					printf( '<path d="M%1$.2f %2$.2f Q %5$.2f %6$.2f %3$.2f %4$.2f" />', $hx, $hy, $x, $y, ( $hx + $x ) / 2 + 6, ( $hy + $y ) / 2 - 6 );
				}
				?>
			</svg>
			<?php foreach ( $cities as $i => $c ) : list( $x, $y ) = $pos( $c ); ?>
				<span class="city <?php echo $c[3] ? 'city--hub' : ''; ?> <?php echo isset( $c[4] ) ? 'city--left' : ''; ?>" style="left:<?php echo esc_attr( round( $x, 2 ) ); ?>%;top:<?php echo esc_attr( round( $y, 2 ) ); ?>%;--d:<?php echo esc_attr( $i * 0.18 ); ?>s"><i></i><b><?php echo esc_html( $c[0] ); ?></b></span>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<!-- ============ 11 JOBS ============ -->
<section class="section jobs-section">
	<div class="container">
		<div class="section-head">
			<div>
				<?php prime_label( '08', __( 'Featured roles', 'prime' ) ); ?>
				<h2 class="h2" data-split><?php echo wp_kses_post( prime_heading_html( 'Latest job vacancies *worth a look*' ) ); ?></h2>
			</div>
			<a class="link-arrow" href="<?php echo esc_url( prime_url( 'jobs' ) ); ?>"><?php esc_html_e( 'View all jobs', 'prime' ); ?> <?php echo prime_icon( 'arrow', 16 ); // phpcs:ignore ?></a>
		</div>
		<?php get_template_part( 'template-parts/jobs-list', null, array( 'limit' => 6 ) ); ?>
	</div>
</section>

<!-- ============ 12 FAQ (AEO) ============ -->
<?php
get_template_part(
	'template-parts/faq',
	null,
	array(
		'faqs'  => prime_home_faqs(),
		'num'   => '09',
		'title' => 'Recruitment questions, *answered*',
	)
);
?>

<!-- ============ 13 CTA + FORM ============ -->
<?php get_template_part( 'template-parts/cta-form' ); ?>

<?php
get_footer();
