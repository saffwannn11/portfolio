<?php
/**
 * Page template. Landing pages defined in inc/pages-data.php get the full
 * designed layout; any other page gets the banner + editor content.
 *
 * @package Prime
 */

defined( 'ABSPATH' ) || exit;
get_header();

$page = prime_current_page();

if ( ! $page ) :
	while ( have_posts() ) :
		the_post();
		?>
		<section class="banner banner--mesh banner--compact">
			<div class="banner__bg" aria-hidden="true"><span class="aurora a1"></span><span class="aurora a2"></span><span class="banner__grid"></span></div>
			<div class="container banner__inner">
				<nav class="crumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'prime' ); ?>"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'prime' ); ?></a><span>/</span><span aria-current="page"><?php the_title(); ?></span></nav>
				<h1 class="banner__title" data-split><?php the_title(); ?></h1>
			</div>
		</section>
		<section class="section"><div class="container prose"><?php the_content(); ?></div></section>
		<?php
	endwhile;
	get_template_part( 'template-parts/cta-form' );
	get_footer();
	return;
endif;

$type     = $page['type'];
$cities   = isset( $page['cities'] ) ? $page['cities'] : prime_default_cities();
$steps    = isset( $page['steps'] ) ? $page['steps'] : prime_default_steps();
$parent   = ! empty( $page['parent'] ) ? prime_page( $page['parent'] ) : null;
$preset   = in_array( $page['key'], array( 'jobs', 'job-seekers' ), true ) ? 'candidate' : 'employer';
$sec      = 0;
$next_num = function () use ( &$sec ) {
	$sec++;
	return str_pad( (string) $sec, 2, '0', STR_PAD_LEFT );
};
?>

<!-- ============ BANNER (<?php echo esc_html( $page['banner'] ); ?>) ============ -->
<section class="banner banner--<?php echo esc_attr( $page['banner'] ); ?>">
	<div class="banner__bg" aria-hidden="true">
		<?php if ( 'mesh' === $page['banner'] ) : ?>
			<span class="aurora a1"></span><span class="aurora a2"></span><span class="aurora a3"></span><span class="banner__noise"></span>
		<?php elseif ( 'grid' === $page['banner'] ) : ?>
			<span class="banner__perspective"></span><span class="banner__scan"></span><span class="aurora a2"></span>
		<?php elseif ( 'orbit' === $page['banner'] ) : ?>
			<span class="orbit-ring r1"><i></i></span><span class="orbit-ring r2"><i></i></span><span class="orbit-ring r3"><i></i></span><span class="aurora a1"></span>
		<?php elseif ( 'beam' === $page['banner'] ) : ?>
			<span class="beam b1"></span><span class="beam b2"></span><span class="beam b3"></span><span class="banner__grid"></span>
		<?php else : ?>
			<svg class="waves" viewBox="0 0 1440 320" preserveAspectRatio="none"><path class="w1" d="M0 200 C 240 120 480 280 720 200 S 1200 120 1440 200 V320 H0Z"/><path class="w2" d="M0 230 C 240 170 480 300 720 230 S 1200 160 1440 230 V320 H0Z"/><path class="w3" d="M0 260 C 240 220 480 310 720 260 S 1200 210 1440 260 V320 H0Z"/></svg><span class="aurora a1"></span>
		<?php endif; ?>
		<span class="banner__vignette"></span>
	</div>

	<div class="container banner__layout">
		<div class="banner__copy">
			<nav class="crumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'prime' ); ?>" data-anim="fade-up">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'prime' ); ?></a><span>/</span>
				<?php if ( $parent ) : ?>
					<a href="<?php echo esc_url( prime_url( $page['parent'] ) ); ?>"><?php echo esc_html( $parent['menu'] ); ?></a><span>/</span>
				<?php endif; ?>
				<span aria-current="page"><?php echo esc_html( $page['menu'] ); ?></span>
			</nav>
			<p class="pill" data-anim="fade-up"><span class="pulse-dot"></span><?php echo esc_html( $page['eyebrow'] ); ?></p>
			<h1 class="banner__title" data-split><?php echo wp_kses_post( prime_heading_html( $page['h1'] ) ); ?></h1>
			<p class="banner__lead" data-anim="fade-up"><?php echo esc_html( $page['lead'] ); ?></p>
			<div class="banner__actions" data-anim="fade-up">
				<a class="btn btn--primary btn--lg" href="#enquiry" data-magnetic><span><?php echo 'candidate' === $preset ? esc_html__( 'Submit your CV', 'prime' ) : esc_html__( 'Get a shortlist', 'prime' ); ?></span><?php echo prime_icon( 'arrow', 18 ); // phpcs:ignore ?></a>
				<a class="btn btn--ghost btn--lg" href="#faq" data-magnetic><span><?php esc_html_e( 'Read FAQs', 'prime' ); ?></span></a>
			</div>
			<ul class="banner__chips" data-anim="stagger">
				<?php foreach ( $page['chips'] as $chip ) : ?>
					<li><?php echo prime_icon( 'check', 15 ); // phpcs:ignore ?><?php echo esc_html( $chip ); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>

		<aside class="answer-card" data-anim="rise" data-tilt>
			<div class="answer-card__head">
				<span class="dots" aria-hidden="true"><i></i><i></i><i></i></span>
				<span class="mono"><?php esc_html_e( 'quick-answer', 'prime' ); ?></span>
			</div>
			<p class="answer-card__q"><?php echo prime_icon( 'search', 16 ); // phpcs:ignore ?><span><?php echo esc_html( ucfirst( $page['focus_kw'] ) ); ?>?</span></p>
			<p class="answer-card__a"><?php echo esc_html( $page['answer'] ); ?></p>
			<div class="answer-card__foot mono">
				<span><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span>
				<span><?php echo esc_html( sprintf( /* translators: %s: date */ __( 'Updated %s', 'prime' ), get_the_modified_date( 'M Y' ) ) ); ?></span>
			</div>
		</aside>
	</div>
