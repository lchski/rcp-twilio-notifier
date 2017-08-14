<?php
/**
 * RCP: RcpTwilioNotifier\Admin\Pages\Validators\AbstractValidator class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin|Pages\Validators
 */

namespace RcpTwilioNotifier\Admin\Pages\Validators;
use RcpTwilioNotifier\Helpers\Notifier;
use RcpTwilioNotifier\Models\Notice;

/**
 * Triggers page validation and handles errors.
 */
abstract class AbstractValidator {

	/**
	 * Whether or not the submitted data is valid.
	 *
	 * @var bool
	 */
	protected $is_valid;

	/**
	 * The $_POST array.
	 *
	 * @var array
	 */
	protected $posted;

	/**
	 * Trigger validation and return the result.
	 *
	 * @return bool
	 */
	public function init() {
		$this->posted = $_POST; // WPCS: CSRF ok.

		$this->is_valid = false;

		return $this->validate();
	}

	/**
	 * Add an error message to the validation process.
	 *
	 * @param string $error_message  The error message to display.
	 */
	protected function add_error( $error_message ) {
		$notifier = Notifier::get_instance();

		$notifier->add_notice( new Notice( 'error', $error_message ) );
	}

}
