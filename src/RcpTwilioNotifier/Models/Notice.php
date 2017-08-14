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
	 * @var string
	 */
	private $message;

	/**
	 * Set internal values.
	 *
	 * @param string $type  The notice's type (one of 'error'|'warning'|'success'|'info').
	 * @param string $message  The message to render.
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
	 * @return string
	 */
	public function get_message() {
		return $this->message;
	}

}
