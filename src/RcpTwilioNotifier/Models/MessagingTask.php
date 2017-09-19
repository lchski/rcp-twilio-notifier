<?php
/**
 * RcpTwilioNotifier: RcpTwilioNotifier\Models\MessagingTask class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Models
 */

namespace RcpTwilioNotifier\Models;

/**
 * A queued message.
 */
class MessagingTask {

	/**
	 * The Message to send.
	 *
	 * @var Message
	 */
	private $message;

	/**
	 * The recipient of this message.
	 *
	 * @var Member
	 */
	private $recipient;

	/**
	 * Set internal values.
	 *
	 * @param int $message_id    The ID of the Message to send.
	 * @param int $recipient_id  The ID of the Member to send the message to.
	 */
	public function __construct( $message_id, $recipient_id ) {
		$this->message = Message::find( $message_id );

		$this->recipient = new Member( $recipient_id );
	}

	/**
	 * Attempt to send the message.
	 */
	public function dispatch() {
		// Bail if we can't find the message.
		if ( is_wp_error( $this->message ) ) {
			return;
		}

		$this->message->send_to_one( $this->recipient );
	}

	/**
	 * Convert from object to array.
	 *
	 * @return array
	 */
	public function convert_to_array() {
		return array(
			'message_id'   => $this->message->get_id(),
			'recipient_id' => $this->recipient->ID,
		);
	}

	/**
	 * Create object from the array format output by `convert_to_array`.
	 *
	 * @param array $messaging_task  The messaging task output by convert_to_array.
	 *
	 * @return MessagingTask
	 */
	public static function create_from_array( $messaging_task ) {
		return new self(
			$messaging_task['message_id'],
			$messaging_task['recipient_id']
		);
	}

}
