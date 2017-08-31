<?php
/**
 * RcpTwilioNotifier: RcpTwilioNotifier\Models\Member class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Models
 */

namespace RcpTwilioNotifier\Models;
use RcpTwilioNotifier\Helpers\MergeTags;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

/**
 * Used to access plugin-specific metadata.
 */
class Member extends \RCP_Member {

	/**
	 * Provide a unique representation of the given RCPTN Member.
	 *
	 * @return string
	 */
	public function __toString() {
		return (string) $this->ID;
	}

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
	 * Get the member's phone country code.
	 *
	 * @return string
	 */
	public function get_phone_country_code() {
		$phone_country_code = get_user_meta( $this->ID, 'rcptn_phone_country_code', true );

		if ( '' === $phone_country_code ) {
			$phone_country_code = 'US';
		}

		return apply_filters( 'rcptn_member_get_phone_country_code', $phone_country_code, $this->ID, $this );
	}

	/**
	 * Get the member's phone number, formatted for API calls.
	 *
	 * @return string
	 */
	public function get_formatted_phone_number() {
		$phone_util = \libphonenumber\PhoneNumberUtil::getInstance();

		try {
			$phone_number_proto = $phone_util->parse( $this->get_phone_number(), $this->get_phone_country_code() );

			$formatted_phone_number = $phone_util->format( $phone_number_proto, \libphonenumber\PhoneNumberFormat::E164 );

			return apply_filters( 'rcptn_member_get_formatted_phone_number', $formatted_phone_number, $this->ID, $this );
		} catch ( \libphonenumber\NumberParseException $e ) {
			return new \WP_Error(
				'rcptn_libphonenumber_format', 'RCPTN Exception: "' . $e->getMessage() . '" (libphonenumber NumberParseException: ' . $e->getCode() . ')', array(
					'exception' => $e,
				)
			);
		}
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
	 * @param MessageBody $message  Message to send to the member.
	 *
	 * @return \Twilio\Rest\Api\V2010\Account\MessageInstance|void|\WP_Error
	 */
	public function send_message( $message ) {
		// Verify that the member is active according to RCP.
		if ( ! $this->is_active() ) {
			return;
		}

		$merge_tag_processor = new MergeTags( $this, $message->get_body_data() );
		$merged_message = $merge_tag_processor->replace_tags( $message->get_raw_body() );

		$twilio_client = new Client(
			get_option( 'rcptn_twilio_sid' ),
			get_option( 'rcptn_twilio_token' )
		);

		try {
			$sms = $twilio_client->messages->create(
				$this->get_formatted_phone_number(),
				array(
					'from' => get_option( 'rcptn_twilio_from_number' ),
					'body' => $merged_message,
				)
			);
		} catch ( TwilioException $e ) {
			$sms = new \WP_Error(
				'rcptn_failed_sms',
				// translators: %1$s is the Twilio SDK exception message, %2$d is the exception code.
				sprintf( __( 'Twilio error: %1$s (%2$d)', 'rcptn' ), $e->getMessage(), $e->getCode() ),
				$e
			);
		}

		return $sms;
	}

}
