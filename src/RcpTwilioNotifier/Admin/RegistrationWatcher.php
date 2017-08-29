<?php
/**
 * RCP: RcpTwilioNotifier\Admin\RegistrationWatcher class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin
 */

namespace RcpTwilioNotifier\Admin;
use RcpTwilioNotifier\Models\Member;
use RcpTwilioNotifier\Models\MessageBody;
use Twilio\Rest\Api\V2010\Account\MessageInstance;

/**
 * Watches for the registration of new members, sending them a welcome message.
 */
class RegistrationWatcher {

	/**
	 * The welcome message.
	 *
	 * @var string
	 */
	private $welcome_message;

	/**
	 * Set internal values.
	 */
	public function __construct() {
		$this->welcome_message = get_option( 'rcptn_welcome_message' );
	}

	/**
	 * Set up the watcher.
	 */
	public function init() {
		// Bail if we don’t have a welcome message.
		if ( false === $this->welcome_message ) {
			return;
		}

		add_action( 'rcp_successful_registration', array( $this, 'send_welcome_message' ) );
	}

	/**
	 * Add the notification with the one-click messaging interface.
	 *
	 * @param \RCP_Member $rcp_member  The just-registered member.
	 */
	public function send_welcome_message( \RCP_Member $rcp_member ) {
		// Convert the RCP member object to the RCPTN member object.
		$member = new Member( $rcp_member->ID );

		// Check if the member has already been welcomed.
		if ( $member->has_member_been_welcomed() ) {
			return;
		}

		$sms_request = $member->send_message( new MessageBody( $this->welcome_message ) );

		if ( $sms_request instanceof \WP_Error ) {
			// @TODO: Log the $sms_request response if it’s unsuccessful.
			return;
		} elseif ( $sms_request instanceof MessageInstance ) {
			update_user_meta( $member->ID, 'rcptn_member_has_member_been_welcomed', true );
		}

	}

}
