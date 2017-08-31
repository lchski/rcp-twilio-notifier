<?php
/**
 * RCP: RcpTwilioNotifier\Admin\Pages\Processors\SingleRecipientMessagingProcessor class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin|Pages\Processors
 */

namespace RcpTwilioNotifier\Admin\Pages\Processors;

/**
 * Processes form submissions from our SingleRecipientMessagingProcessor in the WordPress admin.
 */
class SingleRecipientMessagingProcessor extends AbstractProcessor implements ProcessorInterface {

	/**
	 * The name of the action that this processor processes.
	 *
	 * @var string  Action name.
	 */
	protected $action_name = 'message-single-recipient';

	/**
	 * The name of the nonce that this processor validates.
	 *
	 * @var string  Nonce name.
	 */
	protected $nonce_name = 'rcptn_message_single_recipient_nonce';

	/**
	 * Process!
	 */
	public function process() {
		$validator = new \RcpTwilioNotifier\Admin\Pages\Validators\SingleRecipientMessagingProcessor();
		$validator->init();

		if ( ! $validator->is_valid() ) {
			return false;
		}

		// Message.
		// Check to see whether we had any errors.
		if ( empty( $this->failed_sends ) ) {
			// Everything worked, redirect.
			return;
		}
	}


}
