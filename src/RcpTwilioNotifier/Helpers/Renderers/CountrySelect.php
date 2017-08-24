<?php
/**
 * RCP: RcpTwilioNotifier\Helpers\Renderers\CountrySelect class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Helpers\Renderers
 */

namespace RcpTwilioNotifier\Helpers\Renderers;
use RcpTwilioNotifier\Helpers\CountryLister;

/**
 * Outputs an HTML <select> with the countries as options.
 */
class CountrySelect {

	/**
	 * The user ID to render the select for.
	 *
	 * This affects the currently selected item, if there is one.
	 *
	 * @var int
	 */
	private $user_id;

	/**
	 * Set internal state.
	 *
	 * @param array $args {
	 *    Optional. Provide options to configure the currently selected country.
	 *
	 *    @type int    $user_id               The user ID to render the select for.
	 *    @type string $selected_country_code The currently selected country's code.
	 * }
	 */
	public function __construct( $args ) {
		$defaults = array(
			'user_id'               => false,
			'selected_country_code' => false,
		);

		$this->args = wp_parse_args( $args, $defaults );
	}

	/**
	 * Render the select dropdown.
	 */
	public function render() {
		?>
		<select id="rcptn_phone_country_code" name="rcptn_phone_country_code" class="rcptn-registration-select">
			<?php $this->render_default_option(); ?>
			<?php $this->render_country_options(); ?>
		</select>
		<?php
	}

	/**
	 * Render the default dropdown option.
	 */
	private function render_default_option() {

		?>
		<option value="none">
			<?php echo esc_html( apply_filters( 'rcptn_phone_country_code_select_default_option', __( 'Select a country...', 'rcptn' ) ) ); ?>
		</option>
		<?php

	}

	/**
	 * Render the various country options.
	 */
	private function render_country_options() {

		$current_country = $this->get_current_country();

		foreach ( CountryLister::get_countries() as $country_code => $country_name ) {
			?>
			<option
				value=<?php echo esc_attr( $country_code ); ?>
				<?php selected( $current_country, $country_code ); ?>
			>
				<?php echo esc_html( $country_name ); ?>
			</option>
			<?php
		}

	}

	/**
	 * Get the currently selected country.
	 *
	 * @return string|bool
	 */
	private function get_current_country() {
		if ( $this->args['selected_country_code'] ) {
			return $this->args['selected_country_code'];
		}

		if ( $this->args['user_id'] ) {
			return get_user_meta( $this->args['user_id'], 'rcptn_phone_country_code', true );
		}

		return false;
	}

}
