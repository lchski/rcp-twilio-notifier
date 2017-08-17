<?php
/**
 * RCP: RcpTwilioNotifier\Admin\AlertWatcher class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin
 */

namespace RcpTwilioNotifier\Admin;
use RcpTwilioNotifier\Helpers\Notifier;
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
	 */
	public function __construct() {
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

		add_action( 'save_post_' . $this->alert_post_type, array( $this, 'add_messaging_notification' ) );
	}

	/**
	 * Add the notification with the one-click messaging interface.
	 */
	public function add_messaging_notification() {
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
		echo '<p>' . esc_html__( 'Do you want to message your customers?', 'rcptn' ) . '</p>';
	}

}
