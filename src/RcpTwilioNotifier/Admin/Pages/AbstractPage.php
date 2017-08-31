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
	 * The slug of the parent page under which this page should sit.
	 *
	 * @var string
	 */
	protected $parent_slug = 'rcp-members';

	/**
	 * Hook into WordPress.
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'register_page' ) );
	}

	/**
	 * Register the page based on values set in the child class.
	 */
	public function register_page() {
		add_submenu_page(
			$this->parent_slug,
			$this->page_title,
			$this->menu_title,
			'rcp_view_members',
			$this->menu_slug,
			array( $this, 'render' )
		);
	}

}
