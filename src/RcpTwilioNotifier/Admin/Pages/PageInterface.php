<?php
/**
 * Interface PageInterface
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin\Pages
 */

namespace RcpTwilioNotifier\Admin\Pages;

/**
 * Ensures child classes to RcpTwilioNotifier\Admin\AbstractPage have the right functions.
 */
interface PageInterface {

	/**
	 * Render the HTML for the page.
	 *
	 * @return void
	 */
	public function render();

}
