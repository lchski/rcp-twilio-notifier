<?php
/**
 * RcpTwilioNotifier: RcpTwilioNotifier\Models\Region class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Models
 */

namespace RcpTwilioNotifier\Models;

/**
 * Access information about a region and its members.
 */
class Region {
	/**
	 * The region's slug.
	 *
	 * @var string
	 */
	public $slug;

	/**
	 * The members for the region.
	 *
	 * @var array
	 */
	private $members;

	/**
	 * Set initial state.
	 *
	 * @param string $slug  The region's slug.
	 */
	public function __construct( $slug ) {
		$this->slug = $slug;
	}

	/**
	 * Get the members for the region.
	 *
	 * @return array
	 */
	public function get_members() {
		// If we don’t already have a list of members, query. Otherwise, we skip ahead to avoid the query.
		if ( 0 === count( $this->members ) ) {
			$query_args = array(
				'meta_key'   => 'rcptn_region',
				'meta_value' => $this->slug,
			);

			$this->members = $this->convert_users_to_members( get_users( $query_args ) );
		}

		return apply_filters( 'rcptn_region_get_members', $this->members, $this->slug, $this );
	}

	/**
	 * Converts WP_Users to our custom Member object.
	 *
	 * @param array $users  Array of WP_User objects to convert.
	 *
	 * @return array  The WP_Users objects, now converted to \RcpTwilioNotifier\Models\Member objects.
	 */
	private function convert_users_to_members( $users ) {
		$converter = function( $user ) {
			return new Member( $user->ID );
		};

		return array_map( $converter, $users );
	}
}
