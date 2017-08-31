<?php
/**
 * RCP: RcpTwilioNotifier\Admin\MessagePostType\Registrar class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin
 */

namespace RcpTwilioNotifier\Admin\MessagePostType;

use RcpTwilioNotifier\Admin\MessagePostType\MetaBoxes\MessageBodyBox;
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
	}

	/**
	 * Register the post type.
	 */
	public function register() {
		register_post_type(
			$this->post_type_slug, array(
				'label' => __( 'SMS Message', 'rcptn' ),
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
	}
}
