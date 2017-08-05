<?php
/**
 * RCP: RcpTwilioNotifier\Helpers\Validators\Region
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Helpers\Validators
 */

namespace RcpTwilioNotifier\Helpers\Validators;

/**
 * Offers validation functions related to our list of regions.
 */
class Region {

	/**
	 * Set internal values.
	 *
	 * @param array $regions  The app's regions.
	 */
	public function __construct( $regions ) {
		$this->regions = $regions;
		$this->region_slugs = $this->extract_region_slugs( $this->regions );
	}

	/**
	 * Checks whether a region is valid (i.e. whether it exists in our array).
	 *
	 * @param string $region_slug  The submitted region’s slug.
	 *
	 * @return bool  Whether or not the region exists.
	 */
	public function is_valid_region( $region_slug ) {
		return in_array( $region_slug, $this->region_slugs, true );
	}

	/**
	 * Maps a region array to one containing just its slugs.
	 *
	 * @param array $regions  The full region array.
	 *
	 * @return array  The region array, mapped to just its slugs.
	 */
	private function extract_region_slugs( $regions ) {
		$extractor = function( $region ) {
			return $region['slug'];
		};

		return array_map( $extractor, $regions );
	}

}
