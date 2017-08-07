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
								<textarea name="rcptn_message" id="rcptn_message" cols="30" rows="10" placeholder="Your message..."><?php echo esc_html( $message ); ?></textarea>
								<p class="description">
									<?php esc_html_e( 'Enter the message to send to the chosen region.', 'rcptn' ); ?>
								</p>
							</td>
						</tr>
						</tbody>
					</table>

					<input type="hidden" name="rcptn-action" value="save-settings"/>
					<?php wp_nonce_field( 'rcptn_save_settings_nonce', 'rcptn_save_settings_nonce' ); ?>

					<?php submit_button(); ?>
				</form>
			</div>
		<?php
	}

}
