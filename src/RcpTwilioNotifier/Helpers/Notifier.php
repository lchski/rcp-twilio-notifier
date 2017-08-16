<?php
/**
 * RCP: RcpTwilioNotifier\Helpers\Notifier class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Helpers
 */

namespace RcpTwilioNotifier\Helpers;

use RcpTwilioNotifier\Models\Notice;

/**
 * Handles all admin notices.
 *
 * Thanks to https://github.com/JolekPress/Easy-WordPress-Admin-Notifications for the options trick to
 * enable persistence across `save_post`.
 */
class Notifier {

	/**
	 * The option under which our notices are stored.
	 */
	const NOTICES_OPTION_KEY = 'rcptn_admin_notices';

	/**
	 * Unique plugin instance.
	 *
	 * @var Notifier
	 */
	private static $instance;

	/**
	 * List of notices.
	 *
	 * @var array
	 */
	private $notices = array();

	/**
	 * Return the unique plugin instance.
	 *
	 * @return Notifier
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Notifier constructor.
	 */
	private function __construct() {
		add_action( 'admin_notices', array( $this, 'render_notices' ) );
	}

	/**
	 * Add a notice to display.
	 *
	 * @param Notice $notice  The notice to display.
	 */
	public function add_notice( Notice $notice ) {
		$notices = $this->get_notices();
		$notices[] = $notice;

		$this->update_notices( $notices );
	}

	/**
	 * Render the notices to the admin.
	 */
	public function render_notices() {
		$notices = $this->get_notices();

		if ( empty( $notices ) ) {
			return;
		}

		foreach ( $notices as $notice ) {
			?>
				<div class="notice notice-<?php echo esc_attr( $notice->get_type() ); ?>">
					<p><?php echo esc_html( $notice->get_message() ); ?></p>
				</div>
			<?php
		}

		$this->update_notices( array() );
	}

	/**
	 * Retrieve the notices stored in the database.
	 *
	 * @return Notice[]
	 */
	private function get_notices() {
		return get_option( self::NOTICES_OPTION_KEY, array() );
	}

	/**
	 * Update the notices stored in the database.
	 *
	 * @param Notice[] $notices  The notices to store.
	 */
	private function update_notices( $notices ) {
		update_option( self::NOTICES_OPTION_KEY, $notices );
	}

}
