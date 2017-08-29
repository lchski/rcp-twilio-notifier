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
	 * @return Member[]
	 */
	public static function get_region_members_and_all_region_subscribers( Region $region ) {
		$region_members = $region->get_members();

		$all_region_subscribers = self::get_all_region_subscribers();

		return array_unique( array_merge( $region_members, $all_region_subscribers ), SORT_REGULAR );
	}

	/**
	 * Retrieve the members who subscribe to an all-region plan.
	 *
	 * @return Member[]
	 */
	public static function get_all_region_subscribers() {
		// 1: Retrieve the subscription IDs as an array.
		$all_region_subscription_ids = AllRegionSubscriptionIdLister::convert_id_string_to_array( get_option( 'rcptn_rcp_all_regions_subscription_id' ) );

		// 2: Convert the array of IDs into an array of arrays, each containing the WP_Users for that subscription ID.
		$all_region_subscribers = array_map(
			function( $subscription_id ) {
					return rcp_get_members_of_subscription( $subscription_id, 'all' );
			}, $all_region_subscription_ids
		);

		// 3: Merge the array of arrays containing the subscribers.
		$merged_all_region_subscribers = array_merge( ...$all_region_subscribers );

		// 4: Convert the subscribers (WP_Users) to members (RCPTN_Members).
		return self::convert_users_to_members( $merged_all_region_subscribers );
	}

	/**
	 * Retrieve all subscribers.
	 *
	 * @return Member[]
	 */
	public static function get_all_subscribers() {
		// Get all active RCP subscribers.
		$rcp_members = rcp_get_members();

		// Convert the WP_User objects to RCPTN Member objects.
		return self::convert_users_to_members( $rcp_members );
	}

	/**
	 * Converts WP_Users to our custom Member object.
	 *
	 * @param WP_User[] $users  Array of WP_User objects to convert.
	 *
	 * @return Member[]  The WP_Users objects, now converted to \RcpTwilioNotifier\Models\Member objects.
	 */
	public static function convert_users_to_members( $users ) {
		$converter = function( $user ) {
			return new Member( $user->ID );
		};

		return array_map( $converter, $users );
	}

	/**
	 * Converts WP_User IDs to our custom Member object.
	 *
	 * @param int[] $user_ids  Array of WP_User IDs to convert.
	 *
	 * @return Member[]  The WP_Users objects, now converted to \RcpTwilioNotifier\Models\Member objects.
	 */
	public static function convert_user_ids_to_members( $user_ids ) {
		$converter = function( $user_id ) {
			return new Member( $user_id );
		};

		return array_map( $converter, $user_ids );
	}

}
