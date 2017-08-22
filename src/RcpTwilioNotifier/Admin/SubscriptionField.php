<?php
/**
 * RCP: RcpTwilioNotifier\Admin\SubscriptionField class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin
 */

namespace RcpTwilioNotifier\Admin;

use RcpTwilioNotifier\Helpers\Renderers\AdminFormField;

/**
 * Adds a field to the subscription admin interface to link basic subscriptions
 * to their add-on equivalents.
 */
class SubscriptionField {

	/**
	 * Hook into WordPress.
	 */
	public function init() {
		add_action( 'rcp_edit_subscription_form', array( $this, 'render_field' ) );
		add_action( 'admin_init', array( $this, 'process' ) );
	}

	/**
	 * Render the field.
	 *
	 * @param object $level  The RCP_Levels level being edited.
	 */
	public function render_field( $level ) {
		$this->level = $level;

		AdminFormField::render(
			'rcptn_linked_addon_id',
			__( 'All Regions Add-on Subscription ID', 'rcptn' ),
			__( 'The ID of the all regions add-on version of this subscription, if this is the basic level.', 'rcptn' ),
			array( $this, 'render_input' ),
			array(
				'required' => false,
			)
		);
	}

	/**
	 * Render the input for the field.
	 *
	 * @param string $field_id  The field’s ID.
	 */
	public function render_input( $field_id ) {
		$rcp_levels = new \RCP_Levels();

		if ( isset( $_POST[ $field_id ] ) ) { // WPCS: CSRF ok.
			$field_value = $_POST[ $field_id ]; // WPCS: CSRF ok.
		}

		$field_value = $rcp_levels->get_meta( $this->level->id, 'rcptn_add_on_level_id', true );

		if ( false === $field_value ) {
			$field_value = '';
		}

		?>
			<input type="number" name="<?php echo esc_attr( $field_id ); ?>" id="<?php echo esc_attr( $field_id ); ?>" value="<?php echo esc_attr( $field_value ); ?>">
		<?php
	}

	/**
	 * Handle form processing, following the RCP processing.
	 */
	public function process() {

	}

	/**
	 * Verify the data is in the correct format.
	 */
	public function validate() {

	}

	/**
	 * Save the data.
	 */
	public function save() {

	}

}
