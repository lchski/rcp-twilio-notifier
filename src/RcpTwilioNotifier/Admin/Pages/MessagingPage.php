<?php
/**
 * RCP: RcpTwilioNotifier\Admin\Pages\MessagingPage class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin\Pages
 */

namespace RcpTwilioNotifier\Admin\Pages;
use RcpTwilioNotifier\Helpers\Renderers\AdminFormField;
use RcpTwilioNotifier\Helpers\Renderers\RegionSelect;

/**
 * WordPress admin page for messaging members by their region.
 */
class MessagingPage extends AbstractPage implements PageInterface {

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
	 * List of regions available for messaging.
	 *
	 * @var array
	 */
	private $regions;

	/**
	 * Set internal values.
	 *
	 * @param array $regions  Regions available for messaging.
	 */
	public function __construct( $regions ) {
		$this->regions = $regions;

		$this->page_title = __( 'Region Notifier', 'rcptn' );
		$this->menu_title = __( 'Region Notifier', 'rcptn' );
		$this->menu_slug = 'rcptn-region-notifier';
	}

	/**
	 * Render the UI for the messaging page.
	 *
	 * @return void
	 */
	public function render() {
		$select_renderer = new RegionSelect(
			$this->regions,
			array(
				'selected_region_slug' => isset( $_POST['rcptn_region'] ) ? $_POST['rcptn_region'] : false, // WPCS: CSRF ok.
			)
		);

		?>
			<div class="wrap" id="<?php esc_attr( $this->menu_slug ); ?>">
				<h1><?php echo esc_html( $this->page_title ); ?></h1>

				<form id="rcptn-region-notifier-messenger" action="" method="post">
					<table class="form-table">
						<tbody>
							<?php
								AdminFormField::render(
									'rcptn_region',
									__( 'Target region', 'rcptn' ),
									__( 'Choose the region that should receive this notice.', 'rcptn' ),
									array( $select_renderer, 'render' )
								);

								AdminFormField::render(
									'rcptn_message',
									__( 'Message', 'rcptn' ),
									__( 'Enter the message to send to the chosen region.', 'rcptn' ),
									array( $this, 'render_message_field' )
								);
							?>
						</tbody>
					</table>
					<p class="submit">
						<input type="hidden" name="rcptn-action" value="send-single-message"/>
						<input type="submit" value="<?php esc_attr_e( 'Send Message', 'rcptn' ); ?>" class="button-primary"/>
					</p>
					<?php wp_nonce_field( 'rcptn_send_single_message_nonce', 'rcptn_send_single_message_nonce' ); ?>
				</form>
			</div>
		<?php
	}

	/**
	 * * Render the message body field.
	 *
	 * @param string $field_id  The field's ID.
	 */
	public function render_message_field( $field_id ) {
		$message = isset( $_POST['rcptn_message'] ) ? $_POST['rcptn_message'] : ''; // WPCS: CSRF ok.

		?>
			<textarea name="<?php echo esc_attr( $field_id ); ?>" id="<?php echo esc_attr( $field_id ); ?>" cols="30" rows="10" placeholder="<?php esc_attr_e( 'Your message...', 'rcptn' ); ?>"><?php echo esc_html( $message ); ?></textarea>
		<?php
	}

}
