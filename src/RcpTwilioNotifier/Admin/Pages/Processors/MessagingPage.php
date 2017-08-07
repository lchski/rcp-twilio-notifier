<?php
/**
 * RCP: RcpTwilioNotifier\Admin\Pages\Processors\MessagingPage class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin|Pages\Processors
 */

namespace RcpTwilioNotifier\Admin\Pages\Processors;
use RcpTwilioNotifier\Helpers\MemberRetriever;
use RcpTwilioNotifier\Models\Member;
use RcpTwilioNotifier\Models\Region;

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
			$member->send_message( $message );
		}
	}
}
