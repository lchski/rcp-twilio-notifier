<?php
/**
 * RCP: RcpTwilioNotifier\Admin\Pages\Validators\ValidatorInterface interface
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin|Pages\Validators
 */

namespace RcpTwilioNotifier\Admin\Pages\Validators;

/**
 * Ensures child classes to RcpTwilioNotifier\Admin\Pages\Validators\AbstractValidator have the right methods.
 */
interface ValidatorInterface {

	/**
	 * The validator itself.
	 *
	 * @return bool
	 */
	public function validate();

}
