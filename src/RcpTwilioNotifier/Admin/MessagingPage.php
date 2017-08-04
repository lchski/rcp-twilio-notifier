<?php
/**
 * RCP: RcpTwilioNotifier\RegionField\MessagingPage class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin
 */

namespace RcpTwilioNotifier\Admin;

/**
 * WordPress admin page for messaging members by their region.
 */
class MessagingPage extends AbstractPage implements PageInterface {

	/**
	 * Page title
	 *
	 * @see add_submenu_page
	 * @var string
	 */
	protected $page_title;

	/**
	 * Menu title
	 *
	 * @see add_submenu_page
	 * @var string
	 */
	protected $menu_title;

	/**
	 * Menu slug (must be unique)
	 *
	 * @see add_submenu_page
	 * @var string
	 */
	protected $menu_slug;

	/**
	 * Set internal values.
	 */
	public function __construct() {
		$this->page_title = __( 'Region Notifier', 'rcptn' );
		$this->menu_title = __( 'Region Notifier', 'rcptn' );
		$this->menu_slug = 'rcptn-region-notifier';
	}

	/**
	 * Render the UI for the messaging page.
	 *
	 * @return void
	 */
	public function render() {
		// TODO: Implement render() method.
	}

}
