<?php
/**
 * RCP: RcpTwilioNotifier\Admin\MessagePostType\MetaBoxes\AbstractMetaBox class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Admin\MessagePostType\MetaBoxes
 */

namespace RcpTwilioNotifier\Admin\MessagePostType\MetaBoxes;

/**
 * Handles routine meta box registration function, based on values set in the child class.
 */
abstract class AbstractMetaBox {

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
