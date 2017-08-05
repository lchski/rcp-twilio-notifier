<?php
/**
 * RCP: RcpTwilioNotifier\Admin\Processors\AbstractProcessor class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin|Processors
 */

namespace RcpTwilioNotifier\Admin\Processors;

/**
 * Handles tasks global to all admin processors.
 */
abstract class AbstractProcessor {

	/**
	 * Hook into WordPress, on any admin page.
	 */
	public function init() {
		add_action( 'admin_init', array( $this, 'load_processor' ) );
	}

	/**
	 * Conditionally run our processor.
	 */
	public function load_processor() {
		// If there's no data posted, there's nothing to process.
		if ( empty( $_POST ) ) {
			return;
		}

		// If there's no action from our plugin, there's nothing to process.
		if ( ! isset( $_POST['rcptn-action'] ) ) {
			return;
		}

		// If our processor's action isn't included in the posted data, there's nothing to process.
		// Note the "WPCS: CSRF ok." comment below. We verify our nonces next.
		if ( $this->action_name !== $_POST['rcptn-action'] ) { // WPCS: CSRF ok.
			return;
		}

		// If the nonce doesn’t validate, we’re not processing anything.
		if ( ! wp_verify_nonce( $_POST[ $this->nonce_name ], $this->nonce_name ) ) {
			wp_die( esc_html__( 'Nonce verification failed.', 'rcptn' ) );
		}

		// Everything checks out... we’re good to process!
		$this->process();
	}

}
