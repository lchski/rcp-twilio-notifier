<?php
/**
 * RCP: RcpTwilioNotifier\Admin\Pages\Validators\AbstractValidator class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin|Pages\Validators
 */

namespace RcpTwilioNotifier\Admin\Pages\Validators;

/**
 * Triggers page validation and handles errors.
 */
abstract class AbstractValidator {

	/**
	 * List of errors.
	 *
	 * @var array
	 */
	protected $errors = array();

	/**
	 * Trigger validation and return the result.
	 *
	 * @return bool
	 */
	public function init() {
		$validation_status = $this->validate();

		if ( ! $validation_status ) {
			add_action( 'admin_notices', array( $this, 'render_errors' ) );

			return false;
		}

		return true;
	}

	/**
	 * Add an error message to the validation process.
	 *
	 * @param string $error_message  The error message to display.
	 */
	protected function add_error( $error_message ) {
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
}
