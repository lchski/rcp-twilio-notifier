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
	 * @param string $country_code  The phone number's country code.
	 * @param string $number        The phone number to check.
	 *
	 * @return bool
	 */
	public static function is_valid_phone_number( $country_code, $number ) {
		$phone_util = \libphonenumber\PhoneNumberUtil::getInstance();

		try {
			$phone_number_proto = $phone_util->parse( $number, $country_code );

			return $phone_util->isValidNumber( $phone_number_proto );
		} catch ( \libphonenumber\NumberParseException $e ) {
			// @TODO: Log this.
			return;
		}
	}

}
