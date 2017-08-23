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
		wp_enqueue_script( 'rcptn_registration_handler', plugins_url( '../../../assets/js/registration_handler.js', __FILE__ ), array( 'jquery' ), false, true );
		wp_localize_script( 'rcptn_registration_handler', 'rcptn_registration_handler_data', $this->get_js_data() );
	}

	/**
	 * Retrieve and process the data to provide to the JavaScript.
	 *
	 * @return array
	 */
	private function get_js_data() {
		$rcp_levels = new \RCP_Levels();

		$levels = $rcp_levels->get_levels(
			array(
				'status' => 'active',
			)
		);

		$addon_id_finder = function( $level ) use ( $rcp_levels ) {
			$add_on_level_id = $rcp_levels->get_meta( $level->id, 'rcptn_add_on_level_id', true );

			if ( empty( $add_on_level_id ) ) {
				return false;
			}

			return array(
				'basic_level' => absint( $level->id ),
				'addon_level' => absint( $add_on_level_id ),
			);
		};

		return array(
			'associated_subscription_ids' => array_values( array_filter( array_map( $addon_id_finder, $levels ) ) ),
			'addon_input_label' => get_option( 'rcptn_rcp_addon_input_label', '' ),
		);
	}

}
