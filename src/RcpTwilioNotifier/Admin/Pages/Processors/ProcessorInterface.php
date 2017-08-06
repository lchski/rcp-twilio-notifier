<?php
/**
 * RCP: RcpTwilioNotifier\Admin\Pages\Processors\ProcessorInterface interface
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin\Pages\Processors
 */

namespace RcpTwilioNotifier\Admin\Pages\Processors;

/**
 * Ensures child classes to RcpTwilioNotifier\Admin\Pages\Processors\AbstractProcessor have the right functions.
 */
interface ProcessorInterface {

	/**
	 * Process the data for a request on a given page.
	 *
	 * @return void
	 */
	public function process();

}
