<?php
/**
 * RCP: RcpTwilioNotifier\Admin\Pages\Validators\SettingsPage class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin|Pages\Validators
 */

namespace RcpTwilioNotifier\Admin\Pages\Validators;
use RcpTwilioNotifier\Helpers\Validators\PhoneNumber;

/**
 * Validates form submissions from our SettingsPage in the WordPress admin.
 */
class SettingsPage extends AbstractValidator implements ValidatorInterface {

	/**
	 * Validate each of the submitted inputs.
	 */
	public function validate() {
		// Validate all inputs.
		$validations = array();

		$validations['twilio_sid'] = $this->validate_twilio_sid();
		$validations['twilio_token'] = $this->validate_twilio_token();
		$validations['twilio_from_number'] = $this->validate_twilio_from_number();
		$validations['rcp_all_regions_subscription_id'] = $this->validate_rcp_all_regions_subscription_id();

		// If any input is invalid, we exit.
		if ( in_array( false, $validations, true ) ) {
			$this->is_valid = false;

			return false;
		}

		// A happy ending!
		$this->is_valid = true;

		return true;
	}

	/**
	 * Check whether or not the submitted data is valid.
	 *
	 * @return bool
	 */
	public function is_valid() {
		return $this->is_valid;
	}

	/**
	 * Validate the Twilio SID input.
	 *
	 * @return bool
	 */
	private function validate_twilio_sid() {
		if ( ! isset( $this->posted['rcptn_twilio_sid'] ) || 0 === strlen( $this->posted['rcptn_twilio_sid'] ) ) {
			$this->add_error( __( 'No Twilio SID set.', 'rcptn' ) );

			return false;
		}

		return true;
	}

	/**
	 * Validate the Twilio token input.
	 *
	 * @return bool
	 */
	private function validate_twilio_token() {
		if ( ! isset( $this->posted['rcptn_twilio_token'] ) || 0 === strlen( $this->posted['rcptn_twilio_token'] ) ) {
			$this->add_error( __( 'No Twilio token set.', 'rcptn' ) );

			return false;
		}

		return true;
	}

	/**
	 * Validate the Twilio from number input.
	 *
	 * @return bool
	 */
	private function validate_twilio_from_number() {
		if ( ! isset( $this->posted['rcptn_twilio_from_number'] ) || 0 === strlen( $this->posted['rcptn_twilio_from_number'] ) ) {
			$this->add_error( __( 'No Twilio from number set.', 'rcptn' ) );

			return false;
		}

		if ( ! PhoneNumber::is_valid_phone_number( $this->posted['rcptn_twilio_from_number'] ) ) {
			$this->add_error( __( 'Invalid format for the Twilio from number. Please put it in the format “+10123456789”.', 'rcptn' ) );

			return false;
		}

		return true;
	}

	/**
	 * Validate the RCP all regions subscription ID input.
	 *
	 * @return bool
	 */
	private function validate_rcp_all_regions_subscription_id() {
		if ( ! isset( $this->posted['rcptn_rcp_all_regions_subscription_id'] ) || 0 === strlen( $this->posted['rcptn_rcp_all_regions_subscription_id'] ) ) {
			$this->add_error( __( 'No RCP all regions subscription ID set.', 'rcptn' ) );

			return false;
		}

		if ( false === rcp_get_subscription_details( $this->posted['rcptn_rcp_all_regions_subscription_id'] ) ) {
			$this->add_error( __( 'The ID provided for the RCP all regions subscription ID does not exist as a subscription level.', 'rcptn' ) );

			return false;
		}

		return true;
	}

}
