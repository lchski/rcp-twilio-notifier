<?php
/**
 * RcpTwilioNotifier: RcpTwilioNotifier\Models\Message class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Models
 */

namespace RcpTwilioNotifier\Models;
use RcpTwilioNotifier\Helpers\MemberRetriever;
use Twilio\Rest\Api\V2010\Account\MessageInstance;

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
		} else {
			return new \WP_Error(
				'rcptn_message_no_wp_post',
				__( 'Cannot find a WP Post using the identifier given.', 'rcptn' ),
				array(
					'message_identifier' => $message_identifier,
				)
			);
		}

		$this->wp_post = $message_post;

		$this->recipients = MemberRetriever::convert_user_ids_to_members( get_post_meta( $this->wp_post->ID, 'rcptn_recipient_ids', true ) );

		$this->message_body = new MessageBody( $this->wp_post->post_content, get_post_meta( $this->wp_post->ID, 'rcptn_body_data', true ) );

		$send_attempts = get_post_meta( $this->wp_post->ID, 'rcptn_send_attempts', true );

		$this->send_attempts = ( '' !== $send_attempts ) ? $send_attempts : array();
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
	 * @return Message|\WP_Error
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
			return new \WP_Error( 'rcptn_message_missing_recipients', __( 'No recipients were provided.', 'rcptn' ) );
		}

		// Make sure we have a message body.
		if ( empty( $args['raw_body'] ) || 0 === strlen( $args['raw_body'] ) ) {
			return new \WP_Error( 'rcptn_message_missing_body', __( 'No message body was provided.', 'rcptn' ) );
		}

		// Set up arguments for the WP_Post.
		$post_args = array(
			'post_type'      => self::POST_TYPE,
			'post_status'    => 'publish',
			'post_author'    => wp_get_current_user()->ID,
			'post_title'     => wp_trim_words( $args['raw_body'], 15 ),
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
	 * Get the send attempts for a given recipient.
	 *
	 * @param Member $recipient  The recipient to check the send attempts for.
	 *
	 * @return bool|array|\WP_Error
	 */
	public function get_send_attempts_for_recipient( $recipient ) {
		$send_attempt = array_values(
			array_filter(
				$this->send_attempts, function( $send_attempt ) use ( $recipient ) {
					return $send_attempt['recipient'] === $recipient->ID;
				}
			)
		);

		if ( empty( $send_attempt ) ) {
			return false;
		}

		if ( 1 === count( $send_attempt ) ) {
			return $send_attempt[0];
		}

		return new \WP_Error(
			'rcptn_message_multiple_send_attempts',
			__( 'Multiple send attempts exist for that recipient.', 'rcptn' ),
			array(
				'send_attempts' => $send_attempt,
			)
		);
	}

	/**
	 * Get the message's body.
	 *
	 * @return MessageBody
	 */
	public function get_message_body() {
		return $this->message_body;
	}

	/**
	 * Send the message to all recipients.
	 */
	public function send_to_all() {
		foreach ( $this->recipients as $member ) {
			$this->send( $member );
		}

		$this->save_send_attempts();
	}

	/**
	 * Send the message to multiple specified people.
	 *
	 * @param Member[] $recipients  The Members to message.
	 */
	public function send_to_some( $recipients ) {
		foreach ( $recipients as $recipient ) {
			$this->send( $recipient );
		}

		$this->save_send_attempts();
	}

	/**
	 * Send the message to one person in particular.
	 *
	 * @param Member $recipient  The Member to message.
	 */
	public function send_to_one( $recipient ) {
		$this->send( $recipient );

		$this->save_send_attempts();
	}

	/**
	 * Resend the message to those for whom it failed previously.
	 */
	public function retry_failed_sends() {
		// Get the failed sends.
		$failed_sends = array_values(
			array_filter(
				$this->send_attempts, function( $send_attempt ) {
					return 'failed' === $send_attempt['status'];
				}
			)
		);

		// Bail if there are no failed sends.
		if ( empty( $failed_sends ) ) {
			return;
		}

		// Get the recipients of the failed sends as Member objects.
		$recipients = array_map(
			function( $send_attempt ) {
					return new Member( $send_attempt['recipient'] );
			}, $failed_sends
		);

		// Message the recipients of the failed sends.
		$this->send_to_some( $recipients );
	}

	/**
	 * Send a message.
	 *
	 * Note: This is the private method handling sending only. The public `send_to_one`
	 *       method handles saving metadata once the request is closed out.
	 *
	 * @param Member $recipient  The Member to message.
	 */
	private function send( $recipient ) {
		$sms_request = $recipient->send_message( $this->message_body );

		$this->record_send_attempt( $sms_request, $recipient );
	}

	/**
	 * Record the SMS request.
	 *
	 * @param MessageInstance|\WP_Error $sms_response  The SMS API response.
	 * @param Member                    $recipient     The member who was messaged.
	 */
	private function record_send_attempt( $sms_response, $recipient ) {
		if ( $sms_response instanceof \WP_Error ) {
			$send_attempt = array(
				'recipient' => $recipient->ID,
				'status'    => 'failed',
				'error'     => $sms_response->get_error_message(),
			);
		} elseif ( $sms_response instanceof MessageInstance ) {
			$send_attempt = array(
				'recipient' => $recipient->ID,
				'status'    => 'success',
			);
		}

		// If there's an existing send attempt for this recipient, overwrite the existing list to exclude that attempt.
		// We don't want to have two attempts for the same recipient.
		if ( false !== $this->get_send_attempts_for_recipient( $recipient ) ) {
			$this->send_attempts = array_filter(
				$this->send_attempts, function( $send_attempt ) use ( $recipient ) {
					return $send_attempt['recipient'] !== $recipient->ID;
				}
			);
		}

		// Add the send attempt to the list.
		$this->send_attempts[] = $send_attempt;
	}

	/**
	 * Save the send attempts to metadata.
	 *
	 * @return bool|\WP_Error
	 */
	private function save_send_attempts() {
		$update_attempt = update_post_meta( $this->wp_post->ID, 'rcptn_send_attempts', $this->send_attempts );

		if ( false === $update_attempt ) {
			return new \WP_Error(
				'rcptn_message_save_send_attempts_failed',
				__( 'Failed to save the send attempts metadata.', 'rcptn' ),
				array(
					'send_attempts' => $this->send_attempts,
				)
			);
		}

		return true;
	}

}
