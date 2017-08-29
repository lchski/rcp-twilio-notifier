<?php
/**
 * RcpTwilioNotifier: RcpTwilioNotifier\Models\Message class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Models
 */

namespace RcpTwilioNotifier\Models;
use RcpTwilioNotifier\Helpers\MemberRetriever;

/**
 * A message sent to members.
 */
class Message {

	/**
	 * The post type slug for the Member CPT.
	 */
	const POST_TYPE = 'rcptn_message';

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
	 * The MessageBody instance representing the raw message and the data to process it.
	 *
	 * @var MessageBody
	 */
	private $message_body;

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

		$this->wp_post = $message_post;

		$this->recipients = MemberRetriever::convert_user_ids_to_members( get_post_meta( $this->wp_post->ID, 'rcptn_recipient_ids', true ) );

		$this->message_body = new MessageBody( $this->wp_post->post_content, get_post_meta( $this->wp_post->ID, 'rcptn_body_data', true ) );

		$this->send_attempts = array();
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
		$wp_post_id = wp_insert_post( $post_args, true );

		// Bail if we got an error.
		if ( is_wp_error( $wp_post_id ) ) {
			return $wp_post_id;
		}

		// Retrieve just the IDs from the array of Recipients.
		$recipient_ids = array_map(
			function( Member $recipient ) {
					return $recipient->ID;
			}, $args['recipients']
		);

		// Setting metadata...
		add_post_meta( $wp_post_id, 'rcptn_recipient_ids', $recipient_ids );
		add_post_meta( $wp_post_id, 'rcptn_body_data', $args['body_data'] );

		// Return a new instance of the Message object now that all the settings are in place.
		return new Message( $wp_post_id );
	}

	/**
	 * Send the message to all recipients.
	 */
	public function send_to_all() {
		foreach ( $this->recipients as $member ) {
			$this->send_to_one( $member );
		}
	}

	/**
	 * Send the message to one recipient in particular.
	 *
	 * @param Member $recipient  The Member to message.
	 */
	public function send_to_one( $recipient ) {
		$sms_request = $recipient->send_message( $this->message_body );

		$this->record_send_attempt( $sms_request, $recipient );
	}

}
