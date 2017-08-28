<?php
/**
 * RCP: RcpTwilioNotifier\Helpers\AllRegionSubscriptionIdLister class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Helpers
 */

namespace RcpTwilioNotifier\Helpers;

/**
 * Helper functions to manage lists of all region subscription IDs.
 */
class AllRegionSubscriptionIdLister {

	/**
	 * Convert a string containing IDs into an array, removing whitespace and commas.
	 *
	 * @param string $id_string  String of IDs.
	 *
	 * @return array
	 */
	public static function convert_id_string_to_array( $id_string ) {
		$without_whitespace = preg_replace( '/ /', '', $id_string );

		return preg_split( '/,/', $without_whitespace );
	}

}
