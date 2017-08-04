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
	public $members;

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
		$query_args = array(
			'meta_key'   => 'rcptn_region',
			'meta_value' => $this->slug,
		);

		$member_query = new \WP_User_Query( $query_args );

		return $member_query->get_results();
	}
}
