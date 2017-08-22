<?php
/**
 * RCP: RcpTwilioNotifier\Admin\SubscriptionField class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin
 */

namespace RcpTwilioNotifier\Admin;

use RcpTwilioNotifier\Helpers\Notifier;
use RcpTwilioNotifier\Helpers\Renderers\AdminFormField;
use RcpTwilioNotifier\Models\Notice;

/**
 * Adds a field to the subscription admin interface to link basic subscriptions
 * to their add-on equivalents.
 */
class SubscriptionField {

	/**
	 * The $this->posted array.
	 *
	 * @var array
	 */
	protected $posted;

	/**
	 * The RCPTN Notifier instance.
	 *
	 * @var Notifier
	 */
	protected $notifier;

	/**
	 * Hook into WordPress.
	 */
	public function init() {
		$this->posted   = $_POST; // WPCS: CSRF ok.
		$this->notifier = Notifier::get_instance();

		add_action( 'rcp_edit_subscription_form', array( $this, 'render_field' ) );
		add_action( 'admin_init', array( $this, 'process' ), 9 );
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

		if ( isset( $this->posted[ $field_id ] ) ) {
			$field_value = $this->posted[ $field_id ];
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
		if ( ! $this->check_whether_to_process() ) {
			return;
		}

		if ( ! $this->validate() ) {
			return;
		}

		if ( ! $this->save() ) {
			$this->notifier->add_notice( new Notice( 'error', __( 'The All Regions Add-on Subscription ID failed to save. Please try again, or contact an admin if this error persists.', 'rcptn' ) ) );
		}
	}

	/**
	 * Check whether to proceed with processing.
	 *
	 * @return bool
	 */
	private function check_whether_to_process() {
		// Need POST data.
		if ( empty( $this->posted ) ) {
			return false;
		}

		// Need an RCP form action.
		if ( ! isset( $this->posted['rcp-action'] ) ) {
			return false;
		}

		// The RCP form action must be correct.
		if ( 'edit-subscription' !== $this->posted['rcp-action'] ) {
			return false;
		}

		// The nonce must be correct.
		if ( ! wp_verify_nonce( $this->posted['rcp_edit_level_nonce'], 'rcp_edit_level_nonce' ) ) {
			wp_die( esc_html__( 'Nonce verification failed.', 'rcp' ) );
		}

		// The user must have the proper permissions.
		if ( ! current_user_can( 'rcp_manage_levels' ) ) {
			wp_die( esc_html__( 'You do not have permission to perform this action.', 'rcp' ) );
		}

		// The input must be submitted.
		if ( ! isset( $this->posted['rcptn_linked_addon_id'] ) ) {
			return false;
		}

		// We’re good!
		return true;
	}

	/**
	 * Verify the data is in the correct format.
	 */
	private function validate() {
		// If it's been cleared, the input will be blank.
		if ( 0 === strlen( $this->posted['rcptn_linked_addon_id'] ) ) {
			return true;
		}

		// The ID must be a number.
		if ( false === is_numeric( $this->posted['rcptn_linked_addon_id'] ) ) {
			$this->notifier->add_notice( new Notice( 'error', __( 'The ID provided for the RCP all regions subscription ID is not a number.', 'rcptn' ) ) );

			return false;
		}

		// The ID must exist as a subscription.
		if ( false === rcp_get_subscription_details( $this->posted['rcptn_linked_addon_id'] ) ) {
			$this->notifier->add_notice( new Notice( 'error', __( 'The ID provided for the RCP all regions subscription ID does not exist as a subscription level.', 'rcptn' ) ) );

			return false;
		}

		return true;
	}

	/**
	 * Save the data.
	 */
	private function save() {
		// Retrieve the level we're modifying.
		$current_level_id = absint( $this->posted['subscription_id'] );

		// Get the levels modifier object.
		$rcp_levels = new \RCP_Levels();

		// Modify the level.
		return $rcp_levels->update_meta( $current_level_id, 'rcptn_add_on_level_id', $this->posted['rcptn_linked_addon_id'] ); // @TODO: Sanitize this.
	}

}
