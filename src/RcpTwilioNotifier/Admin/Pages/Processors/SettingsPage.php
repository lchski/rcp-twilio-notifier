<?php
/**
 * RCP: RcpTwilioNotifier\Admin\Pages\Processors\SettingsPage class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin|Pages\Processors
 */

namespace RcpTwilioNotifier\Admin\Pages\Processors;

/**
 * Processes form submissions from our SettingsPage in the WordPress admin.
 */
class SettingsPage extends AbstractProcessor implements ProcessorInterface {

	/**
	 * The name of the action that this processor processes.
	 *
	 * @var string  Action name.
	 */
	protected $action_name = 'save-settings';

	/**
	 * The name of the nonce that this processor validates.
	 *
	 * @var string  Nonce name.
	 */
	protected $nonce_name = 'rcptn_save_settings_nonce';

	/**
	 * Set internal values.
	 */
	public function __construct() {}

	/**
	 * Process!
	 */
	public function process() {
		$validator = new \RcpTwilioNotifier\Admin\Pages\Validators\SettingsPage();
		$validator->init();

		if ( ! $validator->is_valid() ) {
			return false;
		}

		// @TODO: Process...
	}

}
