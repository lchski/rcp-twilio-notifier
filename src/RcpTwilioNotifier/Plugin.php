<?php
/**
 * RcpTwilioNotifier: RcpTwilioNotifier\Plugin class
 *
 * The RcpTwilioNotifier\Plugin class runs top-level functionality for the RcpTwilioNotifier plugin. It brings together
 * a variety of classes to run RcpTwilioNotifier.
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier
 */

namespace RcpTwilioNotifier;

/**
 * Class RcpTwilioNotifier\Plugin
 */
class Plugin {

	/**
	 * Set internal values.
	 */
	public function __construct() {
		// Set up the regions with our defaults, filtered for customization.
		$this->regions = apply_filters(
			'rcptn_regions', array(
				array(
					'slug' => 'south-east',
					'label' => __( 'South East', 'rcptn' ),
				),
				array(
					'slug' => 'north-east',
					'label' => __( 'North East', 'rcptn' ),
				),
				array(
					'slug' => 'new-england',
					'label' => __( 'New England', 'rcptn' ),
				),
				array(
					'slug' => 'mid-west',
					'label' => __( 'Mid West', 'rcptn' ),
				),
				array(
					'slug' => 'west-coast',
					'label' => __( 'West Coast', 'rcptn' ),
				),
			)
		);
	}

	/**
	 * Let's go!
	 */
	public function load() {
		/**
		 * Basic setup
		 */
		// Initialize the notifier, to make sure it runs on every request.
		$notifier = Helpers\Notifier::get_instance();

		// Verify required settings are set.
		$settings_verifier = new Admin\SettingsVerification();
		$settings_verifier->init();
		$settings_verifier->remind_if_settings_not_present();

		// Load Message post type.
		$message_post_type_registrar = new Admin\MessagePostType\Registrar();
		$message_post_type_registrar->init();

		/**
		 * Admin pages and their processors
		 */
		$admin_messaging_page = new Admin\Pages\MessagingPage( $this->regions );
		$admin_messaging_page->init();

		$admin_messaging_page_processor = new Admin\Pages\Processors\MessagingPage( $this->regions );
		$admin_messaging_page_processor->init();

		$admin_settings_page = new Admin\Pages\SettingsPage();
		$admin_settings_page->init();

		$admin_settings_page_processor = new Admin\Pages\Processors\SettingsPage();
		$admin_settings_page_processor->init();

		/**
		 * Member fields
		 */
		$region_registration_field = new Admin\MemberFields\Region\Registration( $this->regions );
		$region_registration_field->init();

		$region_edit_member_field = new Admin\MemberFields\Region\EditMember( $this->regions );
		$region_edit_member_field->init();

		$phone_country_registration_field = new Admin\MemberFields\PhoneCountry\Registration();
		$phone_country_registration_field->init();

		$phone_country_edit_member_field = new Admin\MemberFields\PhoneCountry\EditMember();
		$phone_country_edit_member_field->init();

		$phone_number_registration_field = new Admin\MemberFields\PhoneNumber\Registration();
		$phone_number_registration_field->init();

		$phone_number_edit_member_field = new Admin\MemberFields\PhoneNumber\EditMember();
		$phone_number_edit_member_field->init();

		/**
		 * Handlers to enable all regions add-on functionality.
		 */
		$subscription_addon_field = new Admin\SubscriptionField();
		$subscription_addon_field->init();

		$registration_javascript_supporter = new Admin\RegistrationJavascriptSupporter();
		$registration_javascript_supporter->init();

		/**
		 * Welcome message
		 */
		if ( false !== get_option( 'rcptn_welcome_message' ) ) {
			$registration_watcher = new Admin\RegistrationWatcher();
			$registration_watcher->init();
		}

		/**
		 * One-click messaging features
		 */
		if ( false !== get_option( 'rcptn_enable_automated_messaging' ) ) {
			$alert_watcher = new Admin\AlertWatcher( $this->regions );
			$alert_watcher->init();
		}
	}

}
