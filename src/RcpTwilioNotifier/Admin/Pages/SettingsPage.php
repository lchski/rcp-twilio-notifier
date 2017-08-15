<?php
/**
 * RCP: RcpTwilioNotifier\Admin\Pages\SettingsPage class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin\Pages
 */

namespace RcpTwilioNotifier\Admin\Pages;
use RcpTwilioNotifier\Helpers\Renderers\AdminFormField;

/**
 * WordPress admin page for configuring plugin settings.
 */
class SettingsPage extends AbstractPage implements PageInterface {

	/**
	 * Page title
	 *
	 * @see add_submenu_page
	 * @var string
	 */
	protected $page_title;

	/**
	 * Menu title
	 *
	 * @see add_submenu_page
	 * @var string
	 */
	protected $menu_title;

	/**
	 * Menu slug (must be unique)
	 *
	 * @see add_submenu_page
	 * @var string
	 */
	protected $menu_slug;

	/**
	 * Set internal values.
	 */
	public function __construct() {
		$this->page_title = __( 'Region Notifier Settings', 'rcptn' );
		$this->menu_title = __( 'Region Notifier Settings', 'rcptn' );
		$this->menu_slug = 'rcptn-region-notifier-settings';
	}

	/**
	 * Render the UI for the messaging page.
	 *
	 * @return void
	 */
	public function render() {
		?>
			<div class="wrap" id="<?php esc_attr( $this->menu_slug ); ?>">
				<h1><?php echo esc_html( $this->page_title ); ?></h1>

				<form id="rcptn-region-notifier-settings" method="post" action="">
					<h2 class="title"><?php esc_html_e( 'Twilio related settings', 'rcptn' ); ?></h2>
					<table class="form-table">
						<tbody>
							<?php
								AdminFormField::render(
									'rcptn_twilio_sid',
									__( 'Twilio SID', 'rcptn' ),
									__( 'The account SID from twilio.com/console. (When testing, be sure to use the testing credential.)', 'rcptn' ),
									array( $this, 'render_twilio_sid' )
								);

								AdminFormField::render(
									'rcptn_twilio_token',
									__( 'Twilio Token', 'rcptn' ),
									__( 'The account token from twilio.com/console. (When testing, be sure to use the testing credential.)', 'rcptn' ),
									array( $this, 'render_twilio_token' )
								);

								AdminFormField::render(
									'rcptn_twilio_from_number',
									__( 'Twilio From Number', 'rcptn' ),
									__( 'The phone number you purchased from Twilio. Format: +10123456789. (When testing, use “+15005550006”; Twilio will always accept it.)', 'rcptn' ),
									array( $this, 'render_twilio_from_number' )
								);
							?>
						</tbody>
					</table>

					<h2 class="title"><?php esc_html_e( 'Restrict Content Pro related settings', 'rcptn' ); ?></h2>
					<table class="form-table">
						<tbody>
							<?php
								AdminFormField::render(
									'rcptn_rcp_all_regions_subscription_id',
									__( 'RCP All Regions Subscription ID', 'rcptn' ),
									__( 'The ID of the RCP subscription for all regions. Active subscribers to this subscription will receive all text alerts, regardless of their region.', 'rcptn' ),
									array( $this, 'render_rcp_all_regions_subscription_id' )
								);
							?>
						</tbody>
					</table>

					<h2 class="title"><?php esc_html_e( 'WordPress related settings', 'rcptn' ); ?></h2>
					<table class="form-table">
						<tbody>
							<?php
								AdminFormField::render(
									'rcptn_alert_cpt',
									__( 'Alert Post Type Name', 'rcptn' ),
									__( 'The name of the post type being used for alerts. Can be set to “post” if the default posts are being used. This is used to trigger automatic SMS notices when alerts are published.', 'rcptn' ),
									array( $this, 'render_alert_cpt' )
								);
							?>
						</tbody>
					</table>

					<input type="hidden" name="rcptn-action" value="save-settings"/>
					<?php wp_nonce_field( 'rcptn_save_settings_nonce', 'rcptn_save_settings_nonce' ); ?>

					<?php submit_button(); ?>
				</form>
			</div>
		<?php
	}

	/**
	 * Render the Twilio SID field.
	 *
	 * @param string $field_id  The field's ID.
	 */
	public function render_twilio_sid( $field_id ) {
		?>
			<input type="text" name="<?php echo esc_attr( $field_id ); ?>" id="<?php echo esc_attr( $field_id ); ?>" value="<?php echo esc_attr( $this->get_field_value( $field_id ) ); ?>">
		<?php
	}

	/**
	 * Render the Twilio token field.
	 *
	 * @param string $field_id  The field's ID.
	 */
	public function render_twilio_token( $field_id ) {
		?>
			<input type="text" name="<?php echo esc_attr( $field_id ); ?>" id="<?php echo esc_attr( $field_id ); ?>" value="<?php echo esc_attr( $this->get_field_value( $field_id ) ); ?>">
		<?php
	}

	/**
	 * Render the Twilio from number field.
	 *
	 * @param string $field_id  The field's ID.
	 */
	public function render_twilio_from_number( $field_id ) {
		?>
			<input type="tel" name="<?php echo esc_attr( $field_id ); ?>" id="<?php echo esc_attr( $field_id ); ?>" value="<?php echo esc_attr( $this->get_field_value( $field_id ) ); ?>">
		<?php
	}

	/**
	 * Render the Twilio Token field.
	 *
	 * @param string $field_id  The field's ID.
	 */
	public function render_rcp_all_regions_subscription_id( $field_id ) {
		?>
			<input type="number" name="<?php echo esc_attr( $field_id ); ?>" id="<?php echo esc_attr( $field_id ); ?>" value="<?php echo esc_attr( $this->get_field_value( $field_id ) ); ?>">
		<?php
	}

	/**
	 * Render the Twilio Token field.
	 *
	 * @param string $field_id  The field's ID.
	 */
	public function render_alert_cpt( $field_id ) {
		?>
			<input type="text" name="<?php echo esc_attr( $field_id ); ?>" id="<?php echo esc_attr( $field_id ); ?>" value="<?php echo esc_attr( $this->get_field_value( $field_id ) ); ?>">
		<?php
	}

	/**
	 * Get the value for a given field.
	 *
	 * The value is determined as follows:
	 *
	 *     1. If there’s posted data, we pull the value from there.
	 *     2. If it doesn’t exist in the database as an option, we return an empty string.
	 *     3. If it does exist in the database, we return the option value.
	 *
	 * @param string $field_id  The ID of the field for which to retrieve the value.
	 *
	 * @return mixed|string|void
	 */
	private function get_field_value( $field_id ) {
		if ( isset( $_POST[ $field_id ] ) ) { // WPCS: CSRF ok.
			return $_POST[ $field_id ]; // WPCS: CSRF ok.
		}

		$field_option_value = get_option( $field_id );

		if ( false === $field_option_value ) {
			return '';
		}

		return $field_option_value;
	}

}
