<?php
/**
 * RCP: RcpTwilioNotifier\Helpers\Validators\PhoneNumber
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Helpers\Validators
 */

namespace RcpTwilioNotifier\Helpers\Validators;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;

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
	 * @return bool|\WP_Error
	 */
	public static function is_valid_phone_number( $country_code, $number ) {
		$phone_util = PhoneNumberUtil::getInstance();

		try {
			$phone_number_proto = $phone_util->parse( $number, $country_code );

			return $phone_util->isValidNumber( $phone_number_proto );
		} catch ( NumberParseException $e ) {
			return new \WP_Error(
				'rcptn_libphonenumber_parse', 'RCPTN Exception: "' . $e->getMessage() . '" (libphonenumber NumberParseException: ' . $e->getCode() . ')', array(
					'exception' => $e,
				)
			);
		}
	}

}
