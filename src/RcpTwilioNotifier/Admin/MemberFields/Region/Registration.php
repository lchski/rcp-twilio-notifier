<?php
/**
 * RCP: RcpTwilioNotifier\Admin\MemberFields\Region\Registration class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier
 */

namespace RcpTwilioNotifier\Admin\MemberFields\Region;

use RcpTwilioNotifier\Admin\MemberFields\AbstractRegistration;
use RcpTwilioNotifier\Helpers\Renderers\RegionSelect;
use RcpTwilioNotifier\Helpers\Validators\Region;

/**
 * Adds a region field to the RCP registration process.
 */
class Registration extends AbstractRegistration {

	/**
	 * Set internal state.
	 *
	 * @param array $regions  List of regions available for selection.
	 */
	public function __construct( $regions ) {
		$this->regions = $regions;
	}

	/**
	 * Render the dropdown with the regions.
	 */
	public function render_field() {

		$select_renderer = new RegionSelect( $this->regions, get_current_user_id() );

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

		$region_validator = new Region( $this->regions );

		// Add an error message if the submitted option isn't one of our valid choices.
		if ( ! $region_validator->is_valid_region( $posted['rcptn_region'] ) ) {
			rcp_errors()->add( 'invalid_region', __( 'Please select a valid home region', 'rcptn' ), 'register' );
		}

	}

	/**
	 * Save the home region on successful registration.
	 *
	 * @param array $posted  The posted registration data.
	 * @param int   $user_id  The newly created user's ID.
	 */
	public function save_on_register( $posted, $user_id ) {

		if ( ! empty( $posted['rcptn_region'] ) ) {
			update_user_meta( $user_id, 'rcptn_region', sanitize_text_field( $posted['rcptn_region'] ) );
		}

	}

}
