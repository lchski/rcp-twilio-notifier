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
 */
class Notifier {

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
		$this->notices[] = $notice;
	}

	/**
	 * Render the notices to the admin.
	 */
	public function render_notices() {
		if ( 0 === count( $this->notices ) ) {
			return;
		}

		foreach ( $this->notices as $notice ) {
			?>
				<div class="notice notice-<?php echo esc_attr( $notice->get_type() ); ?>">
					<p><?php echo esc_html( $notice->get_message() ); ?></p>
				</div>
			<?php
		}
	}

}
