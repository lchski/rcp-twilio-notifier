<?php
/**
 * RcpTwilioNotifier: RcpTwilioNotifier\Models\Message class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Models
 */

namespace RcpTwilioNotifier\Models;

/**
 * A message sent to members.
 */
class Message {

	/**
	 * The WordPress Post this object represents.
	 *
	 * @var \WP_Post
	 */
	private $wp_post;

	/**
	 * The recipients of this message.
	 *
	 * @var Member[]
	 */
	private $recipients;

	/**
	 * The unprocessed body of the message.
	 *
	 * @var string
	 */
	private $raw_body;

	/**
	 * Additional data to include when processing the message.
	 *
	 * @var array
	 */
	private $body_data;

	/**
	 * Attempted sends for each recipient, including retries.
	 *
	 * @var array
	 */
	private $send_attempts;

	/**
	 * Set internal values.
	 *
	 * @param int|\WP_Post $message_identifier  The ID of the \WP_Post this represents, or the \WP_Post itself.
	 */
	public function __construct( $message_identifier ) {
		if ( is_numeric( $message_identifier ) ) {
			// It’s an ID, create a new \WP_Post object from it.
			$message_post = get_post( $message_identifier );
		} elseif ( $message_identifier instanceof \WP_Post ) {
			// It’s already a \WP_Post object.
			$message_post = $message_identifier;
		}

		$this->wp_post = $message_identifier;
	}

	/**
	 * Create a new Message.
	 *
	 * @param array $args {
	 *     Required. The data necessary to create a new Message.
	 *
	 *     @type Member[] $recipients  Recipients of the message.
	 *     @type string   $raw_body    The unprocessed message body.
	 *     @type array    $body_data   Data required to process the message body.
	 * }
	 *
	 * @return Message
	 */
	public static function create( $args ) {
		$defaults = array(
			'recipients' => array(),
			'raw_body'   => '',
			'body_data'  => array(),
		);

		$args = wp_parse_args( $args, $defaults );

		// Make sure we have a list of recipients.
		if ( empty( $args['recipients'] ) || 0 === count( $args['recipients'] ) ) {
			return new WP_Error( 'rcptn_message_missing_recipients', __( 'No recipients were provided.', 'rcptn' ) );
		}

		// Make sure we have a message body.
		if ( empty( $args['raw_body'] ) || 0 === strlen( $args['raw_body'] ) ) {
			return new WP_Error( 'rcptn_message_missing_body', __( 'No message body was provided.', 'rcptn' ) );
		}

		// Set up arguments for the WP_Post.
		$post_args = array(
			'post_type'      => 'message',
			'post_status'    => 'publish',
			'post_author'    => wp_get_current_user()->ID,
			'post_content'   => $args['raw_body'],
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
		);

		// Try to create the WP_Post.
		$wp_post = wp_insert_post( $post_args, true );

		// Bail if we got an error.
		if ( is_wp_error( $wp_post ) ) {
			return $wp_post;
		}

		// Success! Create a new instance of the Message object, using the newly created post ID.
		$message = new Message( $wp_post );

		// Setting metadata...
		return $message;
	}

}
