<?php
/**
 * RCP: RcpTwilioNotifier\Admin\Pages\Processors\MessagingPage class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin|Pages\Processors
 */

namespace RcpTwilioNotifier\Admin\Pages\Processors;
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

		// Both inputs are valid, so we set them as properties.
		$this->region = new Region( $_POST['rcptn_region'] ); // WPCS: CSRF ok.
		$this->message = $_POST['rcptn_message']; // WPCS: CSRF ok.

		$this->message_all_in_region( $this->region );
	}

	/**
	 * Message all the members within a region.
	 *
	 * @param Region $region  The region whose members we'll message.
	 */
	public function message_all_in_region( Region $region ) {
		// @TODO: pull in members from other regions, who subscrbie to all regions
		$members = $region->get_members();

		foreach ( $members as $member ) {
			$member->send_message( $this->message );
		}
	}
}
