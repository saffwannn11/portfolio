<?php
/**
 * Enquiry form.
 *
 * Uses Fluent Forms when a form ID is set in Customizer → Prime Recruitments →
 * Contact form. Otherwise falls back to a built-in form that emails the
 * enquiry with wp_mail(), so the CTA works from the moment the theme is live.
 *
 * @package Prime
 */

defined( 'ABSPATH' ) || exit;

/**
 * Print the enquiry form.
 *
 * @param string $preset    Pre-selected enquiry type: 'employer', 'candidate' or 'gifting'.
 * @param string $shortcode Optional form shortcode set on the Elementor widget.
 * @param string $variant   'dark' (default, for dark sections) or 'light' (popup).
 */
function prime_form( $preset = 'employer', $shortcode = '', $variant = 'dark' ) {
	$ff_id  = absint( prime_opt( 'fluentform_id' ) );
	$preset = in_array( $preset, array( 'employer', 'candidate', 'gifting' ), true ) ? $preset : 'employer';
	echo '<div class="prime-form-shell prime-form-shell--' . esc_attr( 'light' === $variant ? 'light' : 'dark' ) . '" data-preset="' . esc_attr( $preset ) . '">';

	if ( $shortcode && preg_match( '/^\[[a-z0-9_\-]+[^\]]*\]$/i', trim( $shortcode ) ) ) {
		echo do_shortcode( trim( $shortcode ) ); // phpcs:ignore
		echo '</div>';
		return;
	}

	if ( $ff_id && shortcode_exists( 'fluentform' ) ) {
		echo do_shortcode( '[fluentform id="' . $ff_id . '"]' );
		echo '</div>';
		return;
	}

	$status = isset( $_GET['enquiry'] ) ? sanitize_key( wp_unslash( $_GET['enquiry'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( 'sent' === $status ) {
		echo '<div class="form-notice form-notice--ok" role="status">' . prime_icon( 'check', 18 ) . esc_html__( 'Thank you — your enquiry has been sent. Our team will reply within one working day.', 'prime' ) . '</div>';
	} elseif ( 'error' === $status ) {
		echo '<div class="form-notice form-notice--err" role="alert">' . esc_html__( 'Sorry, something went wrong. Please check the required fields and try again, or email us directly.', 'prime' ) . '</div>';
	}
	?>
	<form class="prime-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="prime_enquiry">
		<input type="hidden" name="redirect" value="<?php echo esc_url( get_permalink() ? get_permalink() : home_url( '/' ) ); ?>">
		<?php wp_nonce_field( 'prime_enquiry', 'prime_nonce' ); ?>
		<div class="hp-field" aria-hidden="true"><label>Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>

		<fieldset class="pf-toggle">
			<legend class="sr-only"><?php esc_html_e( 'I am', 'prime' ); ?></legend>
			<label><input type="radio" name="type" value="employer" <?php checked( 'employer', $preset ); ?>><span><?php esc_html_e( 'I’m hiring', 'prime' ); ?></span></label>
			<label><input type="radio" name="type" value="candidate" <?php checked( 'candidate', $preset ); ?>><span><?php esc_html_e( 'I need a job', 'prime' ); ?></span></label>
			<label><input type="radio" name="type" value="gifting" <?php checked( 'gifting', $preset ); ?>><span><?php esc_html_e( 'Corporate gifting', 'prime' ); ?></span></label>
		</fieldset>

		<div class="pf-grid">
			<label class="pf-field"><input type="text" name="name" required placeholder=" " autocomplete="name"><span><?php esc_html_e( 'Full name', 'prime' ); ?> *</span></label>
			<label class="pf-field"><input type="email" name="email" required placeholder=" " autocomplete="email"><span><?php esc_html_e( 'Email', 'prime' ); ?> *</span></label>
			<label class="pf-field"><input type="tel" name="phone" required placeholder=" " autocomplete="tel"><span><?php esc_html_e( 'Phone', 'prime' ); ?> *</span></label>
			<label class="pf-field"><input type="text" name="company" placeholder=" "><span class="pf-employer pf-gifting"><?php esc_html_e( 'Company', 'prime' ); ?></span><span class="pf-candidate"><?php esc_html_e( 'Current role', 'prime' ); ?></span></label>
			<label class="pf-field pf-wide"><input type="text" name="role" placeholder=" "><span class="pf-employer"><?php esc_html_e( 'Role(s) you are hiring for', 'prime' ); ?></span><span class="pf-candidate"><?php esc_html_e( 'Role you are looking for + CV link', 'prime' ); ?></span><span class="pf-gifting"><?php esc_html_e( 'Gift type, quantity & budget per gift', 'prime' ); ?></span></label>
			<label class="pf-field pf-wide"><textarea name="message" rows="3" placeholder=" "></textarea><span class="pf-employer pf-candidate"><?php esc_html_e( 'Tell us more (location, timeline, salary range)', 'prime' ); ?></span><span class="pf-gifting"><?php esc_html_e( 'Occasion, delivery city & date', 'prime' ); ?></span></label>
		</div>
		<label class="pf-consent"><input type="checkbox" name="consent" value="1" required> <span><?php esc_html_e( 'I agree to be contacted about my enquiry.', 'prime' ); ?></span></label>
		<button type="submit" class="btn btn--primary btn--full" data-magnetic><span><?php esc_html_e( 'Send enquiry', 'prime' ); ?></span><?php echo prime_icon( 'arrow', 18 ); // phpcs:ignore ?></button>
	</form>
	<?php
	echo '</div>';
}

/**
 * Built-in form handler.
 */
function prime_handle_enquiry() {
	$redirect = isset( $_POST['redirect'] ) ? esc_url_raw( wp_unslash( $_POST['redirect'] ) ) : home_url( '/' );
	$redirect = wp_validate_redirect( $redirect, home_url( '/' ) );

	$fail = function () use ( $redirect ) {
		wp_safe_redirect( add_query_arg( 'enquiry', 'error', $redirect ) . '#enquiry' );
		exit;
	};

	if ( ! isset( $_POST['prime_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['prime_nonce'] ) ), 'prime_enquiry' ) ) {
		$fail();
	}
	// Honeypot: bots fill every field.
	if ( ! empty( $_POST['website'] ) ) {
		wp_safe_redirect( add_query_arg( 'enquiry', 'sent', $redirect ) . '#enquiry' );
		exit;
	}

	$data = array();
	foreach ( array( 'type', 'name', 'email', 'phone', 'company', 'role' ) as $f ) {
		$data[ $f ] = isset( $_POST[ $f ] ) ? sanitize_text_field( wp_unslash( $_POST[ $f ] ) ) : '';
	}
	$data['message'] = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

	if ( ! $data['name'] || ! is_email( $data['email'] ) || ! $data['phone'] || empty( $_POST['consent'] ) ) {
		$fail();
	}

	$to = prime_opt( 'form_recipient' );
	if ( ! is_email( $to ) ) {
		$to = get_option( 'admin_email' );
	}
	$labels  = array( 'candidate' => 'Job seeker', 'gifting' => 'Corporate gifting', 'employer' => 'Employer' );
	$label   = isset( $labels[ $data['type'] ] ) ? $labels[ $data['type'] ] : 'Employer';
	$subject = sprintf( '[%s] New %s enquiry from %s', get_bloginfo( 'name' ), strtolower( $label ), $data['name'] );
	$body    = "Enquiry type: {$label}\n"
		. "Name: {$data['name']}\n"
		. "Email: {$data['email']}\n"
		. "Phone: {$data['phone']}\n"
		. "Company / current role: {$data['company']}\n"
		. "Role: {$data['role']}\n\n"
		. "Message:\n{$data['message']}\n\n"
		. 'Sent from: ' . $redirect . "\n";
	$headers = array( 'Reply-To: ' . $data['name'] . ' <' . $data['email'] . '>' );

	$sent = wp_mail( $to, $subject, $body, $headers );
	wp_safe_redirect( add_query_arg( 'enquiry', $sent ? 'sent' : 'error', $redirect ) . '#enquiry' );
	exit;
}
add_action( 'admin_post_prime_enquiry', 'prime_handle_enquiry' );
add_action( 'admin_post_nopriv_prime_enquiry', 'prime_handle_enquiry' );
