<?php
/**
 * Fallback template: blog index, archives, search and single posts.
 *
 * @package Prime
 */

defined( 'ABSPATH' ) || exit;
get_header();
?>
<section class="banner banner--mesh banner--compact">
	<div class="banner__bg" aria-hidden="true"><span class="aurora a1"></span><span class="aurora a2"></span><span class="banner__grid"></span></div>
	<div class="container banner__inner">
		<nav class="crumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'prime' ); ?>"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'prime' ); ?></a><span>/</span><span aria-current="page"><?php echo esc_html( is_singular() ? get_the_title() : wp_strip_all_tags( get_the_archive_title() ) ); ?></span></nav>
		<h1 class="banner__title" data-split><?php echo esc_html( is_singular() ? get_the_title() : ( is_home() ? __( 'Insights', 'prime' ) : wp_strip_all_tags( get_the_archive_title() ) ) ); ?></h1>
	</div>
</section>

<section class="section">
	<div class="container">
		<?php if ( is_singular() ) : ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<article <?php post_class( 'prose' ); ?>>
					<?php if ( has_post_thumbnail() ) : ?><figure class="prose__media"><?php the_post_thumbnail( 'large' ); ?></figure><?php endif; ?>
					<p class="mono muted"><?php echo esc_html( get_the_date() ); ?></p>
					<?php the_content(); ?>
				</article>
			<?php endwhile; ?>
		<?php elseif ( have_posts() ) : ?>
			<div class="post-grid" data-anim="stagger">
				<?php while ( have_posts() ) : the_post(); ?>
					<a <?php post_class( 'post-card' ); ?> href="<?php the_permalink(); ?>">
						<?php if ( has_post_thumbnail() ) : ?><span class="post-card__media"><?php the_post_thumbnail( 'medium_large' ); ?></span><?php endif; ?>
						<span class="mono"><?php echo esc_html( get_the_date() ); ?></span>
						<h2><?php the_title(); ?></h2>
						<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22 ) ); ?></p>
					</a>
				<?php endwhile; ?>
			</div>
			<div class="pagination"><?php the_posts_pagination(); ?></div>
		<?php else : ?>
			<p class="muted"><?php esc_html_e( 'Nothing here yet.', 'prime' ); ?></p>
		<?php endif; ?>
	</div>
</section>
<?php
get_template_part( 'template-parts/cta-form' );
get_footer();
