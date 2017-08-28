<?php
/**
 * RCP: RcpTwilioNotifier\Admin\Pages\Processors\MessagingPage class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin|Pages\Processors
 */

namespace RcpTwilioNotifier\Admin\Pages\Processors;
use RcpTwilioNotifier\Helpers\MemberRetriever;
use RcpTwilioNotifier\Helpers\Notifier;
use RcpTwilioNotifier\Models\Member;
use RcpTwilioNotifier\Models\Notice;
use RcpTwilioNotifier\Models\Region;
use Twilio\Rest\Api\V2010\Account\MessageInstance;

/**
 * Processes form submissions from our MessagingPage in the WordPress admin.
 */
class MessagingPage extends AbstractProcessor implements ProcessorInterface {

	/**
	 * The name of the action that this processor processes.
	 *
	 * @var string  Action name.
	 */
	protected $action_name = 'send-single-message';

	/**
	 * The name of the nonce that this processor validates.
	 *
	 * @var string  Nonce name.
	 */
	protected $nonce_name = 'rcptn_send_single_message_nonce';

	/**
	 * List of members that we failed to send to.
	 *
	 * @var int[]
	 */
	protected $failed_sends = array();

	/**
	 * Set internal values.
	 *
	 * @param array $regions  Region list.
	 */
	public function __construct( $regions ) {
		$this->regions = $regions;
	}

	/**
	 * Process!
	 */
	public function process() {
		$validator = new \RcpTwilioNotifier\Admin\Pages\Validators\MessagingPage( $this->regions );
		$validator->init();

		if ( ! $validator->is_valid() ) {
			return false;
		}

		// Check whether to message all regions or just one.
		if ( 'all' === $this->posted['rcptn_region'] ) {
			$this->message_all_regions( sanitize_textarea_field( $this->posted['rcptn_message'] ) );
		} else {
			$this->message_all_in_region(
				new Region( sanitize_key( $this->posted['rcptn_region'] ) ),
				sanitize_textarea_field( $this->posted['rcptn_message'] )
			);
		}

		// Check to see whether we had any errors.
		if ( empty( $this->failed_sends ) ) {
			// Everything worked, clear the message page.
			$_POST = array();
		}
	}

	/**
	 * Message the members of all regions.
	 *
	 * @param string $message  The message to send.
	 */
	private function message_all_regions( $message ) {
		$members = MemberRetriever::get_all_subscribers();

		$this->message_members( $members, $message );
	}

	/**
	 * Message all the members within a region.
	 *
	 * @param Region $region   The region whose members we'll message.
	 * @param string $message  The message to send.
	 */
	private function message_all_in_region( Region $region, $message ) {
		$members = MemberRetriever::get_region_members_and_all_region_subscribers( $region );

		$this->message_members( $members, $message );
	}

	/**
	 * Message given members a given message.
	 *
	 * @param Member[] $members  The members to message.
	 * @param string   $message  The message to send.
	 */
	private function message_members( $members, $message ) {
		foreach ( $members as $member ) {
			$sms_request = $member->send_message( $message );

			$this->notify_on_send( $sms_request, $member );
		}
	}

	/**
	 * Create a notification based on the SMS request.
	 *
	 * @param MessageInstance $sms_response  The SMS API response.
	 * @param Member          $member        The member who was messaged.
	 */
	private function notify_on_send( $sms_response, $member ) {
		$notifier = Notifier::get_instance();

		if ( $sms_response instanceof \WP_Error ) {
			$notifier->add_notice( new Notice( 'error', $sms_response->get_error_message() ) );

			$this->failed_sends[] = $member->ID;
		} elseif ( $sms_response instanceof MessageInstance ) {
			$notifier->add_notice(
				new Notice(
					'success',
					// translators: %1$s is the member’s name, %2$d is their phone number.
					sprintf( __( 'Message successfully sent to %1$s (%2$s).', 'rcptn' ), $member->first_name . ' ' . $member->last_name, $member->get_phone_number() )
				)
			);
		}
	}
}
