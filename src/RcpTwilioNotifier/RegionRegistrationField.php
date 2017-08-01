<?php
/**
 * RCP: RcpTwilioNotifier_RegionRegistrationField class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier
 */

/**
 * Adds a region field to the RCP registration process.
 */
class RcpTwilioNotifier_RegionRegistrationField {

	/**
	 * Regions to choose from in the registration process.
	 *
	 * @var array
	 */
	private $regions;

	/**
	 * Set internal state.
	 */
	public function __construct() {

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
	 * Hooks class functions into WordPress.
	 */
	public function init() {

		add_action( 'rcp_after_password_registration_field', array( $this, 'render_select' ) );
		add_action( 'rcp_profile_editor_after', array( $this, 'render_select' ) );

	}

	/**
	 * Render the dropdown with the regions.
	 */
	public function render_select() {

		?>
			<p>
				<label for="rcptn_region"><?php esc_html_e( 'Your Home Region', 'rcptn' ); ?></label>
				<select id="rcptn_region" name="rcptn_region">
					<?php $this->render_options(); ?>
				</select>
			</p>
		<?php

	}

	/**
	 * Render the various region options.
	 */
	private function render_options() {

		$current_region = get_user_meta( get_current_user_id(), 'rcptn_region', true );

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
