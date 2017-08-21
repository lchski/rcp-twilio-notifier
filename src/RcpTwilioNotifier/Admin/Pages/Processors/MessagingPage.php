<?php
/**
 * RCP: RcpTwilioNotifier\Admin\Pages\Processors\MessagingPage class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin|Pages\Processors
 */

namespace RcpTwilioNotifier\Admin\Pages\Processors;
use RcpTwilioNotifier\Helpers\MemberRetriever;
use RcpTwilioNotifier\Helpers\MergeTags;
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

		// @TODO: Sanitize these values.
		$this->message_all_in_region( new Region( $this->posted['rcptn_region'] ), $this->posted['rcptn_message'] );
	}

	/**
	 * Message all the members within a region.
	 *
	 * @param Region $region   The region whose members we'll message.
	 * @param string $message  The message to send.
	 */
	private function message_all_in_region( Region $region, $message ) {
		$members = MemberRetriever::get_region_members_and_all_region_subscribers( $region );

		foreach ( $members as $member ) {
			$merge_tag_processor = new MergeTags( $member );
			$merged_message = $merge_tag_processor->replace_tags( $message );

			$sms_request = $member->send_message( $merged_message );

			self::create_notification_on_send( $sms_request, $member );
		}
	}

	/**
	 * Create a notification based on the SMS request.
	 *
	 * @param MessageInstance $sms_response  The SMS API response.
	 * @param Member          $member        The member who was messaged.
	 */
	private static function notify_on_send( $sms_response, $member ) {
		$notifier = Notifier::get_instance();

		if ( $sms_response instanceof \WP_Error ) {
			$notifier->add_notice( new Notice( 'error', $sms_response->get_error_message() ) );
		} elseif ( $sms_response instanceof MessageInstance ) {
			$notifier->add_notice(
				new Notice(
					'success',
					// translators: %1$s is the member’s name, %2$d is their phone number.
					sprintf( __( 'Message successfully sent to %1$s (%2$d).', 'rcptn' ), $member->first_name . ' ' . $member->last_name, $member->get_phone_number() )
				)
			);
		}
	}
}
