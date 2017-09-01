<?php
/**
 * RCP: RcpTwilioNotifier\Admin\Pages\Processors\SingleRecipientMessagingProcessor class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin|Pages\Processors
 */

namespace RcpTwilioNotifier\Admin\Pages\Processors;
use RcpTwilioNotifier\Helpers\Notifier;
use RcpTwilioNotifier\Models\Member;
use RcpTwilioNotifier\Models\Message;
use RcpTwilioNotifier\Models\Notice;

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
	 * Whether or not this processor redirects after processing.
	 *
	 * @var bool
	 */
	protected $redirects_after_processing = true;

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
		$recipient = new Member( absint( $this->posted['rcptn_recipient_id'] ) );

		$message = Message::find( absint( $this->posted['rcptn_message_id'] ) );
		$message->send_to_one( $recipient );

		$notifier = Notifier::get_instance();
		$notifier->add_notice(
			new Notice(
				'success',
				sprintf(
					// translators: %1$s is the recipient's full name, %2$s is their phone number.
					 __( 'Attempted to resend the message to %1$s (%2$s).', 'rcptn' ),
					$recipient->first_name . ' ' . $recipient->last_name,
					$recipient->get_phone_number()
				)
			)
		);

		// We’re done! Redirect.
		wp_safe_redirect( get_edit_post_link( absint( $this->posted['rcptn_message_id'] ), '' ) );
	}


}
