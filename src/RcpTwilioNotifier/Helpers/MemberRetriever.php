<?php
/**
 * RCP: RcpTwilioNotifier\Helpers\MemberRetriever class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Helpers
 */

namespace RcpTwilioNotifier\Helpers;

use RcpTwilioNotifier\Models\Region;
use RcpTwilioNotifier\Models\Member;

/**
 * Various methods to help gather and manage collections of our Member object.
 */
class MemberRetriever {

	/**
	 * Retrieve a region’s members, combined with subscribers at the "all regions" subscription level.
	 * This is the standard group of people to be messaged.
	 *
	 * @param Region $region  The region whose members to retrieve.
	 *
	 * @return array
	 */
	public static function get_region_members_and_all_region_subscribers( Region $region ) {
		$region_members = $region->get_members();

		$all_region_subscribers = self::convert_users_to_members(
			rcp_get_members_of_subscription(
				get_option( 'rcptn_rcp_all_regions_subscription_id', getenv( 'RCPTN_RCP_ALL_REGIONS_SUBSCRIPTION_ID' ) ),
				'all'
			)
		);

		// @TODO: Improve this uniqueness check; it may fail sometimes.
		return array_unique( array_merge( $region_members, $all_region_subscribers ), SORT_REGULAR );
	}

	/**
	 * Converts WP_Users to our custom Member object.
	 *
	 * @param array $users  Array of WP_User objects to convert.
	 *
	 * @return array  The WP_Users objects, now converted to \RcpTwilioNotifier\Models\Member objects.
	 */
	public static function convert_users_to_members( $users ) {
		$converter = function( $user ) {
			return new Member( $user->ID );
		};

		return array_map( $converter, $users );
	}

}
