<?php
/**
 * RCP: RcpTwilioNotifier\Admin\MemberFields\PhoneNumber\EditMember class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin\MemberFields\PhoneNumber
 */

namespace RcpTwilioNotifier\Admin\MemberFields\PhoneNumber;
use RcpTwilioNotifier\Helpers\Renderers\PhoneNumberInput;
use RcpTwilioNotifier\Helpers\Validators\PhoneNumber;

/**
 * Adds a phone number field to the RCP member editing screen.
 */
class EditMember extends AbstractUi {

	/**
	 * Hooks class functions into WordPress.
	 */
	public function init() {

		add_action( 'rcp_edit_member_after', array( $this, 'render_field' ) );

		add_action( 'rcp_user_profile_updated', array( $this, 'save_on_update' ), 10 );
		add_action( 'rcp_edit_member', array( $this, 'save_on_update' ), 10 );

	}

	/**
	 * Render the dropdown with the regions.
	 *
	 * @param int $user_id  ID of the member whose profile is being edited.
	 */
	public function render_field( $user_id = 0 ) {
		$phone_number_input_renderer = new PhoneNumberInput( $user_id );

		?>
			<tr valign="top">
				<th scope="row" valign="top">
					<label for="rcptn_phone_number"><?php esc_html_e( 'Phone Number', 'rcptn' ); ?></label>
				</th>
				<td>
					<?php $phone_number_input_renderer->render(); ?>
					<p class="description"><?php esc_html_e( 'The member\'s phone number for text alerts', 'rcptn' ); ?></p>
				</td>
			</tr>
		<?php
	}

	/**
	 * Save the new region data on profile update.
	 *
	 * @param int $user_id  The ID for the user we’re updating.
	 */
	public function save_on_update( $user_id ) {

		// Note the "WPCS: CSRF ok." comments below. This is because this function only fires after RCP has verified its nonces.
		if ( isset( $_POST['rcptn_phone_number'] ) && PhoneNumber::is_valid_phone_number( $_POST['rcptn_phone_number'] ) ) { // WPCS: CSRF ok.
			update_user_meta( $user_id, 'rcptn_phone_number', sanitize_text_field( $_POST['rcptn_phone_number'] ) ); // WPCS: CSRF ok.
		}

	}

}
