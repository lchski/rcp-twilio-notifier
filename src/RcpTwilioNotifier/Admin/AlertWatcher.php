<?php
/**
 * RCP: RcpTwilioNotifier\Admin\AlertWatcher class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin
 */

namespace RcpTwilioNotifier\Admin;
use RcpTwilioNotifier\Helpers\Notifier;
use RcpTwilioNotifier\Helpers\Renderers\MessagingUi;
use RcpTwilioNotifier\Helpers\Renderers\RegionSelect;
use RcpTwilioNotifier\Models\Notice;

/**
 * Watches for the publication of travel alert posts, triggering a notification with
 * the one-click messaging interface if so.
 */
class AlertWatcher {

	/**
	 * The post type to watch.
	 *
	 * @var string|bool
	 */
	private $alert_post_type;

	/**
	 * Set internal values.
	 *
	 * @param array $regions  Regions available for messaging.
	 */
	public function __construct( $regions ) {
		$this->regions         = $regions;
		$this->alert_post_type = get_option( 'rcptn_alert_post_type' );
	}

	/**
	 * Set up the watcher.
	 */
	public function init() {
		// Bail if we don’t have a post type to watch.
		if ( false === $this->alert_post_type ) {
			return;
		}

		add_action( 'save_post_' . $this->alert_post_type, array( $this, 'add_messaging_notification' ), 10, 2 );
	}

	/**
	 * Add the notification with the one-click messaging interface.
	 *
	 * @param int      $post_id  The post's ID.
	 * @param \WP_Post $post     The post object.
	 */
	public function add_messaging_notification( $post_id, $post ) {
		// Bail if post revision.
		if ( wp_is_post_revision( $post ) ) {
			return;
		}

		// Bail if not published.
		if ( isset( $post->post_status ) && 'publish' !== $post->post_status ) {
			return;
		}

		$notifier = Notifier::get_instance();
		$notifier->add_notice(
			new Notice(
				'info',
				array( $this, 'render_interface' )
			)
		);
	}

	/**
	 * Render the one-click messaging interface.
	 */
	public function render_interface() {
		$select_renderer = new RegionSelect(
			$this->regions,
			array(
				'selected_region_slug' => isset( $_POST['rcptn_region'] ) ? $_POST['rcptn_region'] : false, // WPCS: CSRF ok.
				'include_all_regions_option' => true,
			)
		);

		$message_value = get_option( 'rcptn_automated_message_template', '' );

		echo '<p>' . esc_html__( 'Would you like to message a region about this alert?', 'rcptn' ) . '</p>';

		MessagingUi::render(
			array(
				'region_renderer'    => $select_renderer,
				'message_value'      => $message_value,
				'extra_data'         => array(
					'post_ID' => get_the_ID(),
				),
				'enabled_merge_tags' => array(
					'|*FIRST_NAME*|',
					'|*LAST_NAME*|',
					'|*ALERT_LINK*|',
				),
			)
		);
	}

}
