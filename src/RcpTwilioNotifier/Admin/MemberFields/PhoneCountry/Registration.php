<?php
/**
 * RCP: RcpTwilioNotifier\Admin\MemberFields\PhoneCountry\Registration class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier
 */

namespace RcpTwilioNotifier\Admin\MemberFields\PhoneCountry;

use RcpTwilioNotifier\Admin\MemberFields\AbstractRegistration;
use RcpTwilioNotifier\Helpers\CountryLister;
use RcpTwilioNotifier\Helpers\Renderers\CountrySelect;

/**
 * Adds a phone country field to the RCP registration process.
 */
class Registration extends AbstractRegistration {

	/**
	 * Render the dropdown with the regions.
	 */
	public function render_field() {

		$select_renderer = new CountrySelect(
			array(
				'user_id' => get_current_user_id(),
			)
		);

		?>
			<p>
				<label for="rcptn_phone_country_code"><?php esc_html_e( 'Your Phone Number Country', 'rcptn' ); ?></label>
				<?php $select_renderer->render(); ?>
				<small style="display: block; margin-top: 0.75em;"><?php esc_html_e( 'Choose the country that your phone number is in. We’ll add the country code to your number when we send out alerts.', 'rcptn' ); ?></small>
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
		if ( ! in_array( $posted['rcptn_phone_country_code'], array_keys( CountryLister::get_countries() ), true ) ) {
			rcp_errors()->add( 'invalid_phone_country_code', __( 'Please select a valid country for your phone country code.', 'rcptn' ), 'register' );
		}

	}

	/**
	 * Save the home region on successful registration.
	 *
	 * @param array $posted  The posted registration data.
	 * @param int   $user_id  The newly created user's ID.
	 */
	public function save_on_register( $posted, $user_id ) {

		if ( ! empty( $posted['rcptn_phone_country_code'] ) ) {
			update_user_meta( $user_id, 'rcptn_phone_country_code', sanitize_text_field( $posted['rcptn_phone_country_code'] ) );
		}

	}

}
