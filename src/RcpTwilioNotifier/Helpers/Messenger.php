<?php
/**
 * RcpTwilioNotifier: RcpTwilioNotifier\Helpers\Messenger class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Helpers
 */

namespace RcpTwilioNotifier\Helpers;

use RcpTwilioNotifier\Models\MessagingTask;

/**
 * Processes messages in a queue.
 */
class Messenger extends \WP_Background_Process {

	/**
	 * The unique queue name.
	 *
	 * @var string
	 */
	protected $action = 'rcptn_messenger';

	/**
	 * Send the message.
	 *
	 * @param array $messaging_task  The messaging task, in the form output by MessagingTask->convert_to_array.
	 */
	protected function task( $messaging_task ) {
		$messaging_task = MessagingTask::create_from_array( $messaging_task );
		$messaging_task->dispatch();

		return false;
	}

}
