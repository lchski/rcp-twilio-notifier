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
		add_action( 'rcp_add_subscription_form', array( $this, 'render_field' ) );
		add_action( 'rcp_edit_subscription_form', array( $this, 'render_field' ) );
	}

	/**
	 * Render the field.
	 */
	public function render_field() {
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
		if ( isset( $_POST[ $field_id ] ) ) { // WPCS: CSRF ok.
			$field_option_value = $_POST[ $field_id ]; // WPCS: CSRF ok.
		}

		$field_option_value = get_option( $field_id ); // @TODO: Change this.

		if ( false === $field_option_value ) {
			$field_option_value = '';
		}

		?>
			<input type="number" name="<?php echo esc_attr( $field_id ); ?>" id="<?php echo esc_attr( $field_id ); ?>" value="<?php echo esc_attr( $field_option_value ); ?>">
		<?php
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
