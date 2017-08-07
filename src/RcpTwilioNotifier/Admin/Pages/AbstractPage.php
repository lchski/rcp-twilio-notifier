<?php
/**
 * RCP: RcpTwilioNotifier\Admin\Pages\AbstractPage class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin|Pages
 */

namespace RcpTwilioNotifier\Admin\Pages;

/**
 * Handles routine submenu registration function, based on values set in the child class.
 */
abstract class AbstractPage {

	/**
	 * Hook into WordPress.
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'register_page' ) );

		if ( method_exists( $this, 'register_hooks' ) ) {
			$this->register_hooks();
		}
	}

	/**
	 * Register the page based on values set in the child class.
	 */
	public function register_page() {
		add_submenu_page(
			'rcp-members',
			$this->page_title,
			$this->menu_title,
			'rcp_view_members',
			$this->menu_slug,
			array( $this, 'render' )
		);
	}

}
