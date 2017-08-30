<?php
/**
 * RCP: RcpTwilioNotifier\Admin\MessagePostTypeRegistrar class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin
 */

namespace RcpTwilioNotifier\Admin;

use RcpTwilioNotifier\Models\Message;

/**
 * Registers the Message custom post type with WordPress.
 */
class MessagePostTypeRegistrar {

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
	}

	/**
	 * Register the post type.
	 */
	public function register() {
		register_post_type(
			$this->post_type_slug, array(
				'label' => __( 'SMS Message', 'rcptn' ),
				'exclude_from_search' => true,
				'publicly_queryable'  => false,
				'show_in_nav_menus'   => false,
				'show_ui'             => true,
				'show_in_admin_bar'   => false,
				'menu_icon'           => 'dashicons-testimonial',
				'capability_type'     => 'post',
				'capabilities'        => array(
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
				'supports' => false,
			)
		);
	}
}
