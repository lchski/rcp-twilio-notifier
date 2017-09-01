<?php
/**
 * RCP: RcpTwilioNotifier\Admin\MessagePostType\Registrar class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin
 */

namespace RcpTwilioNotifier\Admin\MessagePostType;

use RcpTwilioNotifier\Admin\MessagePostType\MetaBoxes\MessageBodyBox;
use RcpTwilioNotifier\Admin\MessagePostType\MetaBoxes\SendAttemptsBox;
use RcpTwilioNotifier\Models\Message;

/**
 * Registers the Message custom post type with WordPress.
 */
class Registrar {

	/**
	 * The slug for the post type.
	 *
	 * @var string
	 */
	private $post_type_slug;

	/**
	 * Set internal values.
	 */
	public function __construct() {
		$this->post_type_slug = Message::POST_TYPE;
	}

	/**
	 * Add hooks with WordPress.
	 */
	public function init() {
		add_action( 'init', array( $this, 'register' ) );

		add_filter( 'bulk_actions-edit-' . $this->post_type_slug, array( $this, 'remove_bulk_actions' ) );
		add_filter( 'post_row_actions', array( $this, 'remove_row_actions' ) );
		add_filter( 'get_user_option_screen_layout_' . $this->post_type_slug, array( $this, 'set_one_column_editor_layout' ) );
		add_action( 'admin_menu', array( $this, 'remove_publish_box' ) );
	}

	/**
	 * Register the post type.
	 */
	public function register() {
		register_post_type(
			$this->post_type_slug, array(
				'exclude_from_search'  => true,
				'publicly_queryable'   => false,
				'show_in_nav_menus'    => false,
				'show_ui'              => true,
				'show_in_admin_bar'    => false,
				'menu_icon'            => 'dashicons-testimonial',
				'capability_type'      => 'post',
				'supports'             => false,
				'register_meta_box_cb' => array( $this, 'register_meta_boxes' ),
				'capabilities'         => array(
					'edit_post'          => 'rcp_view_members',
					'read_post'          => 'rcp_view_members',
					'delete_post'        => false,
					'edit_posts'         => 'rcp_view_members',
					'edit_others_posts'  => 'rcp_view_members',
					'delete_posts'       => false,
					'publish_posts'      => false,
					'read_private_posts' => 'rcp_view_members',
					'create_posts'       => false,
				),
				'labels' => array(
					'name' => __( 'SMS Messages', 'rcptn' ),
					'singular_name' => __( 'Message', 'rcptn' ),
					'edit_item' => __( 'View Message', 'rcptn' ),
					'view_item' => __( 'View Message', 'rcptn' ),
					'view_items' => __( 'View Messages', 'rcptn' ),
					'search_items' => __( 'Search Messages', 'rcptn' ),
					'not_found' => __( 'No messages found', 'rcptn' ),
					'not_found_in_trash' => __( 'No messages found in Trash', 'rcptn' ),
					'all_items' => __( 'All Messages', 'rcptn' ),
					'filter_items_list' => __( 'Filter messages list', 'rcptn' ),
					'items_list_navigation' => __( 'Messages list navigation', 'rcptn' ),
					'items_list' => __( 'Messages list', 'rcptn' ),
				),
			)
		);
	}

	/**
	 * Register the meta boxes for the post type editing screen.
	 *
	 * @param \WP_Post $post  The currently edited post.
	 */
	public function register_meta_boxes( $post ) {
		$message_body_box = new MessageBodyBox( $post );
		$message_body_box->register();

		$send_attempts_box = new SendAttemptsBox( $post );
		$send_attempts_box->register();
	}

	/**
	 * Disable the editing bulk action.
	 *
	 * @param array $actions  The list of possible bulk actions.
	 *
	 * @return array
	 */
	public function remove_bulk_actions( $actions ) {
		unset( $actions['edit'] );
		return $actions;
	}

	/**
	 * Remove the controls in the list of messages.
	 *
	 * @param array $actions  The list of possible post actions.
	 *
	 * @return array
	 */
	public function remove_row_actions( $actions ) {
		global $current_screen;

		if ( $current_screen->post_type !== $this->post_type_slug ) {
			return $actions;
		}

		unset( $actions['edit'] );
		unset( $actions['view'] );
		unset( $actions['trash'] );
		unset( $actions['inline hide-if-no-js'] );

		return $actions;
	}

	/**
	 * Force the editor to one column on the CPT page.
	 *
	 * @return int
	 */
	public function set_one_column_editor_layout() {
		return 1;
	}

	/**
	 * Remove the publish box on the CPT page.
	 */
	public function remove_publish_box() {
		remove_meta_box( 'submitdiv', $this->post_type_slug, 'side' );
	}

}
