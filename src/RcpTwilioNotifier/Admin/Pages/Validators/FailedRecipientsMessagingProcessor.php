<?php
/**
 * RCP: RcpTwilioNotifier\Admin\Pages\Validators\FailedRecipientsMessagingProcessor class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin|Pages\Validators
 */

namespace RcpTwilioNotifier\Admin\Pages\Validators;
use RcpTwilioNotifier\Models\Message;

/**
 * Validates form submissions from our FailedRecipientsMessagingProcessor in the WordPress admin.
 */
class FailedRecipientsMessagingProcessor extends AbstractValidator implements ValidatorInterface {

	/**
	 * Validate each of the submitted inputs.
	 */
	public function validate() {
		// Validate both inputs.
		$is_valid_message_id = $this->validate_message_id();

		// If the input is invalid, we exit.
		if ( ! $is_valid_message_id ) {
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
	 * Validate the message ID input.
	 *
	 * @return bool
	 */
	private function validate_message_id() {
		if ( ! isset( $this->posted['rcptn_message_id'] ) ) {
			$this->add_error( __( 'No Message ID set.', 'rcptn' ) );

			return false;
		}

		if ( ! is_numeric( $this->posted['rcptn_message_id'] ) ) {
			$this->add_error( __( 'Message ID must be an integer.', 'rcptn' ) );

			return false;
		}

		if ( ! Message::find( absint( $this->posted['rcptn_message_id'] ) ) instanceof Message ) {
			$this->add_error( __( 'Could not find a Message with that ID.', 'rcptn' ) );

			return false;
		}

		return true;
	}

}