</section>

<?php if ( 'contact' === $type ) : ?>
	<!-- ============ CONTACT ============ -->
	<section class="section contact-cards">
		<div class="container">
			<div class="contact-cards__grid" data-anim="stagger">
				<a class="contact-card" href="mailto:<?php echo esc_attr( prime_opt( 'contact_email' ) ); ?>" data-spotlight><?php echo prime_icon( 'mail', 26 ); // phpcs:ignore ?><small><?php esc_html_e( 'Email us', 'prime' ); ?></small><strong><?php echo esc_html( prime_opt( 'contact_email' ) ); ?></strong></a>
				<a class="contact-card" href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', prime_opt( 'contact_phone' ) ) ); ?>" data-spotlight><?php echo prime_icon( 'phone', 26 ); // phpcs:ignore ?><small><?php esc_html_e( 'Call us', 'prime' ); ?></small><strong><?php echo esc_html( prime_opt( 'contact_phone' ) ); ?></strong></a>
				<a class="contact-card" href="mailto:<?php echo esc_attr( prime_opt( 'careers_email' ) ); ?>" data-spotlight><?php echo prime_icon( 'file', 26 ); // phpcs:ignore ?><small><?php esc_html_e( 'Send your CV', 'prime' ); ?></small><strong><?php echo esc_html( prime_opt( 'careers_email' ) ); ?></strong></a>
				<div class="contact-card" data-spotlight><?php echo prime_icon( 'pin', 26 ); // phpcs:ignore ?><small><?php echo esc_html( prime_opt( 'contact_hours' ) ); ?></small><strong><?php echo esc_html( prime_opt( 'contact_address' ) ); ?></strong></div>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php if ( ! empty( $page['intro'] ) ) : ?>
	<!-- ============ INTRO + AT A GLANCE ============ -->
	<section class="section intro">
		<div class="container intro__layout">
			<div class="intro__main">
				<?php prime_label( $next_num(), __( 'Overview', 'prime' ) ); ?>
				<h2 class="h2" data-split><?php echo wp_kses_post( prime_heading_html( $page['intro_h'] ) ); ?></h2>
				<div class="intro__text" data-anim="stagger">
					<?php foreach ( $page['intro'] as $para ) : ?>
						<p><?php echo esc_html( $para ); ?></p>
					<?php endforeach; ?>
				</div>
				<?php
				// Anything typed in the page editor is shown here too.
				while ( have_posts() ) :
					the_post();
					if ( trim( get_the_content() ) ) {
						echo '<div class="prose intro__extra">';
						the_content();
						echo '</div>';
					}
				endwhile;
				?>
			</div>
			<aside class="glance" data-anim="rise">
				<p class="mono glance__title">// <?php esc_html_e( 'at a glance', 'prime' ); ?></p>
				<dl>
					<div><dt><?php esc_html_e( 'Service', 'prime' ); ?></dt><dd><?php echo esc_html( $page['menu'] ); ?></dd></div>
					<div><dt><?php esc_html_e( 'Best for', 'prime' ); ?></dt><dd><?php echo esc_html( 'job-seekers' === $page['key'] || 'jobs' === $page['key'] ? __( 'Job seekers', 'prime' ) : __( 'Employers & HR teams', 'prime' ) ); ?></dd></div>
					<div><dt><?php esc_html_e( 'Coverage', 'prime' ); ?></dt><dd><?php echo esc_html( 'location' === $type ? $page['city'] : ( 'overseas' === $page['key'] ? __( 'India → Gulf, UK, Europe', 'prime' ) : __( 'Pan-India + remote', 'prime' ) ) ); ?></dd></div>
					<div><dt><?php esc_html_e( 'Response', 'prime' ); ?></dt><dd><?php esc_html_e( 'Within 1 working day', 'prime' ); ?></dd></div>
				</dl>
				<p class="mono glance__title">// <?php esc_html_e( 'on this page', 'prime' ); ?></p>
				<ul class="glance__toc">
					<?php if ( ! empty( $page['features'] ) || 'hub' === $type ) : ?><li><a href="#features"><?php echo esc_html( wp_strip_all_tags( str_replace( '*', '', $page['features_h'] ) ) ); ?></a></li><?php endif; ?>
					<?php if ( in_array( $type, array( 'service', 'location' ), true ) ) : ?><li><a href="#process"><?php esc_html_e( 'Our process', 'prime' ); ?></a></li><?php endif; ?>
					<li><a href="#facts"><?php esc_html_e( 'Key facts', 'prime' ); ?></a></li>
					<li><a href="#faq"><?php esc_html_e( 'FAQs', 'prime' ); ?></a></li>
					<li><a href="#enquiry"><?php esc_html_e( 'Get in touch', 'prime' ); ?></a></li>
				</ul>
			</aside>
		</div>
	</section>
