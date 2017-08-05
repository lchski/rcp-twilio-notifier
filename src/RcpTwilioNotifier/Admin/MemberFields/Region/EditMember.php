<?php
/**
 * RCP: RcpTwilioNotifier\Admin\MemberFields\Region\EditMember class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier
 */

namespace RcpTwilioNotifier\Admin\MemberFields\Region;

use RcpTwilioNotifier\Admin\MemberFields\AbstractEditMember;
use RcpTwilioNotifier\Helpers\Renderers\RegionSelect;
use RcpTwilioNotifier\Helpers\Validators\Region;

/**
 * Adds a region field to the RCP member editing screen.
 */
class EditMember extends AbstractEditMember {

	/**
	 * Set internal state.
	 *
	 * @param array $regions  List of regions available for selection.
	 */
	public function __construct( $regions ) {
		$this->regions = $regions;
	}

	/**
	 * Render the dropdown with the regions.
	 *
	 * @param int $user_id  ID of the member whose profile is being edited.
	 */
	public function render_field( $user_id = 0 ) {

		$select_renderer = new RegionSelect(
			$this->regions,
			array(
				'user_id' => $user_id,
			)
		);

		?>
			<tr valign="top">
				<th scope="row" valign="top">
					<label for="rcptn_region"><?php esc_html_e( 'Home Region', 'rcptn' ); ?></label>
				</th>
				<td>
					<?php $select_renderer->render(); ?>
					<p class="description"><?php esc_html_e( 'The member\'s home region for text alerts', 'rcptn' ); ?></p>
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

		$region_validator = new Region( $this->regions );

		// Note the "WPCS: CSRF ok." comments below. This is because this function only fires after RCP has verified its nonces.
		if ( isset( $_POST['rcptn_region'] ) && $region_validator->is_valid_region( $_POST['rcptn_region'] ) ) { // WPCS: CSRF ok.
			update_user_meta( $user_id, 'rcptn_region', sanitize_text_field( $_POST['rcptn_region'] ) ); // WPCS: CSRF ok.
		}

	}

}
