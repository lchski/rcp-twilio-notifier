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
	 * @param array $form_args  @see MessagingUi::render_form.
	 */
	public static function render( $form_args ) {
		?>
			<form id="rcptn-region-notifier-messenger" action="<?php echo esc_attr( menu_page_url( 'rcptn-region-notifier', false ) ); ?>" method="post">
				<?php
					self::render_form( $form_args );
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
	 * @param array $form_args {
	 *     Required. List of arguments for the form renderer.
	 *
	 *     @type RegionSelect $region_renderer     A region renderer. Required.
	 *     @type string       $message_value       A value for the messaging textarea. Optional.
	 *     @type array        $enabled_merge_tags  The merge tags enabled for this form. Optional.
	 *     @type array        $extra_data          Extra data to pass to the form.
	 * }
	 */
	public static function render_form( $form_args ) {
		$default_form_args = array(
			'message_value' => '',
			'enabled_merge_tags' => array(
				'|*FIRST_NAME*|',
				'|*LAST_NAME*|',
			),
			'extra_data' => $_POST['rcptn_extra_data'], // WPCS: CSRF ok.
		);

		$merged_form_args = wp_parse_args( $form_args, $default_form_args );

		?>
			<table class="form-table">
				<tbody>
					<?php
						AdminFormField::render(
							'rcptn_region',
							__( 'Target region', 'rcptn' ),
							__( 'Choose the region that should receive this notice.', 'rcptn' ),
							array( $merged_form_args['region_renderer'], 'render' )
						);

						AdminFormField::render(
							'rcptn_message',
							__( 'Message', 'rcptn' ),
							array_merge(
								array( __( 'Enter the message to send to the chosen region.', 'rcptn' ) ),
								( ! empty( $merged_form_args['enabled_merge_tags'] ) ) ? array( __( 'Several merge tags are available. These will be automatically replaced with their real values when the message is sent:', 'rcptn' ) ) : array(),
								self::get_merge_tag_descriptions( $merged_form_args['enabled_merge_tags'] )
							),
							array( __CLASS__, 'render_message_field' ),
							array(
								'field_value' => $merged_form_args['message_value'],
							)
						);

						self::render_extra_form_data( $merged_form_args['extra_data'] );
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

	/**
	 * Render extra data as hidden fields.
	 *
	 * @param array $fields  Data to render as hidden fields.
	 */
	private function render_extra_form_data( $fields ) {
		foreach ( $fields as $key => $value ) {
			?>
				<input type="hidden" name="<?php echo esc_attr( 'rcptn_extra_data[' . $key . ']' ); ?>" value="<?php echo esc_attr( $value ); ?>"/>
			<?php
		}
	}

	/**
	 * Get the descriptions for the merge tags enabled for this form.
	 *
	 * @param array $enabled_merge_tags  The merge tags enabled for this form.
	 *
	 * @return array  The descriptions of the merge tags enabled for this form.
	 */
	private static function get_merge_tag_descriptions( $enabled_merge_tags ) {
		$descriptions = array(
			'|*FIRST_NAME*|' => __( '|*FIRST_NAME*| for the member’s first name.', 'rcptn' ),
			'|*LAST_NAME*|'  => __( '|*LAST_NAME*| for the member’s last name.', 'rcptn' ),
			'|*ALERT_LINK*|' => __( '|*ALERT_LINK*| to link to this alert.', 'rcptn' ),
		);

		$verifier = function( $merge_tag ) use ( $enabled_merge_tags ) {
			return in_array( $merge_tag, $enabled_merge_tags, true );
		};

		return array_filter( $descriptions, $verifier, ARRAY_FILTER_USE_KEY );
	}

}
