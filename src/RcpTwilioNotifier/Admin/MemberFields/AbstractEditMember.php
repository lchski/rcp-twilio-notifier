<?php
/**
 * RCP: RcpTwilioNotifier\Admin\MemberFields\AbstractEditMember class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin\MemberFields
 */

namespace RcpTwilioNotifier\Admin\MemberFields;

/**
 * Generic init for RCP edit member field classes.
 */
abstract class AbstractEditMember {

	/**
	 * Hooks class functions into WordPress.
	 */
	public function init() {

		add_action( 'rcp_edit_member_after', array( $this, 'render_field' ) );

		add_action( 'rcp_user_profile_updated', array( $this, 'save_on_update' ), 10 );
		add_action( 'rcp_edit_member', array( $this, 'save_on_update' ), 10 );

	}

}
