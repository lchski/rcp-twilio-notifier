<?php
/**
 * RCP: RcpTwilioNotifier\Admin\Pages\Validators\MessagingPage class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin|Pages\Validators
 */

namespace RcpTwilioNotifier\Admin\Pages\Validators;

/**
 * Validates form submissions from our MessagingPage in the WordPress admin.
 */
class MessagingPage extends AbstractValidator implements ValidatorInterface {

	/**
	 * Whether or not the submitted data is valid.
	 *
	 * @var bool
	 */
	private $is_valid;

	/**
	 * List of regions available for messaging.
	 *
	 * @var array
	 */
	private $regions;

	/**
	 * Set internal values.
	 *
	 * @param array $regions  Region list.
	 */
	public function __construct( $regions ) {
		$this->regions = $regions;
		$this->is_valid = false;
	}

	/**
	 * Validate each of the submitted inputs, setting them as properties if valid.
	 */
	public function validate() {
		// Validate both inputs.
		$is_valid_region = $this->validate_region();
		$is_valid_message = $this->validate_message();

		// If either input is invalid, we exit.
		if ( ! $is_valid_message || ! $is_valid_region ) {
			$this->is_valid = false;

			return false;
		}

		// Both inputs are valid, so we set them as properties.
		$this->region = new Region( $_POST['rcptn_region'] ); // WPCS: CSRF ok.
		$this->message = $_POST['rcptn_message']; // WPCS: CSRF ok.

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
	 * Validate the region input.
	 *
	 * @return bool
	 */
	private function validate_region() {
		if ( ! isset( $_POST['rcptn_region'] ) ) { // WPCS: CSRF ok.
			$this->add_error( __( 'No region set.', 'rcptn' ) );

			return false;
		}

		$region_validator = new \RcpTwilioNotifier\Helpers\Validators\Region( $this->regions );

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
