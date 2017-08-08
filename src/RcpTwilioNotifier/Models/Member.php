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
		$phone_number = get_user_meta( $this->ID, 'rcptn_phone_number', true );

		return apply_filters( 'rcptn_member_get_phone_number', $phone_number, $this->ID, $this );
	}

	/**
	 * Message the member's phone number.
	 *
	 * @param string $message  Message to send to the member.
	 */
	public function send_message( $message ) {
		// Verify that the member is active according to RCP.
		if ( ! $this->is_active() ) {
			return;
		}

		$twilio_client = new Client(
			get_option( 'rcptn_twilio_sid', getenv( 'TWILIO_SID' ) ),
			get_option( 'rcptn_twilio_token', getenv( 'TWILIO_TOKEN' ) )
		);

		$sms = $twilio_client->messages->create(
			$this->get_phone_number(),
			array(
				'from' => get_option( 'rcptn_twilio_from_number', getenv( 'TWILIO_FROM_NUMBER' ) ),
				'body' => $message,
			)
		);

		return $sms;
	}

}
