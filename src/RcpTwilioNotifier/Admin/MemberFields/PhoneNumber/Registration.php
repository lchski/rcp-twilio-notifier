<?php
/**
 * RCP: RcpTwilioNotifier\Admin\MemberFields\PhoneNumber\Registration class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin\MemberFields\PhoneNumber
 */

namespace RcpTwilioNotifier\Admin\MemberFields\PhoneNumber;
use RcpTwilioNotifier\Helpers\Renderers\PhoneNumberInput;
use RcpTwilioNotifier\Helpers\Validators\PhoneNumber;

/**
 * Adds a phone number field to the RCP registration process.
 */
class Registration extends AbstractUi {

	/**
	 * Hooks class functions into WordPress.
	 */
	public function init() {

		add_action( 'rcp_after_password_registration_field', array( $this, 'render_field' ) );
		add_action( 'rcp_profile_editor_after', array( $this, 'render_field' ) );

		add_action( 'rcp_form_errors', array( $this, 'validate_on_register' ) , 10 );

		add_action( 'rcp_form_processing', array( $this, 'save_on_register' ), 10, 2 );

	}

	/**
	 * Render the phone number field
	 */
	public function render_field() {
		$phone_number_input_renderer = new PhoneNumberInput( get_current_user_id() );

		?>
			<p>
				<label for="rcptn_phone_number"><?php esc_html_e( 'Your Phone Number', 'rcptn' ); ?></label>
				<?php $phone_number_input_renderer->render(); ?>
			</p>
		<?php
	}

	/**
	 * Validate the posted registration data.
	 *
	 * @param array $posted  The posted registration data.
	 */
	public function validate_on_register( $posted ) {

		if ( rcp_get_subscription_id() ) {
			return;
		}

		// Add an error message if the submitted option isn't one of our valid choices.
		if ( ! PhoneNumber::is_valid_phone_number( $posted['rcptn_phone_number'] ) ) {
			rcp_errors()->add( 'invalid_phone_number', __( 'Please enter a valid phone number', 'rcptn' ), 'register' );
		}

	}

	/**
	 * Save the phone number on successful registration.
	 *
	 * @param array $posted  The posted registration data.
	 * @param int   $user_id  The newly created user's ID.
	 */
	public function save_on_register( $posted, $user_id ) {

		if ( ! empty( $posted['rcptn_phone_number'] ) ) {
			update_user_meta( $user_id, 'rcptn_phone_number', sanitize_text_field( $posted['rcptn_phone_number'] ) );
		}

	}

}
