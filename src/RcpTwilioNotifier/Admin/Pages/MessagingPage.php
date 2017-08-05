<?php
/**
 * RCP: RcpTwilioNotifier\Admin\Pages\MessagingPage class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin\Pages
 */

namespace RcpTwilioNotifier\Admin\Pages;
use RcpTwilioNotifier\RegionField\SelectRenderer;

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
		$select_renderer = new SelectRenderer( $this->regions, -1 );

		?>
			<div class="wrap" id="<?php esc_attr( $this->menu_slug ); ?>">
				<h1><?php echo esc_html( $this->page_title ); ?></h1>

				<form id="rcptn-region-notifier-messenger" action="" method="post">
					<table class="form-table">
						<tbody>
							<tr class="form-field">
								<th scope="row" valign="top">
									<label for="rcptn_region"><?php esc_html_e( 'Target region', 'rcptn' ); ?></label>
								</th>
								<td>
									<?php $select_renderer->render(); ?>
									<p class="description">
										<?php esc_html_e( 'Choose the region that should receive this notice.', 'rcptn' ); ?>
									</p>
								</td>
							</tr>
							<tr class="form-field">
								<th scope="row" valign="top">
									<label for="rcptn_message"><?php esc_html_e( 'Message', 'rcptn' ); ?></label>
								</th>
								<td>
									<textarea name="rcptn_message" id="rcptn_message" cols="30" rows="10" placeholder="Your message..."></textarea>
									<p class="description">
										<?php esc_html_e( 'Enter the message to send to the chosen region.', 'rcptn' ); ?>
									</p>
								</td>
							</tr>
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

}