<?php endif; ?>

<?php if ( 'hub' === $type ) : ?>
	<!-- ============ SERVICES HUB ============ -->
	<section class="section hub" id="features">
		<div class="container">
			<div class="section-head">
				<div>
					<?php prime_label( $next_num(), __( 'Service lines', 'prime' ) ); ?>
					<h2 class="h2" data-split><?php echo wp_kses_post( prime_heading_html( $page['features_h'] ) ); ?></h2>
				</div>
			</div>
			<div class="hub__grid" data-anim="stagger">
				<?php $n = 0; foreach ( prime_pages_in( 'service' ) as $key => $s ) : $n++; ?>
					<a class="hub-card" href="<?php echo esc_url( prime_url( $key ) ); ?>" data-spotlight>
						<span class="hub-card__top"><span class="svc-card__icon"><?php echo prime_icon( $s['icon'], 24 ); // phpcs:ignore ?></span><span class="mono"><?php echo esc_html( str_pad( (string) $n, 2, '0', STR_PAD_LEFT ) ); ?></span></span>
						<h3><?php echo esc_html( $s['menu'] ); ?></h3>
						<p><?php echo esc_html( $s['short'] ); ?></p>
						<span class="hub-card__kw mono"><?php echo esc_html( $s['focus_kw'] ); ?></span>
						<span class="svc-card__link"><?php esc_html_e( 'Learn more', 'prime' ); ?> <?php echo prime_icon( 'arrow', 16 ); // phpcs:ignore ?></span>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
