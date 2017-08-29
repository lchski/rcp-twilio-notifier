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

}
