<?php
/**
 * RCP: RcpTwilioNotifier\Admin\MemberFields\Region\EditMember class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier
 */

namespace RcpTwilioNotifier\Admin\MemberFields\Region;

/**
 * Adds a region field to the RCP member editing screen.
 */
class EditMember extends AbstractUi {

	/**
	 * Hooks class functions into WordPress.
	 */
	public function init() {

		add_action( 'rcp_edit_member_after', array( $this, 'render_select' ) );

		add_action( 'rcp_user_profile_updated', array( $this, 'save_on_update' ), 10 );
		add_action( 'rcp_edit_member', array( $this, 'save_on_update' ), 10 );

	}

	/**
	 * Render the dropdown with the regions.
	 *
	 * @param int $user_id  ID of the member whose profile is being edited.
	 */
	public function render_select( $user_id = 0 ) {

		$select_renderer = new SelectRenderer( $this->regions, $user_id );

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

		// Note the "WPCS: CSRF ok." comments below. This is because this function only fires after RCP has verified its nonces.
		if ( isset( $_POST['rcptn_region'] ) && in_array( $_POST['rcptn_region'], $this->region_slugs, true ) ) { // WPCS: CSRF ok.
			update_user_meta( $user_id, 'rcptn_region', sanitize_text_field( $_POST['rcptn_region'] ) ); // WPCS: CSRF ok.
		}

	}

}
