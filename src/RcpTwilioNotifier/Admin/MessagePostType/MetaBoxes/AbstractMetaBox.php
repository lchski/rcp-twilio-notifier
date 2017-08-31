<?php
/**
 * RCP: RcpTwilioNotifier\Admin\MessagePostType\MetaBoxes\AbstractMetaBox class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin\MessagePostType\MetaBoxes
 */

namespace RcpTwilioNotifier\Admin\MessagePostType\MetaBoxes;
use RcpTwilioNotifier\Models\Message;

/**
 * Handles routine meta box registration function, based on values set in the child class.
 */
abstract class AbstractMetaBox {

	/**
	 * The currently edited Message.
	 *
	 * @var Message
	 */
	protected $message;

	/**
	 * The meta box's ID.
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * The meta box's title.
	 *
	 * @var string
	 */
	protected $title;

	/**
	 * Set internal values.
	 *
	 * @param \WP_Post $post  The currently edited post.
	 */
	public function __construct( \WP_Post $post ) {
		$this->message = new Message( $post );
	}

	/**
	 * Register the meta box.
	 */
	public function register() {
		add_meta_box(
			$this->id,
			$this->title,
			array( $this, 'render' )
		);
	}

}
