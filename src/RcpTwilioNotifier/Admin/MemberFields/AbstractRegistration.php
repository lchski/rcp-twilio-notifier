<?php
/**
 * RCP: RcpTwilioNotifier\Admin\MemberFields\AbstractRegistration class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin\MemberFields
 */

namespace RcpTwilioNotifier\Admin\MemberFields;

/**
 * Generic init for RCP registration field classes.
 */
abstract class AbstractRegistration {

	/**
	 * Hooks class functions into WordPress.
	 */
	public function init() {

		add_action( 'rcp_after_password_registration_field', array( $this, 'render_field' ) );
		add_action( 'rcp_profile_editor_after', array( $this, 'render_field' ) );

		add_action( 'rcp_form_errors', array( $this, 'validate_on_register' ) , 10 );

		add_action( 'rcp_form_processing', array( $this, 'save_on_register' ), 10, 2 );

	}

}

