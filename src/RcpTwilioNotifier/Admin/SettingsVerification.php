<?php
/**
 * RCP: RcpTwilioNotifier\Admin\SettingsVerification class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin
 */

namespace RcpTwilioNotifier\Admin;

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
		$settings_field_keys = array(
			'rcptn_twilio_sid',
			'rcptn_twilio_token',
			'rcptn_twilio_from_number',
			'rcptn_rcp_all_regions_subscription_id',
		);

		$are_settings_present = true;

		foreach ( $settings_field_keys as $field_key ) {
			$option_status = get_option( $field_key );

			if ( false === $option_status ) {
				$are_settings_present = false;
			}
		}

		$this->are_settings_present = $are_settings_present;
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
			add_action( 'admin_notices', array( $this, 'render_settings_nag' ) );
		}
	}

	/**
	 * Render the admin notice for missing settings.
	 */
	public function render_settings_nag() {
		?>
			<div class="error">
				<p><?php echo esc_html_e( 'There are settings missing for the Twilio Region Notifier. Make sure to fill out all the fields on the settings page. The plugin won’t work properly until you do. (If you’ve just filled out the fields, you can ignore this message.)', 'rcptn' ); ?> </p>
			</div>
		<?php
	}
}
