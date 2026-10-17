<?php
/**
 * Fluent Forms integration.
 *
 * When Fluent Forms is active and no form has been chosen yet, the theme
 * creates a ready-made "Prime Recruitments Enquiry" form through Fluent
 * Forms' own form service (so it is a normal, fully editable Fluent form)
 * and connects it to the popup and every CTA on the site.
 *
 * @package Prime
 */

defined( 'ABSPATH' ) || exit;

/**
 * Field builders using Fluent Forms' current element format.
 */
function prime_ff_text( $name, $label, $placeholder, $required, $type = 'text' ) {
	return array(
		'element'        => 'input_text',
		'attributes'     => array( 'type' => $type, 'name' => $name, 'value' => '', 'class' => '', 'placeholder' => $placeholder, 'maxlength' => '' ),
		'settings'       => array(
			'container_class'           => '',
			'label'                     => $label,
			'label_placement'           => '',
			'admin_field_label'         => $label,
			'help_message'              => '',
			'prefix_label'              => '',
			'suffix_label'              => '',
			'validation_rules'          => array( 'required' => array( 'value' => $required, 'message' => 'This field is required', 'global' => true ) ),
			'conditional_logics'        => array(),
			'is_unique'                 => 'no',
			'unique_validation_message' => 'This value need to be unique.',
		),
		'editor_options' => array( 'title' => 'Simple Text', 'icon_class' => 'ff-edit-text', 'template' => 'inputText' ),
		'uniqElKey'      => 'el_' . $name . '_prime',
	);
}

function prime_ff_fields() {
	$radio = array(
		'element'        => 'input_radio',
		'attributes'     => array( 'type' => 'radio', 'name' => 'enquiry_type', 'value' => 'employer' ),
		'settings'       => array(
			'dynamic_default_value' => '',
			'container_class'       => 'prime-ff-type',
			'label'                 => 'I am',
			'admin_field_label'     => 'Enquiry type',
			'label_placement'       => 'hide_label',
			'display_type'          => 'inline',
			'help_message'          => '',
			'randomize_options'     => 'no',
			'advanced_options'      => array(
				array( 'label' => 'I’m hiring', 'value' => 'employer', 'calc_value' => '', 'image' => '' ),
				array( 'label' => 'I need a job', 'value' => 'candidate', 'calc_value' => '', 'image' => '' ),
				array( 'label' => 'Corporate gifting', 'value' => 'gifting', 'calc_value' => '', 'image' => '' ),
			),
			'calc_value_status'     => false,
			'enable_image_input'    => false,
			'values_visible'        => false,
			'validation_rules'      => array( 'required' => array( 'value' => true, 'message' => 'Please choose an option', 'global' => true ) ),
			'conditional_logics'    => array(),
			'layout_class'          => 'ff_list_inline',
		),
		'editor_options' => array( 'title' => 'Radio Field', 'icon_class' => 'ff-edit-radio', 'element' => 'input-radio', 'template' => 'inputCheckable' ),
		'uniqElKey'      => 'el_enquiry_type_prime',
	);

	$email                                   = prime_ff_text( 'email', 'Email', 'you@company.com', true, 'email' );
	$email['element']                        = 'input_email';
	$email['settings']['validation_rules']['email'] = array( 'value' => true, 'message' => 'Please enter a valid email', 'global' => true );
	$email['editor_options']                 = array( 'title' => 'Email Address', 'icon_class' => 'ff-edit-email', 'template' => 'inputText' );

	$message = array(
		'element'        => 'textarea',
		'attributes'     => array( 'name' => 'message', 'value' => '', 'id' => '', 'class' => '', 'placeholder' => 'Roles, quantity, location, timeline…', 'rows' => 3, 'cols' => 2 ),
		'settings'       => array(
			'container_class'    => '',
			'label'              => 'Tell us more',
			'admin_field_label'  => 'Message',
			'label_placement'    => '',
			'help_message'       => '',
			'validation_rules'   => array( 'required' => array( 'value' => false, 'message' => 'This field is required', 'global' => true ) ),
			'conditional_logics' => array(),
		),
		'editor_options' => array( 'title' => 'Text Area', 'icon_class' => 'ff-edit-textarea', 'template' => 'inputTextarea' ),
		'uniqElKey'      => 'el_message_prime',
	);

	return array(
		$radio,
		prime_ff_text( 'full_name', 'Full name', 'Your name', true ),
		$email,
		prime_ff_text( 'phone', 'Phone / WhatsApp', '+91', true, 'tel' ),
		prime_ff_text( 'company', 'Company / current role', '', false ),
		$message,
	);
}

/**
 * Register the template with Fluent Forms.
 */
