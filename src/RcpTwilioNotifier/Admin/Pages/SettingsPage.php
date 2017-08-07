<?php
/**
 * RCP: RcpTwilioNotifier\Admin\Pages\SettingsPage class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin\Pages
 */

namespace RcpTwilioNotifier\Admin\Pages;

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
	 * Register additional WordPress hooks.
	 */
	public function register_hooks() {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Register WordPress settings page.
	 */
	public function register_settings() {
		register_setting(
			'rcptn_config', 'twilio_sid', array(
				'type' => 'string',
				'description' => __( 'Account SID from twilio.com/console. (Use the testing one when testing.)' ),
				'sanitize_callback' => array( $this, 'sanitize_twilio_sid' ),
			)
		);

		register_setting(
			'rcptn_config', 'twilio_token', array(
				'type' => 'string',
				'description' => __( 'Auth token from twilio.com/console. (Use the testing one when testing.)' ),
				'sanitize_callback' => array( $this, 'sanitize_twilio_token' ),
			)
		);

		register_setting(
			'rcptn_config', 'twilio_from_number', array(
				'type' => 'string',
				'description' => __( 'A Twilio phone number you purchased at twilio.com.' ),
				'sanitize_callback' => array( $this, 'sanitize_twilio_from_number' ),
			)
		);

		register_setting(
			'rcptn_config', 'rcp_all_regions_subscription_id', array(
				'type' => 'integer',
				'description' => __( 'The ID of the RCP subscription for all regions. Active subscribers to this subscription will receive all text alerts, regardless of their region.' ),
				'sanitize_callback' => array( $this, 'sanitize_rcp_all_regions_subscriptions_id' ),
			)
		);

		add_settings_section( 'lc_listings_options_section', '', array( $this, 'print_section_info' ), 'rcptn-listings-admin' );

		add_settings_field( 'rcptn_', __( 'Listings Server URL', 'lc-listing' ), array( $this, 'server_url_callback' ), 'rcptn-listings-admin', 'lc_listings_options_section' );

		add_settings_field( 'lc_listings_client_url', __( 'Listings Client URL', 'lc-listing' ), array( $this, 'client_url_callback' ), 'rcptn-listings-admin', 'lc_listings_options_section' );
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

				<form method="post" action="options.php">
					<?php
						settings_fields( 'rcptn_config' );
						do_settings_sections( 'rcptn-settings-admin' );
						submit_button();
					?>
				</form>
			</div>
		<?php
	}

}
