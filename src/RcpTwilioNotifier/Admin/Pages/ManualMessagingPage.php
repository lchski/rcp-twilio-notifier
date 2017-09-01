<?php
/**
 * RCP: RcpTwilioNotifier\Admin\Pages\ManualMessagingPage class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin\Pages
 */

namespace RcpTwilioNotifier\Admin\Pages;
use RcpTwilioNotifier\Helpers\Renderers\AdminFormField;
use RcpTwilioNotifier\Helpers\Renderers\MessagingUi;
use RcpTwilioNotifier\Helpers\Renderers\RegionSelect;
use RcpTwilioNotifier\Models\Message;

/**
 * WordPress admin page for messaging members by their region.
 */
class ManualMessagingPage extends AbstractPage implements PageInterface {

	/**
	 * The slug of the parent page under which this page should sit.
	 *
	 * @see add_submenu_page
	 * @var string
	 */
	protected $parent_slug;

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

		$this->parent_slug = 'edit.php?post_type=' . Message::POST_TYPE;
		$this->page_title = __( 'Region Notifier', 'rcptn' );
		$this->menu_title = __( 'Send New', 'rcptn' );
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
				'include_all_regions_option' => true,
			)
		);

		$message_value = isset( $_POST['rcptn_message'] ) ? $_POST['rcptn_message'] : ''; // WPCS: CSRF ok.

		?>
			<div class="wrap" id="<?php esc_attr( $this->menu_slug ); ?>">
				<h1><?php echo esc_html( $this->page_title ); ?></h1>

				<?php
					MessagingUi::render(
						array(
							'region_renderer' => $select_renderer,
							'message_value' => $message_value,
						)
					);
				?>
			</div>
		<?php
	}

}
