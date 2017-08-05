<?php
/**
 * RCP: RcpTwilioNotifier\Admin\Processors\AbstractProcessor class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin|Processors
 */

namespace RcpTwilioNotifier\Admin\Processors;

/**
 * Handles tasks global to all admin processors.
 */
abstract class AbstractProcessor {

	/**
	 * Hook into WordPress, on any admin page.
	 */
	public function init() {
		add_action( 'admin_init', array( $this, 'process' ) );
	}

}
