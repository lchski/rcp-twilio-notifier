<?php
/**
 * RCP: RcpTwilioNotifier\Admin\MessagePostType\MessageBodyBox class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin\MessagePostType
 */

namespace RcpTwilioNotifier\Admin\MessagePostType\MetaBoxes;

/**
 * Displays the body of the Message.
 */
class MessageBodyBox extends AbstractMetaBox {

	/**
	 * Set values for use by the parent class.
	 */
	protected function set_parent_values() {
		$this->id    = 'rcptn_message_body';
		$this->title = __( 'Message Body', 'rcptn' );
	}

	/**
	 * Render the body.
	 */
	public function render() {
		?>
			<textarea name="rcptn_message_body" id="rcptn_message_body" style="width: 100%;" rows="4" disabled><?php echo esc_html( $this->message->get_message_body()->get_raw_body() ); ?></textarea>
		<?php
	}

}
