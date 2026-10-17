<?php
/**
 * Filterable job cards.
 *
 * @package Prime
 * @var array $args { limit }
 */

defined( 'ABSPATH' ) || exit;
$jobs  = prime_jobs();
$limit = isset( $args['limit'] ) ? (int) $args['limit'] : 0;
if ( $limit ) {
	$jobs = array_slice( $jobs, 0, $limit );
}
$cats = array_values( array_unique( wp_list_pluck( $jobs, 1 ) ) );
?>
<div class="jobs" data-jobs>
	<div class="filter-tabs" role="tablist" aria-label="<?php esc_attr_e( 'Filter roles', 'prime' ); ?>">
		<span class="filter-tabs__pill" aria-hidden="true"></span>
		<button type="button" class="is-active" data-filter="all"><?php esc_html_e( 'All roles', 'prime' ); ?></button>
		<?php foreach ( $cats as $c ) : ?>
			<button type="button" data-filter="<?php echo esc_attr( sanitize_title( $c ) ); ?>"><?php echo esc_html( $c ); ?></button>
		<?php endforeach; ?>
	</div>
	<div class="jobs__grid">
		<?php foreach ( $jobs as $i => $j ) : ?>
			<article class="job-card" data-cat="<?php echo esc_attr( sanitize_title( $j[1] ) ); ?>">
				<div class="job-card__top">
					<span class="tag"><?php echo esc_html( $j[1] ); ?></span>
					<span class="mono job-card__id">#PR-<?php echo esc_html( str_pad( (string) ( 101 + $i ), 4, '0', STR_PAD_LEFT ) ); ?></span>
				</div>
				<h3><?php echo esc_html( $j[0] ); ?></h3>
				<p><?php echo esc_html( $j[5] ); ?></p>
				<ul class="job-card__meta">
					<li><?php echo prime_icon( 'pin', 15 ); // phpcs:ignore ?><?php echo esc_html( $j[2] ); ?></li>
					<li><?php echo prime_icon( 'clock', 15 ); // phpcs:ignore ?><?php echo esc_html( $j[3] ); ?></li>
					<li><?php echo prime_icon( 'briefcase', 15 ); // phpcs:ignore ?><?php echo esc_html( $j[4] ); ?></li>
				</ul>
				<a class="job-card__apply" href="<?php echo esc_url( prime_url( 'job-seekers' ) ); ?>#enquiry" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: job */ __( 'Apply for %s', 'prime' ), $j[0] ) ); ?>"><?php esc_html_e( 'Apply now', 'prime' ); ?> <?php echo prime_icon( 'arrow-ur', 16 ); // phpcs:ignore ?></a>
			</article>
		<?php endforeach; ?>
	</div>
</div>
