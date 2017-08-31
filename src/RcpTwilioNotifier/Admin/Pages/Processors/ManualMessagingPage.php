<?php
/**
 * RCP: RcpTwilioNotifier\Admin\Pages\Processors\ManualMessagingPage class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin|Pages\Processors
 */

namespace RcpTwilioNotifier\Admin\Pages\Processors;
use RcpTwilioNotifier\Helpers\MemberRetriever;
use RcpTwilioNotifier\Helpers\Notifier;
use RcpTwilioNotifier\Models\Member;
use RcpTwilioNotifier\Models\Message;
use RcpTwilioNotifier\Models\Notice;
use RcpTwilioNotifier\Models\Region;

/**
 * Processes form submissions from our ManualMessagingPage in the WordPress admin.
 */
class ManualMessagingPage extends AbstractProcessor implements ProcessorInterface {

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
	 * Whether or not this processor redirects after processing.
	 *
	 * @var bool
	 */
	protected $redirects_after_processing = true;

	/**
	 * The message created by this form submission.
	 *
	 * @var Message
	 */
	private $message;

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
		$validator = new \RcpTwilioNotifier\Admin\Pages\Validators\ManualMessagingPage( $this->regions );
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

		// Spread the good news!
		$notifier = Notifier::get_instance();
		$notifier->add_notice(
			new Notice(
				'success',
				__( 'Queued the message.', 'rcptn' )
			)
		);

		// We’re done! Redirect.
		wp_safe_redirect( get_edit_post_link( $this->message->get_id(), '' ) );
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
	 * @param Member[] $members       The members to message.
	 * @param string   $message_body  The message to send.
	 */
	private function message_members( $members, $message_body ) {
		$this->message = Message::create(
			array(
				'recipients' => $members,
				'raw_body'   => $message_body,
				'body_data'  => array(
					'post_ID' => ( isset( $this->posted['rcptn_extra_data']['post_ID'] ) ) ? $this->posted['rcptn_extra_data']['post_ID'] : null,
				),
			)
		);

		$this->message->send_to_all();
	}

}
