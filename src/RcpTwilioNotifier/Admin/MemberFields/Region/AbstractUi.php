<?php
/**
 * RCP: RcpTwilioNotifier\Admin\MemberFields\Region\AbstractUiclass
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier
 */

namespace RcpTwilioNotifier\Admin\MemberFields\Region;

/**
 * Make the regions available to the child region UI class.
 */
abstract class AbstractUi {

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
		$this->region_slugs = $this->extract_region_slugs( $this->regions );

	}

	/**
	 * Maps a region array to one containing just its slugs.
	 *
	 * @param array $regions  The full region array.
	 *
	 * @return array  The region array, mapped to just its slugs.
	 */
	protected function extract_region_slugs( $regions ) {

		$extractor = function( $region ) {
			return $region['slug'];
		};

		return array_map( $extractor, $regions );

	}

}