function prime_ff_template( $forms ) {
	$to   = is_email( prime_opt( 'contact_email' ) ) ? prime_opt( 'contact_email' ) : '{wp.admin_email}';
	$json = array(
		array(
			'title'         => 'Prime Recruitments Enquiry',
			'form'          => array(
				'fields'       => prime_ff_fields(),
				'submitButton' => array(
					'uniqElKey'      => 'el_submit_prime',
					'element'        => 'button',
					'attributes'     => array( 'type' => 'submit', 'class' => '' ),
					'settings'       => array(
						'align'            => 'left',
						'button_style'     => 'default',
						'container_class'  => '',
						'help_message'     => '',
						'background_color' => '#c9a227',
						'button_size'      => 'lg',
						'color'            => '#0b2342',
						'button_ui'        => array( 'type' => 'default', 'text' => 'Send enquiry', 'img_url' => '' ),
					),
					'editor_options' => array( 'title' => 'Submit Button' ),
				),
			),
			'notifications' => array(
				'name'           => 'Prime enquiry notification',
				'sendTo'         => array( 'type' => 'email', 'email' => $to, 'field' => 'email', 'routing' => array( array( 'email' => null, 'field' => null, 'operator' => '=', 'value' => null ) ) ),
				'fromName'       => '',
				'fromEmail'      => '',
				'replyTo'        => '{inputs.email}',
				'bcc'            => '',
				'subject'        => 'New website enquiry ({inputs.enquiry_type}) from {inputs.full_name}',
				'message'        => '<p>{all_data}</p><p>Sent from: {embed_post.permalink}</p>',
				'conditionals'   => array( 'status' => false, 'type' => 'all', 'conditions' => array( array( 'field' => null, 'operator' => '=', 'value' => null ) ) ),
				'enabled'        => true,
				'email_template' => '',
			),
		),
	);
	$forms['prime_enquiry'] = array(
		'screenshot' => '',
		'createable' => true,
		'title'      => 'Prime Recruitments Enquiry',
		'brief'      => 'Employer, job seeker and corporate gifting enquiries.',
		'tag'        => array( 'prime', 'contact', 'enquiry' ),
		'category'   => 'Basic',
		'json'       => wp_json_encode( $json ),
	);
	return $forms;
}
add_filter( 'fluentform/predefined_forms', 'prime_ff_template' );

/**
 * Create the form once, when Fluent Forms is active and no form is set.
 */
function prime_ff_maybe_create() {
	if ( ! current_user_can( 'manage_options' ) || absint( prime_opt( 'fluentform_id' ) ) || get_option( 'prime_ff_attempted' ) ) {
		return;
	}
	if ( ! class_exists( '\FluentForm\App\Services\Form\FormService' ) ) {
		return;
	}
	update_option( 'prime_ff_attempted', 1 );
	try {
		$service = new \FluentForm\App\Services\Form\FormService();
		$form    = $service->store( array( 'type' => 'form', 'predefined' => 'prime_enquiry', 'title' => 'Prime Recruitments Enquiry' ) );
		if ( $form && ! empty( $form->id ) ) {
			set_theme_mod( 'prime_fluentform_id', (int) $form->id );
			set_transient( 'prime_ff_notice', (int) $form->id, 300 );
		}
	} catch ( \Throwable $e ) {
		// Leave the built-in form in place; the admin can still pick a form in the Customizer.
		set_transient( 'prime_ff_notice', 'error', 300 );
	}
}
add_action( 'admin_init', 'prime_ff_maybe_create', 20 );

/**
 * Let the admin re-run creation from Prime Setup (e.g. after deleting the form).
 */
function prime_ff_reset_attempt() {
	delete_option( 'prime_ff_attempted' );
}

function prime_ff_notice() {
	$n = get_transient( 'prime_ff_notice' );
	if ( false === $n ) {
		return;
	}
	delete_transient( 'prime_ff_notice' );
	if ( 'error' === $n ) {
		echo '<div class="notice notice-warning is-dismissible"><p>' . esc_html__( 'Prime Recruitments could not create the Fluent form automatically. Create any form in Fluent Forms and paste its ID in Customize → Prime Recruitments → Contact form.', 'prime' ) . '</p></div>';
		return;
	}
	printf(
		'<div class="notice notice-success is-dismissible"><p>%1$s <a href="%2$s">%3$s</a></p></div>',
		/* translators: %d form id */
		esc_html( sprintf( __( 'Fluent Forms enquiry form #%d created and connected to the popup and every CTA on your site.', 'prime' ), (int) $n ) ),
		esc_url( admin_url( 'admin.php?page=fluent_forms&route=editor&form_id=' . (int) $n ) ),
		esc_html__( 'Edit the form', 'prime' )
	);
}
add_action( 'admin_notices', 'prime_ff_notice' );
