<?php
/**
 * RcpTwilioNotifier: RcpTwilioNotifier_Plugin class
 *
 * The RcpTwilioNotifier_Plugin class runs top-level functionality for the RcpTwilioNotifier plugin. It brings together
 * a variety of classes to run RcpTwilioNotifier.
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier
 */

/**
 * Class RcpTwilioNotifier_Plugin
 */
class RcpTwilioNotifier_Plugin {

	/**
	 * Let's go!
	 */
	public function load() {
		$region_registration_field = new RcpTwilioNotifier_RegionRegistrationField();
		$region_registration_field->init();

		$region_edit_member_field = new RcpTwilioNotifier_RegionEditMemberField();
		$region_edit_member_field->init();
	}

}
