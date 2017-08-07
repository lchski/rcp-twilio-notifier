<?php
/**
 * RCP: RcpTwilioNotifier\Admin\Pages\Processors\AbstractProcessor class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin|Pages\Processors
 */

namespace RcpTwilioNotifier\Admin\Pages\Processors;

/**
 * Handles tasks global to all admin processors.
 */
abstract class AbstractProcessor {

	/**
	 * The $_POST array.
	 *
	 * @var array
	 */
	protected $posted;

	/**
	 * Hook into WordPress, on any admin page.
	 */
	public function init() {
		$this->posted = $_POST; // WPCS: CSRF ok.

		add_action( 'admin_init', array( $this, 'load_processor' ) );
	}

	/**
	 * Conditionally run our processor.
	 */
	public function load_processor() {
		// If there's no data posted, there's nothing to process.
		if ( empty( $this->posted ) ) {
			return;
		}

		// If there's no action from our plugin, there's nothing to process.
		if ( ! isset( $this->posted['rcptn-action'] ) ) {
			return;
		}

		// If our processor's action isn't included in the posted data, there's nothing to process.
		if ( $this->action_name !== $this->posted['rcptn-action'] ) {
			return;
		}

		// If the nonce doesn’t validate, we’re not processing anything.
		if ( ! wp_verify_nonce( $this->posted[ $this->nonce_name ], $this->nonce_name ) ) {
			wp_die( esc_html__( 'Nonce verification failed.', 'rcptn' ) );
		}

		// Everything checks out... we’re good to process!
		$this->process();
	}

}
