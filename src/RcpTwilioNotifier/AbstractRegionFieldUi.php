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
		$this->region_keys = $this->extract_region_keys( $this->regions );

	}

	/**
	 * Maps a region array to one containing just its keys.
	 *
	 * @param array $regions  The full region array.
	 *
	 * @return array  The region array, mapped to just its keys.
	 */
	protected function extract_region_keys( $regions ) {

		$extractor = function( $region ) {
			return $region['key'];
		};

		return array_map( $extractor, $regions );

	}

}
