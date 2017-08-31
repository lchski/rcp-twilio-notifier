<?php
/**
 * RCP: RcpTwilioNotifier\Admin\MessagePostType\MessageBodyBox class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin\MessagePostType
 */

namespace RcpTwilioNotifier\Admin\MessagePostType\MetaBoxes;
use RcpTwilioNotifier\Helpers\Renderers\AdminFormField;

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
			<table class="form-table">
				<tbody>
					<?php
						AdminFormField::render(
							'rcptn_message_content',
							__( 'Message', 'rcptn' ),
							'',
							function( $field_id ) {
								?>
								<textarea name="<?php echo esc_attr( $field_id ); ?>" id="<?php echo esc_attr( $field_id ); ?>" style="width: 100%;" rows="4" disabled><?php echo esc_html( $this->message->get_message_body()->get_raw_body() ); ?></textarea>
								<?php
							},
							array(
								'required' => false,
							)
						);
					?>
				</tbody>
			</table>
		<?php
	}

}
