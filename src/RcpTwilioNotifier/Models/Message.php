<?php
/**
 * RcpTwilioNotifier: RcpTwilioNotifier\Models\Message class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Models
 */

namespace RcpTwilioNotifier\Models;

/**
 * A message sent to members.
 */
class Message {

	/**
	 * The recipients of this message.
	 *
	 * @var Member[]
	 */
	private $recipients;

	/**
	 * The unprocessed body of the message.
	 *
	 * @var string
	 */
	private $raw_body;

	/**
	 * Additional data to include when processing the message.
	 *
	 * @var array
	 */
	private $body_data;

	/**
	 * Attempted sends for each recipient, including retries.
	 *
	 * @var array
	 */
	private $send_attempts;

	/**
	 * Create a new Message.
	 *
	 * @param array $args {
	 *     Required. The data necessary to create a new Message.
	 *
	 *     @type Member[] $recipients  Recipients of the message.
	 *     @type string   $raw_body    The unprocessed message body.
	 *     @type array    $body_data   Data required to process the message body.
	 * }
	 *
	 * @return Message
	 */
	public static function create( $args ) {
		return new Message();
	}

}
