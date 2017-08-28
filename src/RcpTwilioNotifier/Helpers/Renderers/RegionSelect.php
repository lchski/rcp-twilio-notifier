<?php
/**
 * RCP: RcpTwilioNotifier\Helpers\Renderers\RegionSelect class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Helpers\Renderers
 */

namespace RcpTwilioNotifier\Helpers\Renderers;

/**
 * Outputs an HTML <select> with the regions as options.
 */
class RegionSelect {

	/**
	 * Regions to choose from in select.
	 *
	 * @var array
	 */
	private $regions;

	/**
	 * The user ID to render the select for.
	 *
	 * This affects the currently selected item, if there is one.
	 *
	 * @var int
	 */
	private $user_id;

	/**
	 * Set internal state.
	 *
	 * @param array $regions  Regions to choose from in select.
	 * @param array $args {
	 *    Optional. Provide options to configure the currently selected region.
	 *
	 *    @type int    $user_id                     The user ID to render the select for.
	 *    @type string $selected_region_slug        The currently selected region's slug.
	 *    @type bool   $include_all_regions_option  Whether to include an option for all regions.
	 * }
	 */
	public function __construct( $regions, $args ) {
		$this->regions = $regions;

		$defaults = array(
			'user_id'                     => false,
			'selected_region_slug'        => false,
			'include_all_regions_option'  => false,
		);

		$this->args = wp_parse_args( $args, $defaults );
	}

	/**
	 * Render the select dropdown.
	 */
	public function render() {
		?>
			<select id="rcptn_region" name="rcptn_region" class="rcptn-registration-select">
				<?php $this->render_default_option(); ?>
				<?php $this->render_all_regions_option(); ?>
				<?php $this->render_region_options(); ?>
			</select>
		<?php
	}

	/**
	 * Render the default dropdown option.
	 */
	private function render_default_option() {

		?>
			<option value="none">
				<?php echo esc_html( apply_filters( 'rcptn_region_select_default_option', __( 'Select a region...', 'rcptn' ) ) ); ?>
			</option>
		<?php

	}

	/**
	 * Render the all regions option.
	 */
	private function render_all_regions_option() {
		$current_region = $this->get_current_region();

		if ( $this->args['include_all_regions_option'] ) {
			?>
				<option
					value="all"
					<?php selected( $current_region, 'all' ); ?>
				>
					<?php esc_html_e( 'All Regions', 'rcptn' ); ?>
				</option>
			<?php
		}
	}

	/**
	 * Render the various region options.
	 */
	private function render_region_options() {

		$current_region = $this->get_current_region();

		foreach ( $this->regions as $region ) {
			?>
				<option
					value=<?php echo esc_attr( $region['slug'] ); ?>
					<?php selected( $current_region, $region['slug'] ); ?>
				>
					<?php echo esc_html( $region['label'] ); ?>
				</option>
			<?php
		}

	}

	/**
	 * Get the currently selected region.
	 *
	 * @return string|bool
	 */
	private function get_current_region() {
		if ( $this->args['selected_region_slug'] ) {
			return $this->args['selected_region_slug'];
		}

		if ( $this->args['user_id'] ) {
			return get_user_meta( $this->args['user_id'], 'rcptn_region', true );
		}

		return false;
	}

}
