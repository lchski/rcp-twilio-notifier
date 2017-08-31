<?php
/**
 * RCP: RcpTwilioNotifier\Admin\MessagePostType\SendAttemptsBox class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin\MessagePostType\MetaBoxes
 */

namespace RcpTwilioNotifier\Admin\MessagePostType\MetaBoxes;

/**
 * Displays the send attempts for the Message.
 */
class SendAttemptsBox extends AbstractMetaBox {

	/**
	 * Set values for use by the parent class.
	 */
	protected function set_parent_values() {
		$this->id    = 'rcptn_send_attempts';
		$this->title = __( 'Send Attempts', 'rcptn' );
	}

	/**
	 * Render the list of send attempts.
	 */
	public function render() {
		?>

		<?php
	}

}
