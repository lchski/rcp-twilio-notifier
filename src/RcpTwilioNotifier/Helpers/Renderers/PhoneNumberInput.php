<?php
/**
 * RCP: RcpTwilioNotifier\Helpers\Renderers\RegionSelect class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Helpers\Renderers
 */

namespace RcpTwilioNotifier\Helpers\Renderers;

/**
 * Outputs an HTML <input> for a given member's phone number.
 */
class PhoneNumberInput {

	/**
	 * Set internal state.
	 *
	 * @param int $user_id  The user for whom to render the input.
	 */
	public function __construct( $user_id ) {
		$this->user_id = $user_id;
	}

	/**
	 * Renders the input.
	 */
	public function render_phone_number_field() {
		$current_number = get_user_meta( $this->user_id, 'rcptn_phone_number', true );

		?>
			<input id="rcptn_phone_number" name="rcptn_phone_number" type="tel" value="<?php echo esc_attr( $current_number ); ?>">
		<?php
	}

}
