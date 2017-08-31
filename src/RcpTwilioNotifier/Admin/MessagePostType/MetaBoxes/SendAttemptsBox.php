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
			<table class="widefat fixed" cellspacing="0">
				<thead>
					<tr>
						<th scope="col"><?php esc_html_e( 'Recipient', 'rcptn' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Status', 'rcptn' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
						$send_attempt_counter = 0;

					foreach ( $this->message->get_send_attempts() as $send_attempt ) {
						$send_attempt_counter++;
						?>
						<tr class="<?php echo esc_attr( (0 !== $send_attempt_counter % 2) ? 'alternate' : '' ); ?>">
							<td>
								<?php
								echo esc_html(
									sprintf(
										'%1$s %2$s (%3$s)',
										$send_attempt['recipient']->first_name,
										$send_attempt['recipient']->last_name,
										$send_attempt['recipient']->get_phone_number()
									)
								);
								?>
							</td>
							<td>
								<?php
									$send_status = '';

								switch ( $send_attempt['status'] ) {
									case 'success':
										$send_status = __( 'Success', 'rcptn' );
										break;
									case 'failed':
										// translators: %s is the error message.
										$send_status = sprintf( __( 'Failed <br><small>%s</small>', 'rcptn' ), $send_attempt['error'] );
										break;
									default:
										// translators: %s is the unknown send attempt status.
										$send_status = sprintf( __( 'Unknown (%s)', 'rcptn' ), $send_attempt['status'] );
										break;
								}

									echo wp_kses(
										$send_status, array(
											'br' => array(),
											'small' => array(),
										)
									);
								?>
							</td>
						</tr>
						<?php
					}
					?>
				</tbody>
			</table>
		<?php
	}

}