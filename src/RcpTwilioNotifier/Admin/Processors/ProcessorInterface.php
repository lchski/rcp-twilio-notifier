<?php
/**
 * RCP: RcpTwilioNotifier\Admin\Processors\ProcessorInterface interface
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin\Processors
 */

namespace RcpTwilioNotifier\Admin\Processors;

/**
 * Ensures child classes to RcpTwilioNotifier\Admin\Processors\AbstractProcessor have the right functions.
 */
interface ProcessorInterface {

	/**
	 * Process the data for a request on a given page.
	 *
	 * @return void
	 */
	public function process();

}
