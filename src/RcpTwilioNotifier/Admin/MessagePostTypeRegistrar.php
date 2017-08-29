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
				'public' => false,
				'supports' => array(
					'editor',
					'custom-fields',
				),
			)
		);
	}
}
