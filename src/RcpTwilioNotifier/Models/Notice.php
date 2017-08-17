<?php
/**
 * RcpTwilioNotifier: RcpTwilioNotifier\Models\Notice class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Models
 */

namespace RcpTwilioNotifier\Models;

/**
 * An admin notice.
 */
class Notice {

	/**
	 * The type of the notice.
	 *
	 * @var 'error'|'warning'|'success'|'info'
	 */
	private $type;

	/**
	 * The notice message.
	 *
	 * @var mixed
	 */
	private $message;

	/**
	 * Set internal values.
	 *
	 * @param string $type  The notice's type (one of 'error'|'warning'|'success'|'info').
	 * @param mixed  $message  The message to render.
	 */
	public function __construct( $type, $message ) {
		$this->type    = $type;
		$this->message = $message;
	}

	/**
	 * Get the notice type.
	 *
	 * @return string
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * Get the notice message.
	 *
	 * @return mixed
	 */
	public function get_message() {
		return $this->message;
	}

}
