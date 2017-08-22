<?php
/**
 * RCP: RcpTwilioNotifier\Admin\RegistrationJavascriptSupporter class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin
 */

namespace RcpTwilioNotifier\Admin;

/**
 * Passes data required by the plugin's JavaScript to the registration form and
 * registers that JavaScript.
 */
class RegistrationJavascriptSupporter {

	/**
	 * Hook into WordPress.
	 */
	public function init() {
		add_action( 'wp_loaded', array( $this, 'register_script' ) );
	}

	/**
	 * Register our JavaScript and its data.
	 */
	public function register_script() {
		wp_localize_script( 'rcptn_registration_handler', 'rcptn_registration_handler_data', $this->get_js_data() );
		wp_enqueue_script( 'rcptn_registration_handler', plugins_url( '../../../assets/js/registration_handler.js', __FILE__ ), array( 'jquery' ), false, true );
	}

	/**
	 * Retrieve and process the data to provide to the JavaScript.
	 *
	 * @return array
	 */
	private function get_js_data() {
		return array();
	}

}
