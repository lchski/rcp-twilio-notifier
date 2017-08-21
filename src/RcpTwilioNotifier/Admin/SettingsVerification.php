<?php
/**
 * RCP: RcpTwilioNotifier\Admin\SettingsVerification class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin
 */

namespace RcpTwilioNotifier\Admin;
use RcpTwilioNotifier\Helpers\Notifier;
use RcpTwilioNotifier\Models\Notice;

/**
 * Checks whether or not required settings are present.
 */
class SettingsVerification {

	/**
	 * Whether or not all the required settings are present.
	 *
	 * @var bool
	 */
	private $are_settings_present;

	/**
	 * Set initial state.
	 */
	public function __construct() {
		$this->are_settings_present = false;
	}

	/**
	 * Check the settings.
	 */
	public function init() {
		$base_settings_check = $this->are_base_settings_present();
		$automated_settings_check = $this->are_automated_settings_present();

		$this->are_settings_present = $base_settings_check && $automated_settings_check;
	}

	/**
	 * Report on whether the required settings are present.
	 *
	 * @return bool
	 */
	public function are_settings_present() {
		return $this->are_settings_present;
	}

	/**
	 * Add an admin notice if the required settings aren’t present.
	 */
	public function remind_if_settings_not_present() {
		if ( ! $this->are_settings_present() ) {
			$notifier = Notifier::get_instance();
			$notifier->add_notice(
				new Notice(
					'error',
					array( $this, 'render_settings_reminder' )
				)
			);
		}
	}

	/**
	 * Render the admin notice for missing settings.
	 */
	public function render_settings_reminder() {
		?>
			<p>
				<?php
					printf(
						wp_kses(
							// Translators: %s: the href to the settings page; keep it as-is.
							__( 'There are settings missing for the Twilio Region Notifier. Make sure to fill out all the fields on the <a href="%s">settings page</a>. The plugin won’t work properly until you do. (If you’ve just filled out the missing fields, you can ignore this message.)', 'rcptn' ),
							array(
								'a' => array(
									'href' => array(),
								),
							)
						),
						esc_attr( menu_page_url( 'rcptn-region-notifier-settings', false ) )
					);
				?>
			</p>
		<?php
	}

	/**
	 * Check whether the basic settings (the ones that are always required) for the plugin are present.
	 *
	 * @return bool
	 */
	private function are_base_settings_present() {
		return $this->check_settings(
			array(
				'rcptn_twilio_sid',
				'rcptn_twilio_token',
				'rcptn_twilio_from_number',
				'rcptn_rcp_all_regions_subscription_id',
				'rcptn_welcome_message',
			)
		);
	}

	/**
	 * Check whether the settings required for one-click messaging are present.
	 *
	 * @return bool
	 */
	private function are_automated_settings_present() {
		if ( false === get_option( 'rcptn_enable_automated_messaging' ) ) {
			return true;
		}

		return $this->check_settings(
			array(
				'rcptn_automated_message_template',
				'rcptn_alert_post_type',
			)
		);
	}

	/**
	 * Checks an array of options to see if they’re set. Returns false if any one of the
	 * option keys returns false on a `get_option` check.
	 *
	 * @param string[] $setting_keys  The keys of the options to check.
	 *
	 * @return bool
	 */
	private function check_settings( $setting_keys ) {
		// Get the values for each key via `get_option`.
		$setting_values = array_map(
			'get_option',
			$setting_keys
		);

		// Check to see if there’s a false value within the list of values.
		return ! in_array( false, $setting_values, true );
	}
}
