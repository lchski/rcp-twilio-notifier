<?php
/**
 * RCP: RcpTwilioNotifier\Helpers\Validators\MessageBody
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Helpers\Validators
 */

namespace RcpTwilioNotifier\Helpers\Validators;

/**
 * Validation functions related to SMS message bodies.
 */
class MessageBody {

	/**
	 * Validates a string to ensure it conforms to messaging restrictions.
	 *
	 * @param string $body  The message body.
	 *
	 * @return bool  Whether or not the message was valid.
	 */
	public static function is_valid_message_body( $body ) {
		// Twilio API allows a maximum message length of 1600 characters.
		if ( strlen( $body ) > 1600 ) {
			return false;
		}

		return true;
	}

}
