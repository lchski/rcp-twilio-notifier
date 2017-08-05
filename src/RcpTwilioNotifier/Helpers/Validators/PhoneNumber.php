<?php
/**
 * RCP: RcpTwilioNotifier\Helpers\Validators\PhoneNumber
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Helpers\Validators
 */

namespace RcpTwilioNotifier\Helpers\Validators;

/**
 * Validation functions related to phone numbers.
 */
class PhoneNumber {

	/**
	 * Validate that a phone number is in an acceptable format.
	 *
	 * @param string $number  The phone number to check.
	 *
	 * @return bool
	 */
	public static function is_valid_phone_number( $number ) {
		return true; // @TODO: Implement validator
	}

}
