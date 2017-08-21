<?php
/**
 * RcpTwilioNotifier: RcpTwilioNotifier\Models\Member class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Models
 */

namespace RcpTwilioNotifier\Models;
use Twilio\Exceptions\TwilioException;
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
	 * Get whether or not the member has been welcomed.
	 *
	 * @return bool
	 */
	public function has_member_been_welcomed() {
		$welcomed_status = get_user_meta( $this->ID, 'rcptn_welcomed_status', true );

		return apply_filters( 'rcptn_member_has_member_been_welcomed', $welcomed_status, $this->ID, $this );
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
			get_option( 'rcptn_twilio_sid', getenv( 'RCPTN_TWILIO_SID' ) ),
			get_option( 'rcptn_twilio_token', getenv( 'RCPTN_TWILIO_TOKEN' ) )
		);

		try {
			$sms = $twilio_client->messages->create(
				$this->get_phone_number(),
				array(
					'from' => get_option( 'rcptn_twilio_from_number', getenv( 'RCPTN_TWILIO_FROM_NUMBER' ) ),
					'body' => $message,
				)
			);
		} catch ( TwilioException $e ) {
			$sms = new \WP_Error(
				'rcptn_failed_sms',
				// translators: %1$s is the Twilio SDK exception message, %2$d is the exception code.
				sprintf( __( 'Twilio failed to send a message. (Twilio error: %1$s (%2$d))', 'rcptn' ), $e->getMessage(), $e->getCode() ),
				$e
			);
		}

		return $sms;
	}

}
