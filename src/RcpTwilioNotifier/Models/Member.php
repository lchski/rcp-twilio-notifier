<?php
/**
 * RcpTwilioNotifier: RcpTwilioNotifier\Models\Member class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Models
 */

namespace RcpTwilioNotifier\Models;
use Twilio\Rest\Client;

/**
 * Used to access plugin-specific metadata.
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

	/**
	 * Get the member's phone number.
	 *
	 * @return string
	 */
	public function get_phone_number() {
		return '+15005550000';
	}

	/**
	 * Message the member's phone number.
	 *
	 * @param string $message  Message to send to the member.
	 */
	public function send_message( $message ) {
		$twilio_client = new Client( getenv( 'TWILIO_SID' ), getenv( 'TWILIO_TOKEN' ) );

		$sms = $twilio_client->messages->create(
			$this->get_phone_number(),
			array(
				'from' => '+15005550006', // dummy SMS, validates via Twilio.
				'body' => $message,
			)
		);

		return $sms;
	}

}
