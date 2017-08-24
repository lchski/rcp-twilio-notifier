<?php
/**
 * RCP: RcpTwilioNotifier\Admin\MemberFields\PhoneCountry\EditMember class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier
 */

namespace RcpTwilioNotifier\Admin\MemberFields\PhoneCountry;

use RcpTwilioNotifier\Admin\MemberFields\AbstractEditMember;
use RcpTwilioNotifier\Helpers\CountryLister;
use RcpTwilioNotifier\Helpers\Renderers\CountrySelect;

/**
 * Adds a region field to the RCP member editing screen.
 */
class EditMember extends AbstractEditMember {

	/**
	 * Render the dropdown with the regions.
	 *
	 * @param int $user_id  ID of the member whose profile is being edited.
	 */
	public function render_field( $user_id = 0 ) {

		$select_renderer = new CountrySelect(
			array(
				'user_id' => $user_id,
			)
		);

			?>
		<tr valign="top">
			<th scope="row" valign="top">
				<label for="rcptn_phone_country_code"><?php esc_html_e( 'Phone Number Country', 'rcptn' ); ?></label>
			</th>
			<td>
				<?php $select_renderer->render(); ?>
				<p class="description"><?php esc_html_e( 'The country that the member has their phone number in (gets added to the phone number when messages are sent).', 'rcptn' ); ?></p>
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
		if ( isset( $_POST['rcptn_phone_country_code'] ) && in_array( $_POST['rcptn_phone_country_code'], array_keys( CountryLister::get_countries() ), true ) ) { // WPCS: CSRF ok.
			update_user_meta( $user_id, 'rcptn_phone_country_code', sanitize_text_field( $_POST['rcptn_phone_country_code'] ) ); // WPCS: CSRF ok.
		}
	}

}
