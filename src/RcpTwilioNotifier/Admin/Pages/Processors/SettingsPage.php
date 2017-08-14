<?php
/**
 * RCP: RcpTwilioNotifier\Admin\Pages\Processors\SettingsPage class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin|Pages\Processors
 */

namespace RcpTwilioNotifier\Admin\Pages\Processors;
use RcpTwilioNotifier\Helpers\Notifier;
use RcpTwilioNotifier\Models\Notice;

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

		$this->save_settings();

		$notifier = Notifier::get_instance();
		$notifier->add_notice( new Notice( 'success', 'Settings saved successfully.' ) );
	}

	/**
	 * Save each of the settings to the database.
	 */
	private function save_settings() {
		$settings_field_keys = array(
			'rcptn_twilio_sid',
			'rcptn_twilio_token',
			'rcptn_twilio_from_number',
			'rcptn_rcp_all_regions_subscription_id',
		);

		foreach ( $settings_field_keys as $field_key ) {
			update_option( $field_key, $this->posted[ $field_key ] );
		}
	}

}
