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
	 * A unique ID for the notice.
	 *
	 * @var string
	 */
	public $ID;

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
	 * @param array $args {
	 *     Required. The values for the notice.
	 *
	 *     @type string $ID       A unique identifier for the notice.
	 *     @type string $type     The notice's type (one of 'error'|'warning'|'success'|'info').
	 *     @type mixed  $message  The message to render.
	 * }
	 */
	public function __construct( $args ) {
		$this->ID      = $args['ID'];
		$this->type    = $args['type'];
		$this->message = $args['message'];
	}

	/**
	 * Provide a unique string representation of the notice.
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->ID;
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
