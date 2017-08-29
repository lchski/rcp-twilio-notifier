<?php
/**
 * RcpTwilioNotifier: RcpTwilioNotifier\Models\MessageBody class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Models
 */

namespace RcpTwilioNotifier\Models;

/**
 * An SMS message body.
 */
class MessageBody {

	/**
	 * The message, unprocessed.
	 *
	 * @var string
	 */
	private $raw_body;

	/**
	 * Additional data required to process the body.
	 *
	 * @var array
	 */
	private $body_data;

	/**
	 * Set internal values.
	 *
	 * @param string $raw_body   The message.
	 * @param array  $body_data  Data to process the body.
	 */
	public function __construct( $raw_body, $body_data = array() ) {
		$this->raw_body  = $raw_body;
		$this->body_data = $body_data;
	}

	/**
	 * Get the unprocessed body.
	 *
	 * @return string
	 */
	public function get_raw_body() {
		return $this->raw_body;
	}

	/**
	 * Get the body data.
	 *
	 * @return array
	 */
	public function get_body_data() {
		return $this->body_data;
	}

}
