<?php
/**
 * RCP: RcpTwilioNotifier\Admin\Processors\MessagingPage class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin|Processors
 */

namespace RcpTwilioNotifier\Admin\Processors;
use RcpTwilioNotifier\Helpers\Validators\MessageBody;
use RcpTwilioNotifier\Models\Member;
use RcpTwilioNotifier\Models\Region;

/**
 * Processes form submissions from our MessagingPage in the WordPress admin.
 */
class MessagingPage extends AbstractProcessor implements ProcessorInterface {

	/**
	 * The name of the action that this processor processes.
	 *
	 * @var string  Action name.
	 */
	protected $action_name = 'send-single-message';

	/**
	 * The name of the nonce that this processor validates.
	 *
	 * @var string  Nonce name.
	 */
	protected $nonce_name = 'rcptn_send_single_message_nonce';

	/**
	 * List of regions available for messaging.
	 *
	 * @var array
	 */
	private $regions;

	/**
	 * List of errors.
	 *
	 * @var array
	 */
	private $errors = array();

	/**
	 * Set internal values.
	 *
	 * @param array $regions  Region list.
	 */
	public function __construct( $regions ) {
		$this->regions = $regions;
	}

	/**
	 * Process!
	 */
	public function process() {
		if ( ! $this->validate() ) {
			add_action( 'admin_notices', array( $this, 'render_errors' ) );

			return false;
		}
	}

	/**
	 * Validate each of the submitted inputs, setting them as properties if valid.
	 */
	private function validate() {
		// Validate both inputs.
		$is_valid_region = $this->validate_region();
		$is_valid_message = $this->validate_message();

		// If either input is invalid, we exit.
		if ( ! $is_valid_message || ! $is_valid_region ) {
			return false;
		}

		// Both inputs are valid, so we set them as properties.
		$this->region = new Region( $_POST['rcptn_region'] ); // WPCS: CSRF ok.
		$this->message = $_POST['rcptn_message']; // WPCS: CSRF ok.

		// A happy ending!
		return true;
	}

	/**
	 * Validate the region input.
	 *
	 * @return bool
	 */
	private function validate_region() {
		if ( ! isset( $_POST['rcptn_region'] ) ) { // WPCS: CSRF ok.
			$this->add_error( __( 'No region set.', 'rcptn' ) );

			return false;
		}

		$region_validator = new \RcpTwilioNotifier\Helpers\Validators\Region( $this->regions );

		if ( ! $region_validator->is_valid_region( $_POST['rcptn_region'] ) ) { // WPCS: CSRF ok.
			$this->add_error( __( 'Invalid region set.', 'rcptn' ) );

			return false;
		}

		return true;
	}

	/**
	 * Validate the message input.
	 *
	 * @return bool
	 */
	private function validate_message() {
		if ( ! isset( $_POST['rcptn_message'] ) ) { // WPCS: CSRF ok.
			$this->add_error( __( 'No message set.', 'rcptn' ) );

			return false;
		}

		if ( 0 === strlen( $_POST['rcptn_message'] ) ) { // WPCS: CSRF ok.
			$this->add_error( __( 'Message must not be empty.', 'rcptn' ) );

			return false;
		}

		if ( ! MessageBody::is_valid_message_body( $_POST['rcptn_message'] ) ) { // WPCS: CSRF ok.
			$this->add_error( __( 'Invalid message body.', 'rcptn' ) );

			return false;
		}

		return true;
	}

	/**
	 * Add an error message to the validation process.
	 *
	 * @param string $error_message  The error message to display.
	 */
	private function add_error( $error_message ) {
		$errors = $this->errors;
		$errors[] = $error_message;

		$this->errors = $errors;
	}

	/**
	 * Render the errors to the admin.
	 */
	public function render_errors() {
		foreach ( $this->errors as $error ) {
			?>
				<div class="error">
					<p><?php echo esc_html( $error ); ?> </p>
				</div>
			<?php
		}
	}

	/**
	 * Message all the members within a region.
	 *
	 * @param Region $region  The region whose members we'll message.
	 */
	public function message_all_in_region( Region $region ) {
		// @TODO: pull in members from other regions, who subscrbie to all regions
		$members = $region->get_members();

		foreach ( $members as $member ) {
			$this->message_member( $member );
		}
	}

	/**
	 * Message a given member, checking first that they’re eligible to receive messages.
	 *
	 * @param Member $member  The member to message.
	 *
	 * @return \Twilio\Rest\Api\V2010\Account\MessageInstance|void
	 */
	public function message_member( Member $member ) {
		if ( ! $member->is_active() ) {
			return;
		}

		return $member->send_message( $this->message );
	}

}
