<?php
/**
 * RCP: RcpTwilioNotifier\Helpers\Renderers\AdminFormField class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Helpers\Renderers
 */

namespace RcpTwilioNotifier\Helpers\Renderers;

/**
 * Render a UI with messaging fields.
 */
class MessagingUi {

	/**
	 * Render the entire form.
	 *
	 * @param RegionSelect $region_renderer  A region renderer.
	 * @param string       $message_value    A value for the messaging textarea.
	 */
	public static function render( RegionSelect $region_renderer, $message_value = '' ) {
		?>
			<form id="rcptn-region-notifier-messenger" action="" method="post">
				<?php
					self::render_form( $region_renderer, $message_value );
				?>
				<p class="submit">
					<input type="hidden" name="rcptn-action" value="send-single-message"/>
					<input type="submit" value="<?php esc_attr_e( 'Send Message', 'rcptn' ); ?>" class="button-primary"/>
				</p>
				<?php wp_nonce_field( 'rcptn_send_single_message_nonce', 'rcptn_send_single_message_nonce' ); ?>
			</form>

		<?php
	}

	/**
	 * Render just the fields.
	 *
	 * @param RegionSelect $region_renderer  A region renderer.
	 * @param string       $message_value    A value for the messaging textarea.
	 */
	public static function render_form( RegionSelect $region_renderer, $message_value = '' ) {
		?>
			<table class="form-table">
				<tbody>
					<?php
						AdminFormField::render(
							'rcptn_region',
							__( 'Target region', 'rcptn' ),
							__( 'Choose the region that should receive this notice.', 'rcptn' ),
							array( $region_renderer, 'render' )
						);

						AdminFormField::render(
							'rcptn_message',
							__( 'Message', 'rcptn' ),
							__( 'Enter the message to send to the chosen region. You can use |*FIRST_NAME*| and |*LAST_NAME*| to insert the member’s name—they’ll be automatically replaced with the real values when sent to each member.', 'rcptn' ),
							array( __CLASS__, 'render_message_field' ),
							array(
								'field_value' => $message_value,
							)
						);
					?>
				</tbody>
			</table>
		<?php
	}

	/**
	 * Render the message body field.
	 *
	 * @param string $field_id     The field's ID.
	 * @param mixed  $field_value  The field's value.
	 */
	public static function render_message_field( $field_id, $field_value ) {
		?>
			<textarea name="<?php echo esc_attr( $field_id ); ?>" id="<?php echo esc_attr( $field_id ); ?>" cols="30" rows="4" placeholder="<?php esc_attr_e( 'Your message...', 'rcptn' ); ?>"><?php echo esc_html( $field_value ); ?></textarea>
		<?php
	}

}
