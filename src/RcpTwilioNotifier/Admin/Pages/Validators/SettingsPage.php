<?php
/**
 * RCP: RcpTwilioNotifier\Admin\Pages\Validators\SettingsPage class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin|Pages\Validators
 */

namespace RcpTwilioNotifier\Admin\Pages\Validators;
use RcpTwilioNotifier\Helpers\AllRegionSubscriptionIdLister;

/**
 * Validates form submissions from our SettingsPage in the WordPress admin.
 */
class SettingsPage extends AbstractValidator implements ValidatorInterface {

	/**
	 * The results of each validator.
	 *
	 * @var bool[]
	 */
	protected $validations;

	/**
	 * Validate each of the submitted inputs.
	 */
	public function validate() {
		// Validate all inputs.
		$this->validations['twilio_sid'] = $this->validate_twilio_sid();
		$this->validations['twilio_token'] = $this->validate_twilio_token();
		$this->validations['twilio_from_number'] = $this->validate_twilio_from_number();
		$this->validations['rcp_all_regions_subscription_id'] = $this->validate_rcp_all_regions_subscription_id();
		$this->validations['rcp_addon_input_label'] = $this->validate_rcp_addon_input_label();
		$this->validations['welcome_message'] = $this->validate_welcome_message();

		// If the one-click messaging feature switch is enabled, validate the one-click messaging fields.
		if ( isset( $this->posted['rcptn_enable_automated_messaging'] ) ) {
			$this->validations['automated_message_template'] = $this->validate_automated_message_template();
			$this->validations['alert_post_type'] = $this->validate_alert_post_type();
		}

		// If any input is invalid, we exit.
		if ( in_array( false, $this->validations, true ) ) {
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

		if ( ! preg_match( '/\+[0-9]{1,15}/', $this->posted['rcptn_twilio_from_number'] ) ) {
			$this->add_error( __( 'Invalid format for the Twilio from number. Please put it in the format “+10123456789”. (Start with “+”, follow with the country code, then the phone number, without any spaces.)', 'rcptn' ) );

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
			$this->add_error( __( 'No RCP all regions subscription IDs set.', 'rcptn' ) );

			return false;
		}

		if ( preg_match( '/[^0-9, ]/', $this->posted['rcptn_rcp_all_regions_subscription_id'] ) ) {
			$this->add_error( __( 'The string provided for the RCP all regions subscription IDs must contain only numbers, commas, and spaces.', 'rcptn' ) );

			return false;
		}

		$subscription_ids = AllRegionSubscriptionIdLister::convert_id_string_to_array( $this->posted['rcptn_rcp_all_regions_subscription_id'] );

		foreach ( $subscription_ids as $subscription_id ) {
			if ( false === rcp_get_subscription_details( $subscription_id ) ) {
				$this->add_error(
					// Translators: %d is the ID that does not exist as a subscription level.
					sprintf( __( 'One of the IDs provided for the RCP all regions subscription IDs does not exist as a subscription level. (ID: %d)', 'rcptn' ), $subscription_id )
				);

				return false;
			}
		}

		return true;
	}

	/**
	 * Validate the RCP addon input label input.
	 *
	 * @return bool
	 */
	private function validate_rcp_addon_input_label() {
		if ( ! isset( $this->posted['rcptn_rcp_addon_input_label'] ) || 0 === strlen( $this->posted['rcptn_rcp_addon_input_label'] ) ) {
			$this->add_error( __( 'No RCP registration add-on input label set.', 'rcptn' ) );

			return false;
		}

		return true;
	}

	/**
	 * Validate the welcome message input.
	 *
	 * @return bool
	 */
	private function validate_welcome_message() {
		if ( ! isset( $this->posted['rcptn_welcome_message'] ) || 0 === strlen( $this->posted['rcptn_welcome_message'] ) ) {
			$this->add_error( __( 'No welcome message set.', 'rcptn' ) );

			return false;
		}

		return true;
	}

	/**
	 * Validate the automated message template input.
	 *
	 * @return bool
	 */
	private function validate_automated_message_template() {
		return true;
	}

	/**
	 * Validate the alert post type input.
	 *
	 * @return bool
	 */
	private function validate_alert_post_type() {
		if ( ! isset( $this->posted['rcptn_alert_post_type'] ) || 0 === strlen( $this->posted['rcptn_alert_post_type'] ) ) {
			$this->add_error( __( 'No alert post type set.', 'rcptn' ) );

			return false;
		}

		if ( false === post_type_exists( $this->posted['rcptn_alert_post_type'] ) ) {
			$this->add_error( __( 'There’s no post type of the name provided for the alert post type. Double check the spelling to make sure you have the right one.', 'rcptn' ) );

			return false;
		}

		return true;
	}

}
