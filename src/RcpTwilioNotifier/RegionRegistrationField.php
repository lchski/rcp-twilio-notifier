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
class RcpTwilioNotifier_RegionRegistrationField {

	/**
	 * Hooks class functions into WordPress.
	 */
	public function init() {

		add_action( 'rcp_after_password_registration_field', array( $this, 'render_select' ) );
		add_action( 'rcp_profile_editor_after', array( $this, 'render_select' ) );

	}

	/**
	 * Render the dropdown with the regions.
	 */
	public function render_select() {

		$select_renderer = new RcpTwilioNotifier_RegionSelectRenderer( get_current_user_id() );

		?>
			<p>
				<label for="rcptn_region"><?php esc_html_e( 'Your Home Region', 'rcptn' ); ?></label>
				<?php $select_renderer->render(); ?>
			</p>
		<?php

	}

}
