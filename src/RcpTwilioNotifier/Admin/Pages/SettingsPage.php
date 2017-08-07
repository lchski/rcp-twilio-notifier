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
									__( 'The phone number you purchased from Twilio. Format: +10123456789', 'rcptn' ),
									array( $this, 'render_twilio_from_number' )
								);

								AdminFormField::render(
									'rcptn_rcp_all_regions_subscription_id',
									__( 'RCP All Regions Subscription ID', 'rcptn' ),
									__( 'The ID of the RCP subscription for all regions. Active subscribers to this subscription will receive all text alerts, regardless of their region.', 'rcptn' ),
									array( $this, 'render_rcp_all_regions_subscription_id' )
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
			<input type="text" name="<?php echo esc_attr( $field_id ); ?>" id="<?php echo esc_attr( $field_id ); ?>">
		<?php
	}

	/**
	 * Render the Twilio token field.
	 *
	 * @param string $field_id  The field's ID.
	 */
	public function render_twilio_token( $field_id ) {
		?>
			<input type="text" name="<?php echo esc_attr( $field_id ); ?>" id="<?php echo esc_attr( $field_id ); ?>">
		<?php
	}

	/**
	 * Render the Twilio from number field.
	 *
	 * @param string $field_id  The field's ID.
	 */
	public function render_twilio_from_number( $field_id ) {
		?>
			<input type="tel" name="<?php echo esc_attr( $field_id ); ?>" id="<?php echo esc_attr( $field_id ); ?>">
		<?php
	}

	/**
	 * Render the Twilio Token field.
	 *
	 * @param string $field_id  The field's ID.
	 */
	public function render_rcp_all_regions_subscription_id( $field_id ) {
		?>
			<input type="number" name="<?php echo esc_attr( $field_id ); ?>" id="<?php echo esc_attr( $field_id ); ?>">
		<?php
	}

}
