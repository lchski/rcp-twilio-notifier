<?php
/**
 * RCP: RcpTwilioNotifier\Helpers\MessagingQueue class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Helpers
 */

namespace RcpTwilioNotifier\Helpers;

/**
 * Get a unique instance of the Messenger background processing queue.
 */
class MessagingQueue {

	/**
	 * Unique instance of the Messenger.
	 *
	 * @var Messenger
	 */
	private static $instance;

	/**
	 * Return the unique Messenger instance.
	 *
	 * @return Messenger
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new Messenger();
		}

		return self::$instance;
	}

}
