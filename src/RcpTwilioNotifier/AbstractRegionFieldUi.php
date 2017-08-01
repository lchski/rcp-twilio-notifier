<?php
/**
 * RCP: RcpTwilioNotifier_AbstractRegionFieldUi class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier
 */

/**
 * Make the regions available to the child region UI class.
 */
abstract class RcpTwilioNotifier_AbstractRegionFieldUi {

	/**
	 * List of regions available for selection.
	 *
	 * @var array
	 */
	protected $regions;

	/**
	 * Set internal state.
	 *
	 * @param array $regions  List of regions available for selection.
	 */
	public function __construct( $regions ) {

		$this->regions = $regions;

	}

}
