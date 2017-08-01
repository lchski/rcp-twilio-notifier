<?php
/**
 * RCP: RcpTwilioNotifier_RegionEditMemberField class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier
 */

/**
 * Adds a region field to the RCP member editing screen.
 */
class RcpTwilioNotifier_RegionEditMemberField {

	/**
	 * Hooks class functions into WordPress.
	 */
	public function init() {

		add_action( 'rcp_edit_member_after', array( $this, 'render_select' ) );

	}

	/**
	 * Render the dropdown with the regions.
	 *
	 * @param int $user_id  ID of the member whose profile is being edited.
	 */
	public function render_select( $user_id = 0 ) {

		$select_renderer = new RcpTwilioNotifier_RegionSelectRenderer( $user_id );

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

}
