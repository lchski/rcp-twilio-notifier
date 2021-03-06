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
		// Text fields.
		$setting_text_field_keys = array(
			'rcptn_twilio_sid',
			'rcptn_twilio_token',
			'rcptn_twilio_from_number',
			'rcptn_rcp_addon_input_label',
			'rcptn_rcp_all_regions_subscription_id',
		);

		foreach ( $setting_text_field_keys as $field_key ) {
			update_option( $field_key, sanitize_text_field( $this->posted[ $field_key ] ) );
		}

		// Key fields.
		$setting_text_field_keys = array(
			'rcptn_alert_post_type',
		);

		foreach ( $setting_text_field_keys as $field_key ) {
			update_option( $field_key, sanitize_key( $this->posted[ $field_key ] ) );
		}

		// Textarea fields.
		$setting_textarea_field_keys = array(
			'rcptn_automated_message_template',
			'rcptn_welcome_message',
		);

		foreach ( $setting_textarea_field_keys as $field_key ) {
			update_option( $field_key, sanitize_textarea_field( $this->posted[ $field_key ] ) );
		}

		// Checkmark field.
		update_option( 'rcptn_enable_automated_messaging', isset( $this->posted['rcptn_enable_automated_messaging'] ) );
	}

}
