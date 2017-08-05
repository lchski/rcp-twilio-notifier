<?php
/**
 * RCP: RcpTwilioNotifier\Admin\Processors\MessagingPage class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin|Processors
 */

namespace RcpTwilioNotifier\Admin\Processors;
use RcpTwilioNotifier\Helpers\Validators\MessageBody;
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
	 * List of regions available for messaging.
	 *
	 * @var array
	 */
	private $regions;

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
		$this->validate();
	}

	/**
	 * Validate each of the submitted inputs, setting them as properties if valid.
	 */
	private function validate() {
		if ( $this->validate_region() ) {
			$this->region = new Region( $_POST['rcptn_region'] ); // WPCS: CSRF ok.
		}

		if ( $this->validate_message() ) {
			$this->message = $_POST['rcptn_message']; // WPCS: CSRF ok.
		}
	}

	/**
	 * Validate the region input.
	 *
	 * @return bool
	 */
	private function validate_region() {
		if ( ! isset( $_POST['rcptn_region'] ) ) { // WPCS: CSRF ok.
			$this->error_out( 'No region set.' );
		}

		$region_validator = new \RcpTwilioNotifier\Helpers\Validators\Region( $this->regions );

		if ( ! $region_validator->is_valid_region( $_POST['rcptn_region'] ) ) { // WPCS: CSRF ok.
			$this->error_out( 'Invalid region set.' );
		}

		return true;
	}

	/**
	 * Validate the message input.
	 *
	 * @return bool
	 */
	private function validate_message() {
		if ( ! isset( $_POST['rcptn_message'] ) ) { // WPCS: CSRF ok.
			$this->error_out( 'No message set.' );
		}

		if ( ! MessageBody::is_valid_message_body( $_POST['rcptn_message'] ) ) { // WPCS: CSRF ok.
			$this->error_out( 'Invalid message body.' );
		}

		return true;
	}

	/**
	 * Shuts down the process with an error message.
	 *
	 * @param string $error_message  The error message to display.
	 */
	private function error_out( $error_message ) {
		// @TODO: Write error out code.
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
			$this->message_member( $member );
		}
	}

	/**
	 * Message a given member, checking first that they’re eligible to receive messages.
	 *
	 * @param Member $member  The member to message.
	 *
	 * @return \Twilio\Rest\Api\V2010\Account\MessageInstance|void
	 */
	public function message_member( Member $member ) {
		if ( ! $member->is_active() ) {
			return;
		}

		return $member->send_message( $this->message );
	}

}
