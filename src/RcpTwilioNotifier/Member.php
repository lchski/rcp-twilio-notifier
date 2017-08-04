<?php
/**
 * RcpTwilioNotifier: RcpTwilioNotifier\Member class
 *
 * Used to access plugin-specific metadata.
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier
 */

namespace RcpTwilioNotifier;

/**
 * RcpTwilioNotifier\Member class
 */
class Member extends \RCP_Member {

	/**
	 * Retrieve the member's home region, optionally filtered by `rcptn_member_get_home_region`.
	 *
	 * @return mixed
	 */
	public function get_home_region() {
		$region = get_user_meta( $this->ID, 'rcptn_region', true );

		return apply_filters( 'rcptn_member_get_home_region', $region, $this->ID, $this );
	}

}
