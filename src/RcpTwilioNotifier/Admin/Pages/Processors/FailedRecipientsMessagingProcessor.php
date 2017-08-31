<?php
/**
 * RCP: RcpTwilioNotifier\Admin\Pages\Processors\FailedRecipientsMessagingProcessor class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin|Pages\Processors
 */

namespace RcpTwilioNotifier\Admin\Pages\Processors;
use RcpTwilioNotifier\Helpers\Notifier;
use RcpTwilioNotifier\Models\Message;
use RcpTwilioNotifier\Models\Notice;

/**
 * Processes form submissions from our FailedRecipientsMessagingProcessor in the WordPress admin.
 */
class FailedRecipientsMessagingProcessor extends AbstractProcessor implements ProcessorInterface {

	/**
	 * The name of the action that this processor processes.
	 *
	 * @var string  Action name.
	 */
	protected $action_name = 'message-failed-recipients';

	/**
	 * The name of the nonce that this processor validates.
	 *
	 * @var string  Nonce name.
	 */
	protected $nonce_name = 'rcptn_message_failed_recipients_nonce';

	/**
	 * Whether or not this processor redirects after processing.
	 *
	 * @var bool
	 */
	protected $redirects_after_processing = true;

	/**
	 * Process!
	 */
	public function process() {
		$validator = new \RcpTwilioNotifier\Admin\Pages\Validators\FailedRecipientsMessagingProcessor();
		$validator->init();

		if ( ! $validator->is_valid() ) {
			return false;
		}

		// Message.
		$message = Message::find( absint( $this->posted['rcptn_message_id'] ) );
		$message->retry_failed_sends();

		$notifier = Notifier::get_instance();
		$notifier->add_notice(
			new Notice(
				'success',
				__( 'Attempted to resend previously failed messages.', 'rcptn' )
			)
		);

		// We’re done! Redirect.
		wp_safe_redirect( get_edit_post_link( absint( $this->posted['rcptn_message_id'] ), '' ) );
	}


}
