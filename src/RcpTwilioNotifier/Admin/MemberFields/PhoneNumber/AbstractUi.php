<?php
/**
 * RCP: RcpTwilioNotifier\Admin\MemberFields\PhoneNumber\AbstractUiclass
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin\MemberFields\PhoneNumber
 */

namespace RcpTwilioNotifier\Admin\MemberFields\PhoneNumber;

/**
 * Make functions available to the child phone number UI classes.
 */
abstract class AbstractUi {

	/**
	 * Validate that a phone number is in a format acceptable to us.
	 *
	 * @param string $number  The phone number to check.
	 *
	 * @return bool
	 */
	protected function validate_phone_number( $number ) {
		return true; // @TODO: Implement validator
	}

	/**
	 * Render a phone number field for a given user.
	 *
	 * @param int $user_id  The ID for the user for whom we’re rendering the field.
	 */
	protected function render_phone_number_field( $user_id ) {
		$current_region = get_user_meta( $this->user_id, 'rcptn_phone_number', true );

		?>
			<input id="rcptn_phone_number" name="rcptn_phone_number" type="tel" value="<?php echo esc_attr( $current_region ); ?>">
		<?php
	}

}
