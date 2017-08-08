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
		 * Settings verification
		 */
		$settings_verifier = new Admin\SettingsVerification();
		$settings_verifier->init();
		$settings_verifier->remind_if_settings_not_present();

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

		$phone_number_registration_field = new Admin\MemberFields\PhoneNumber\Registration();
		$phone_number_registration_field->init();

		$phone_number_edit_member_field = new Admin\MemberFields\PhoneNumber\EditMember();
		$phone_number_edit_member_field->init();
	}

}
