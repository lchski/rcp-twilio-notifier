<?php
/**
 * RCP: RcpTwilioNotifier_RegionRegistrationField class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier
 */

/**
 * Adds a region field to the RCP registration process.
 */
class RcpTwilioNotifier_RegionRegistrationField extends RcpTwilioNotifier_AbstractRegionFieldUi {

	/**
	 * Hooks class functions into WordPress.
	 */
	public function init() {

		add_action( 'rcp_after_password_registration_field', array( $this, 'render_select' ) );
		add_action( 'rcp_profile_editor_after', array( $this, 'render_select' ) );

		add_action( 'rcp_form_errors', array( $this, 'validate_on_register' ) , 10 );

	}

	/**
	 * Render the dropdown with the regions.
	 */
	public function render_select() {

		$select_renderer = new RcpTwilioNotifier_RegionSelectRenderer( $this->regions, get_current_user_id() );

		?>
			<p>
				<label for="rcptn_region"><?php esc_html_e( 'Your Home Region', 'rcptn' ); ?></label>
				<?php $select_renderer->render(); ?>
			</p>
		<?php

	}

	/**
	 * Validate the posted registration data.
	 *
	 * @param array $posted  The posted registration data.
	 */
	public function validate_on_register( $posted ) {

		if ( rcp_get_subscription_id() ) {
			return;
		}

		// Add an error message if the submitted option isn't one of our valid choices.
		if ( ! in_array( $posted['rcptn_region'], $this->region_keys, true ) ) {
			rcp_errors()->add( 'invalid_region', __( 'Please select a valid home region', 'rcptn' ), 'register' );
		}

	}

}
