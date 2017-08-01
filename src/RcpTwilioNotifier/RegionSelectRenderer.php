<?php
/**
 * RCP: RcpTwilioNotifier_RegionSelectRenderer class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier
 */

/**
 * Outputs an HTML <select> with the regions as options.
 */
class RcpTwilioNotifier_RegionSelectRenderer {

	/**
	 * The user ID to render the select for.
	 *
	 * This affects the currently selected item, if there is one.
	 *
	 * @var int
	 */
	private $user_id;

	/**
	 * Regions to choose from in select.
	 *
	 * @var array
	 */
	private $regions;

	/**
	 * Set internal state.
	 *
	 * @param int $user_id  The user ID to render the select for.
	 */
	public function __construct( $user_id ) {

		$this->user_id = $user_id;

		// Set up the regions with our defaults, filtered for customization.
		$this->regions = apply_filters(
			'rcptn_regions', array(
				array(
					'key' => 'south-east',
					'label' => __( 'South East', 'rcptn' ),
				),
				array(
					'key' => 'north-east',
					'label' => __( 'North East', 'rcptn' ),
				),
				array(
					'key' => 'new-england',
					'label' => __( 'New England', 'rcptn' ),
				),
				array(
					'key' => 'mid-west',
					'label' => __( 'Mid West', 'rcptn' ),
				),
				array(
					'key' => 'west-coast',
					'label' => __( 'West Coast', 'rcptn' ),
				),
			)
		);

	}

	/**
	 * Render the select dropdown.
	 */
	public function render() {
		?>
			<select id="rcptn_region" name="rcptn_region" class="rcptn-registration-select">
				<?php $this->render_default_option(); ?>
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
			<?php echo esc_html( apply_filters( 'rcptn_region_select_default_option', __( 'Select your region...', 'rcptn' ) ) ); ?>
		</option>
		<?php

	}

	/**
	 * Render the various region options.
	 */
	private function render_region_options() {

		$current_region = get_user_meta( $this->user_id, 'rcptn_region', true );

		foreach ( $this->regions as $region ) {
			?>
			<option
				value=<?php echo esc_attr( $region['key'] ); ?>
				<?php selected( $current_region, $region['key'] ); ?>
			>
				<?php echo esc_html( $region['label'] ); ?>
			</option>
			<?php
		}

	}

}
