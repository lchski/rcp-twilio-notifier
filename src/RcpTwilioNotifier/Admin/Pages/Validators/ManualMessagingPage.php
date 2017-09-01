<?php
/**
 * RCP: RcpTwilioNotifier\Admin\Pages\Validators\ManualMessagingPage class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin|Pages\Validators
 */

namespace RcpTwilioNotifier\Admin\Pages\Validators;
use RcpTwilioNotifier\Helpers\Validators\MessageBody;
use RcpTwilioNotifier\Helpers\Validators\Region;

/**
 * Validates form submissions from our ManualMessagingPage in the WordPress admin.
 */
class ManualMessagingPage extends AbstractValidator implements ValidatorInterface {

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
	}

	/**
	 * Validate each of the submitted inputs.
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
		if ( ! isset( $this->posted['rcptn_region'] ) ) {
			$this->add_error( __( 'No region set.', 'rcptn' ) );

			return false;
		}

		// Special case for all regions option.
		if ( 'all' === $this->posted['rcptn_region'] ) {
			return true;
		}

		$region_validator = new Region( $this->regions );

		if ( ! $region_validator->is_valid_region( $this->posted['rcptn_region'] ) ) {
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
		if ( ! isset( $this->posted['rcptn_message'] ) ) {
			$this->add_error( __( 'No message set.', 'rcptn' ) );

			return false;
		}

		if ( 0 === strlen( $this->posted['rcptn_message'] ) ) {
			$this->add_error( __( 'Message must not be empty.', 'rcptn' ) );

			return false;
		}

		if ( ! MessageBody::is_valid_message_body( $this->posted['rcptn_message'] ) ) {
			$this->add_error( __( 'Invalid message body.', 'rcptn' ) );

			return false;
		}

		return true;
	}

}
