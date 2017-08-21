<?php
/**
 * RCP: RcpTwilioNotifier\Admin\Pages\SettingsPage class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin\Pages
 */

namespace RcpTwilioNotifier\Admin\Pages;
use RcpTwilioNotifier\Helpers\MergeTags;
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
					<h2 class="title"><?php esc_html_e( 'One-click Messaging settings', 'rcptn' ); ?></h2>
					<table class="form-table">
						<tbody>
							<?php
								AdminFormField::render(
									'rcptn_enable_automated_messaging',
									__( 'Enable One-click Messaging', 'rcptn' ),
									__( 'The plugin can automatically prompt you to message members whenever you create a new alert. Required for one-click features.', 'rcptn' ),
									array( $this, 'render_enable_automated_messaging' ),
									array(
										'required' => false,
									)
								);

								AdminFormField::render(
									'rcptn_automated_message_template',
									__( 'Message Template', 'rcptn' ),
									array_merge(
										array(
											__( 'The template used for one-click messages. Whenever you publish an alert, you’ll be prompted to alert your members; this template will be pre-filled to enable one-click messaging. You’ll still be able to change the message before it sends. Required for one-click features.', 'rcptn' ),
											__( 'Several merge tags are available. These will be automatically replaced with their real values when the message is sent:', 'rcptn' ),
										),
										MergeTags::get_merge_tag_descriptions( array( '|*FIRST_NAME*|', '|*LAST_NAME*|', '|*ALERT_LINK*|' ) )
									),
									array( $this, 'render_automated_message_template' ),
									array(
										'required' => false,
									)
								);

								AdminFormField::render(
									'rcptn_alert_post_type',
									__( 'Alert Post Type Name', 'rcptn' ),
									__( 'The name of the post type being used for alerts. Can be set to “post” if the default posts are being used. This is used to trigger automatic SMS notices when alerts are published. Required for one-click features.', 'rcptn' ),
									array( $this, 'render_alert_post_type' ),
									array(
										'required' => false,
									)
								);
							?>
						</tbody>
					</table>

					<h2 class="title"><?php esc_html_e( 'Customer settings', 'rcptn' ); ?></h2>
					<table class="form-table">
						<tbody>
							<?php
								AdminFormField::render(
									'rcptn_welcome_message',
									__( 'Welcome Message', 'rcptn' ),
									array_merge(
										array(
											__( 'The message sent via SMS to a new customer’s phone number when they sign up. Several merge tags are available:', 'rcptn' ),
											__( 'Several merge tags are available. These will be automatically replaced with their real values when the message is sent:', 'rcptn' ),
										),
										MergeTags::get_merge_tag_descriptions( array( '|*FIRST_NAME*|', '|*LAST_NAME*|' ) )
									),
									array( $this, 'render_welcome_message' )
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
	public function render_alert_post_type( $field_id ) {
		?>
			<input type="text" name="<?php echo esc_attr( $field_id ); ?>" id="<?php echo esc_attr( $field_id ); ?>" value="<?php echo esc_attr( $this->get_field_value( $field_id ) ); ?>">
		<?php
	}

	/**
	 * Render the Twilio Token field.
	 *
	 * @param string $field_id  The field's ID.
	 */
	public function render_automated_message_template( $field_id ) {
		?>
			<textarea name="<?php echo esc_attr( $field_id ); ?>" id="<?php echo esc_attr( $field_id ); ?>" cols="30" rows="4"><?php echo esc_html( $this->get_field_value( $field_id ) ); ?></textarea>
		<?php
	}
	/**
	 * Render the welcome message field.
	 *
	 * @param string $field_id  The field's ID.
	 */
	public function render_welcome_message( $field_id ) {
		?>
			<textarea name="<?php echo esc_attr( $field_id ); ?>" id="<?php echo esc_attr( $field_id ); ?>" cols="30" rows="4"><?php echo esc_html( $this->get_field_value( $field_id ) ); ?></textarea>
		<?php
	}

	/**
	 * Render the Twilio Token field.
	 *
	 * @param string $field_id  The field's ID.
	 */
	public function render_enable_automated_messaging( $field_id ) {
		$checked = get_option( $field_id );

		if ( isset( $_POST[ $field_id ] ) ) { // WPCS: CSRF ok.
			$checked = true;
		}

		?>
			<input type="checkbox" name="<?php echo esc_attr( $field_id ); ?>" id="<?php echo esc_attr( $field_id ); ?>" <?php echo ( $checked ) ? 'checked' : ''; ?>>
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