<?php elseif ( 'jobs' === $type ) : ?>
	<!-- ============ JOBS ============ -->
	<section class="section jobs-section" id="openings">
		<div class="container">
			<div class="section-head">
				<div>
					<?php prime_label( $next_num(), __( 'Current openings', 'prime' ) ); ?>
					<h2 class="h2" data-split><?php echo wp_kses_post( prime_heading_html( 'Open roles *hiring now*' ) ); ?></h2>
				</div>
			</div>
			<?php get_template_part( 'template-parts/jobs-list' ); ?>
		</div>
	</section>
<?php endif; ?>

<?php if ( ! empty( $page['features'] ) ) : ?>
	<!-- ============ FEATURES ============ -->
	<section class="section features features--<?php echo esc_attr( $type ); ?>" <?php echo 'hub' === $type ? '' : 'id="features"'; ?>>
		<div class="container">
			<div class="section-head">
				<div>
					<?php prime_label( $next_num(), 'industries' === $type ? __( 'Sectors', 'prime' ) : __( 'Capabilities', 'prime' ), 'label--light' ); ?>
					<h2 class="h2" data-split><?php echo wp_kses_post( prime_heading_html( $page['features_h'] ) ); ?></h2>
				</div>
				<p class="section-head__intro" data-anim="fade-up"><?php echo esc_html( $page['meta_desc'] ); ?></p>
			</div>
			<div class="feature-grid <?php echo count( $page['features'] ) > 6 ? 'feature-grid--dense' : ''; ?>" data-anim="stagger">
				<?php foreach ( $page['features'] as $i => $f ) : ?>
					<article class="feature" data-spotlight>
						<span class="feature__num mono"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
						<span class="feature__icon"><?php echo prime_icon( $f[0], 24 ); // phpcs:ignore ?></span>
						<h3><?php echo esc_html( $f[1] ); ?></h3>
						<p><?php echo esc_html( $f[2] ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php if ( ! empty( $page['roles'] ) ) : ?>
	<!-- ============ ROLES WE HIRE ============ -->
	<section class="roles" aria-label="<?php esc_attr_e( 'Roles we hire', 'prime' ); ?>">
		<div class="container roles__head">
			<?php prime_label( $next_num(), __( 'Roles we hire', 'prime' ) ); ?>
		</div>
		<div class="roles__marquee" data-marquee="-1">
			<div class="kinetic__track">
				<?php for ( $r = 0; $r < 2; $r++ ) : foreach ( $page['roles'] as $role ) : ?>
					<span class="role-chip"><?php echo prime_icon( 'arrow-ur', 16 ); // phpcs:ignore ?><?php echo esc_html( $role ); ?></span>
				<?php endforeach; endfor; ?>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php if ( in_array( $type, array( 'service', 'location' ), true ) ) : ?>
	<!-- ============ PROCESS (horizontal timeline) ============ -->
	<section class="section timeline" id="process">
		<div class="container">
			<div class="section-head">
				<div>
					<?php prime_label( $next_num(), __( 'Process', 'prime' ) ); ?>
					<h2 class="h2" data-split><?php echo wp_kses_post( prime_heading_html( 'location' === $type ? 'How we hire *in ' . $page['city'] . '*' : 'How we deliver *' . $page['menu'] . '*' ) ); ?></h2>
				</div>
			</div>
			<ol class="timeline__track" data-timeline>
				<span class="timeline__line" aria-hidden="true"><i></i></span>
				<?php foreach ( $steps as $i => $s ) : ?>
					<li class="timeline__step">
						<span class="timeline__dot mono"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
						<h3><?php echo esc_html( $s[0] ); ?></h3>
						<p><?php echo esc_html( $s[1] ); ?></p>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</section>
<?php endif; ?>

<!-- ============ KEY FACTS (GEO) + AREAS SERVED ============ -->
<section class="section facts" id="facts">
	<div class="container facts__layout">
		<div class="facts__sheet" data-anim="rise">
			<div class="facts__sheet-head">
				<span class="dots" aria-hidden="true"><i></i><i></i><i></i></span>
				<span class="mono">key-facts.json</span>
			</div>
			<?php prime_label( $next_num(), __( 'Key facts', 'prime' ) ); ?>
			<h2 class="h3"><?php echo esc_html( sprintf( /* translators: %s: topic */ __( '%s: the essentials', 'prime' ), $page['menu'] ) ); ?></h2>
			<ul class="facts__list" data-anim="stagger">
				<?php foreach ( $page['facts'] as $i => $fact ) : ?>
					<li><span class="mono">fact_<?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span><p><?php echo esc_html( $fact ); ?></p></li>
				<?php endforeach; ?>
			</ul>
		</div>
		<div class="facts__areas">
			<?php prime_label( $next_num(), 'overseas' === $page['key'] ? __( 'Countries we recruit for', 'prime' ) : __( 'Areas served', 'prime' ) ); ?>
			<h2 class="h3" data-split><?php echo wp_kses_post( prime_heading_html( 'location' === $type ? 'Covering every corner of *' . $page['city'] . '*' : ( 'overseas' === $page['key'] ? 'Placing Indian talent *worldwide*' : 'Hiring *across India*' ) ) ); ?></h2>
			<ul class="area-chips" data-anim="stagger">
				<?php foreach ( $cities as $c ) : ?>
					<li><?php echo prime_icon( 'pin', 14 ); // phpcs:ignore ?><?php echo esc_html( $c ); ?></li>
				<?php endforeach; ?>
			</ul>
			<?php if ( 'location' !== $type ) : ?>
				<div class="area-links" data-anim="fade-up">
					<?php foreach ( prime_pages_in( 'location' ) as $key => $l ) : ?>
						<a class="link-arrow" href="<?php echo esc_url( prime_url( $key ) ); ?>"><?php echo esc_html( $l['focus_kw'] ); ?> <?php echo prime_icon( 'arrow', 14 ); // phpcs:ignore ?></a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>

<?php
get_template_part(
	'template-parts/faq',
	null,
	array(
		'faqs'  => $page['faqs'],
		'num'   => $next_num(),
		'title' => ucfirst( $page['focus_kw'] ) . ': *FAQs*',
	)
);
?>

<?php if ( ! empty( $page['related'] ) ) : ?>
	<!-- ============ RELATED ============ -->
	<section class="section related">
		<div class="container">
			<?php prime_label( $next_num(), __( 'Keep exploring', 'prime' ) ); ?>
			<div class="related__grid" data-anim="stagger">
				<?php foreach ( $page['related'] as $rk ) : $r = prime_page( $rk ); if ( ! $r ) { continue; } ?>
					<a class="related-card" href="<?php echo esc_url( prime_url( $rk ) ); ?>">
						<span class="related-card__icon"><?php echo prime_icon( $r['icon'], 22 ); // phpcs:ignore ?></span>
						<span class="mono"><?php echo esc_html( $r['eyebrow'] ); ?></span>
						<strong><?php echo esc_html( wp_strip_all_tags( str_replace( '*', '', $r['h1'] ) ) ); ?></strong>
						<span class="related-card__arrow"><?php echo prime_icon( 'arrow-ur', 22 ); // phpcs:ignore ?></span>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php
get_template_part(
	'template-parts/cta-form',
	null,
	array(
		'preset' => $preset,
		'title'  => 'candidate' === $preset ? 'Your next role *starts here.*' : 'Ready to hire? *Let’s talk.*',
	)
);

get_footer();
