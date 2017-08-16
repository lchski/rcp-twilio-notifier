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
		$this->are_settings_present = $this->are_base_settings_present();
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
				<p>
					<?php
						printf(
							wp_kses(
								// Translators: %s: the href to the settings page; keep it as-is.
								__( 'There are settings missing for the Twilio Region Notifier. Make sure to fill out all the fields on the <a href="%s">settings page</a>. The plugin won’t work properly until you do. (If you’ve just filled out the fields, you can ignore this message.)', 'rcptn' ),
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
			</div>
		<?php
	}

	/**
	 * Check whether the basic settings (the ones that are always required) for the plugin are present.
	 *
	 * @return bool
	 */
	private function are_base_settings_present() {
		$settings_fields = array_map(
			'get_option',
			array(
				'rcptn_twilio_sid',
				'rcptn_twilio_token',
				'rcptn_twilio_from_number',
				'rcptn_rcp_all_regions_subscription_id',
			)
		);

		return ! in_array( false, $settings_fields, true );
	}
}
