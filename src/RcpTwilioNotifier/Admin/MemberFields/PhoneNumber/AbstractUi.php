<?php
/**
 * RCP: RcpTwilioNotifier\Admin\MemberFields\PhoneNumber\AbstractUiclass
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin\MemberFields\PhoneNumber
 */

namespace RcpTwilioNotifier\Admin\MemberFields\PhoneNumber;

/**
 * Make functions available to the child phone number UI classes.
 */
abstract class AbstractUi {

	/**
	 * Validate that a phone number is in a format acceptable to us.
	 *
	 * @param string $number  The phone number to check.
	 *
	 * @return bool
	 */
	protected function validate_phone_number( $number ) {
		return true; // @TODO: Implement validator
	}

}
