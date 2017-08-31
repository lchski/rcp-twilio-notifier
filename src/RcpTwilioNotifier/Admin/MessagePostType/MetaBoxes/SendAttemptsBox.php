<?php
/**
 * RCP: RcpTwilioNotifier\Admin\MessagePostType\SendAttemptsBox class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin\MessagePostType\MetaBoxes
 */

namespace RcpTwilioNotifier\Admin\MessagePostType\MetaBoxes;
use RcpTwilioNotifier\Models\SendAttempt;

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
			<table class="widefat fixed" cellspacing="0">
				<thead>
					<tr>
						<th scope="col"><?php esc_html_e( 'Recipient', 'rcptn' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Status', 'rcptn' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Actions', 'rcptn' ); ?></th>
					</tr>
				</thead>

				<tfoot>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>
							<form method="post" action="<?php echo esc_attr( menu_page_url( 'rcptn-region-notifier', false ) ); ?>">
								<input type="hidden" name="rcptn_message_id" value="<?php echo esc_attr( $this->message->get_id() ); ?>">

								<input type="hidden" name="rcptn-action" value="message-failed-recipients">
								<?php wp_nonce_field( 'rcptn_message_failed_recipients_nonce', 'rcptn_message_failed_recipients_nonce' ); ?>

								<button class="button button-primary" type="submit">Retry Failed Messages</button>
							</form>
						</td>
					</tr>
				</tfoot>

				<tbody>
					<?php
					$send_attempt_counter = 0;

					foreach ( $this->message->get_send_attempts() as $send_attempt ) {
						$this->render_send_attempt( $send_attempt, $send_attempt_counter );

						$send_attempt_counter++;
					}
					?>
				</tbody>
			</table>
		<?php
	}

	/**
	 * Render a SendAttempt row.
	 *
	 * @param SendAttempt $send_attempt  The attempt being rendered.
	 * @param int         $index         The index of this row.
	 */
	private function render_send_attempt( SendAttempt $send_attempt, $index ) {
		?>
		<tr class="<?php echo esc_attr( (0 === $index % 2) ? 'alternate' : '' ); ?>">
			<td>
				<?php
				echo esc_html(
					sprintf(
						// translators: %1$s is recipient's first name; %2$s is recipient's last name; %3$s is recipient's phone number.
						__( '%1$s %2$s (%3$s)', 'rcptn' ),
						$send_attempt->recipient->first_name,
						$send_attempt->recipient->last_name,
						$send_attempt->recipient->get_phone_number()
					)
				);
				?>
				<div class="row-actions">
					<?php
					echo esc_html(
						sprintf(
							// translators: %d is the recipient's user ID.
							__( 'ID: %d', 'rcptn' ),
							$send_attempt->recipient->ID
						)
					);
					?>
				</div>
			</td>
			<td>
				<?php
				if ( $send_attempt->is_success() ) {
					$send_status = sprintf(
					    // translators: %s is the send date.
						__( 'Success <br><small>%s</small>', 'rcptn' ),
						( is_int( $send_attempt->timestamp ) )
							? date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $send_attempt->timestamp )
							: ''
					);
				} elseif ( $send_attempt->is_failed() ) {
					// translators: %s is the error message.
					$send_status = sprintf( __( 'Failed <br><small>%s</small>', 'rcptn' ), $send_attempt->error );
				} else {
					// translators: %s is the unknown send attempt status.
					$send_status = sprintf( __( 'Unknown (%s)', 'rcptn' ), $send_attempt->status );
				}

				echo wp_kses(
					$send_status, array(
						'br' => array(),
						'small' => array(),
					)
				);
				?>
			</td>
			<td>
				<form method="post" action="<?php echo esc_attr( menu_page_url( 'rcptn-region-notifier', false ) ); ?>">
					<input type="hidden" name="rcptn_message_id" value="<?php echo esc_attr( $this->message->get_id() ); ?>">
					<input type="hidden" name="rcptn_recipient_id" value="<?php echo esc_attr( $send_attempt->recipient->ID ); ?>">

					<input type="hidden" name="rcptn-action" value="message-single-recipient">
					<?php wp_nonce_field( 'rcptn_message_single_recipient_nonce', 'rcptn_message_single_recipient_nonce' ); ?>

					<button class="button button-link button-small" type="submit">Resend</button>
				</form>
			</td>
		</tr>
		<?php
	}

}
