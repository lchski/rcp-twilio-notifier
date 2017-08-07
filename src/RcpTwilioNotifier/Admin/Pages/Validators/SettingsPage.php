<?php
/**
 * RCP: RcpTwilioNotifier\Admin\Pages\Validators\SettingsPage class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin|Pages\Validators
 */

namespace RcpTwilioNotifier\Admin\Pages\Validators;

/**
 * Validates form submissions from our SettingsPage in the WordPress admin.
 */
class SettingsPage extends AbstractValidator implements ValidatorInterface {

	/**
	 * Whether or not the submitted data is valid.
	 *
	 * @var bool
	 */
	private $is_valid;

	/**
	 * Set internal values.
	 */
	public function __construct() {
		$this->is_valid = false;
	}

	/**
	 * Validate each of the submitted inputs.
	 */
	public function validate() {
		// Validate all inputs.
		$is_valid_twilio_sid = $this->validate_twilio_sid();
		$is_valid_twilio_token = $this->validate_twilio_token();
		$is_valid_twilio_from_number = $this->validate_twilio_from_number();
		$is_valid_rcp_all_regions_subscription_id = $this->validate_rcp_all_regions_subscription_id();

		// If any input is invalid, we exit.
		if ( ! $is_valid_twilio_sid || ! $is_valid_twilio_token || ! $is_valid_twilio_from_number || ! $is_valid_rcp_all_regions_subscription_id ) {
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
		if ( ! isset( $_POST['rcptn_twilio_sid'] ) ) { // WPCS: CSRF ok.
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
		if ( ! isset( $_POST['rcptn_twilio_token'] ) ) { // WPCS: CSRF ok.
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
		if ( ! isset( $_POST['rcptn_twilio_from_number'] ) ) { // WPCS: CSRF ok.
			$this->add_error( __( 'No Twilio from number set.', 'rcptn' ) );

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
		if ( ! isset( $_POST['rcptn_rcp_all_regions_subscription_id'] ) ) { // WPCS: CSRF ok.
			$this->add_error( __( 'No RCP all regions subscription ID set.', 'rcptn' ) );

			return false;
		}

		return true;
	}

	/**
	 * Validate the region input.
	 *
	 * @return bool
	 */
	private function validate_region() {
		if ( ! isset( $_POST['rcptn_region'] ) ) { // WPCS: CSRF ok.
			$this->add_error( __( 'No region set.', 'rcptn' ) );

			return false;
		}

		$region_validator = new Region( $this->regions );

		if ( ! $region_validator->is_valid_region( $_POST['rcptn_region'] ) ) { // WPCS: CSRF ok.
			$this->add_error( __( 'Invalid region set.', 'rcptn' ) );

			return false;
		}

		return true;
	}

	/**
	 * Validate the message input.
	 *
	 * @return bool
	 */
	private function validate_message() {
		if ( ! isset( $_POST['rcptn_message'] ) ) { // WPCS: CSRF ok.
			$this->add_error( __( 'No message set.', 'rcptn' ) );

			return false;
		}

		if ( 0 === strlen( $_POST['rcptn_message'] ) ) { // WPCS: CSRF ok.
			$this->add_error( __( 'Message must not be empty.', 'rcptn' ) );

			return false;
		}

		if ( ! MessageBody::is_valid_message_body( $_POST['rcptn_message'] ) ) { // WPCS: CSRF ok.
			$this->add_error( __( 'Invalid message body.', 'rcptn' ) );

			return false;
		}

		return true;
	}

}
