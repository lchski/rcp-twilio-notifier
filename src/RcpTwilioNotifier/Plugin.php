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
	 * Let's go!
	 */
	public function load() {
		// Set up the regions with our defaults, filtered for customization.
		$regions = apply_filters(
			'rcptn_regions', array(
				array(
					'key' => 'south-east',
					'label' => __( 'South East', 'rcptn' ),
				),
				array(
					'key' => 'north-east',
					'label' => __( 'North East', 'rcptn' ),
				),
				array(
					'key' => 'new-england',
					'label' => __( 'New England', 'rcptn' ),
				),
				array(
					'key' => 'mid-west',
					'label' => __( 'Mid West', 'rcptn' ),
				),
				array(
					'key' => 'west-coast',
					'label' => __( 'West Coast', 'rcptn' ),
				),
			)
		);

		$region_registration_field = new RegionField\Registration( $regions );
		$region_registration_field->init();

		$region_edit_member_field = new RegionField\EditMember( $regions );
		$region_edit_member_field->init();

		$admin_messaging_page = new Admin\MessagingPage( $regions );
		$admin_messaging_page->init();
	}

}
