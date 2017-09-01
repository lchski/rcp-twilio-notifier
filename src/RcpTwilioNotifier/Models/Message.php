<?php
/**
 * RcpTwilioNotifier: RcpTwilioNotifier\Models\Message class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Models
 */

namespace RcpTwilioNotifier\Models;
use RcpTwilioNotifier\Helpers\MemberRetriever;
use RcpTwilioNotifier\Helpers\MessagingQueue;
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
	 * @var SendAttempt[]
	 */
	private $send_attempts;

	/**
	 * Whether send attempts should be queued.
	 *
	 * @var bool
	 */
	private $is_queueing_enabled = false;

	/**
	 * Create a Message instance using an identifier.
	 *
	 * @param int|\WP_Post $message_identifier  The ID of the \WP_Post this represents, or the \WP_Post itself.
	 *
	 * @return Message|\WP_Error
	 */
	public static function find( $message_identifier ) {
		if ( is_numeric( $message_identifier ) ) {
			// It’s an ID, create a new \WP_Post object from it.
			$message_post = get_post( $message_identifier );
		} elseif ( $message_identifier instanceof \WP_Post ) {
			// It’s already a \WP_Post object.
			$message_post = $message_identifier;
		}

		if ( ! $message_post instanceof \WP_Post ) {
			return new \WP_Error(
				'rcptn_message_no_wp_post',
				__( 'Cannot find a WP Post using the identifier given.', 'rcptn' ),
				array(
					'message_identifier' => $message_identifier,
				)
			);
		}

		// Check for the right post type.
		if ( self::POST_TYPE !== get_post_type( $message_post ) ) {
			return new \WP_Error(
				'rcptn_message_wrong_post_type',
				__( 'The WP Post of the identifier given is not of the Message post type.', 'rcptn' ),
				array(
					'message_post' => $message_post,
				)
			);
		}

		return new self( $message_post );
	}

	/**
	 * Set internal values.
	 *
	 * @param \WP_Post $message_post  The \WP_Post this represents.
	 */
	private function __construct( $message_post ) {
		$this->wp_post = $message_post;

		$this->recipients = MemberRetriever::convert_user_ids_to_members( get_post_meta( $this->wp_post->ID, 'rcptn_recipient_ids', true ) );

		$this->message_body = new MessageBody( $this->wp_post->post_content, get_post_meta( $this->wp_post->ID, 'rcptn_body_data', true ) );

		$send_attempts = get_post_meta( $this->wp_post->ID, 'rcptn_send_attempts', true );

		$this->send_attempts = ( '' !== $send_attempts ) ? array_map(
			function( array $send_attempt ) {
				return SendAttempt::create_from_array( $send_attempt );
			}, $send_attempts
		) : array();
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

		// Create preliminary send attempts for each recipient.
		$send_attempts = array_map(
			function( Member $recipient ) {
				$send_attempt = new SendAttempt(
					$recipient,
					'pending',
					time()
				);

				return $send_attempt->convert_to_array();
			}, $args['recipients']
		);

		// Setting metadata...
		add_post_meta( $wp_post_id, 'rcptn_recipient_ids', $recipient_ids );
		add_post_meta( $wp_post_id, 'rcptn_body_data', $args['body_data'] );
		add_post_meta( $wp_post_id, 'rcptn_send_attempts', $send_attempts );

		// Return a new instance of the Message object now that all the settings are in place.
		return self::find( $wp_post_id );
	}

	/**
	 * Get the Message’s ID (aka the ID of the WP Post the Message represents).
	 *
	 * @return int
	 */
	public function get_id() {
		return $this->wp_post->ID;
	}

	/**
	 * Get the send attempts.
	 *
	 * @return SendAttempt[]
	 */
	public function get_send_attempts() {
		return $this->send_attempts;
	}

	/**
	 * Get the send attempts for a given recipient.
	 *
	 * @param Member $recipient  The recipient to check the send attempts for.
	 *
	 * @return bool|SendAttempt[]|\WP_Error
	 */
	public function get_send_attempts_for_recipient( $recipient ) {
		$send_attempt = array_values(
			array_filter(
				$this->send_attempts, function( SendAttempt $send_attempt ) use ( $recipient ) {
					return $send_attempt->recipient->ID === $recipient->ID;
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
	 * Enable message queueing.
	 */
	public function enable_queueing() {
		$this->is_queueing_enabled = true;
	}

	/**
	 * Send the message to all recipients.
	 */
	public function send_to_all() {
		$this->send_to_multiple( $this->recipients );
	}

	/**
	 * Send the message to multiple specified people.
	 *
	 * @param Member[] $recipients  The Members to message.
	 */
	public function send_to_multiple( $recipients ) {
		// Check if we should use the queued version of this function.
		if ( $this->is_queueing_enabled ) {
			$this->send_to_multiple_queued( $recipients );
			return;
		}

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
				$this->send_attempts, function( SendAttempt $send_attempt ) {
					return $send_attempt->is_failed();
				}
			)
		);

		// Bail if there are no failed sends.
		if ( empty( $failed_sends ) ) {
			return;
		}

		// Get the recipients of the failed sends as Member objects.
		$recipients = array_map(
			function( SendAttempt $send_attempt ) {
				return $send_attempt->recipient;
			}, $failed_sends
		);

		// Enable queueing.
		$this->enable_queueing();

		// Message the recipients of the failed sends.
		$this->send_to_multiple( $recipients );
	}

	/**
	 * Send the message to multiple specified people via the messaging queue.
	 *
	 * @param Member[] $recipients  The Members to message.
	 */
	private function send_to_multiple_queued( $recipients ) {
		$messaging_queue = MessagingQueue::get_instance();

		foreach ( $recipients as $recipient ) {
			$messaging_task = new MessagingTask( $this->get_id(), $recipient->ID );

			$messaging_queue->push_to_queue( $messaging_task->convert_to_array() );
		}

		$messaging_queue->save()->dispatch();
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
			$send_attempt = new SendAttempt(
				$recipient,
				'failed',
				time(),
				$sms_response->get_error_message()
			);
		} elseif ( $sms_response instanceof MessageInstance ) {
			$send_attempt = new SendAttempt(
				$recipient,
				'success',
				$sms_response->dateSent->getTimestamp()
			);
		}

		// If there's an existing send attempt for this recipient, overwrite the existing list to exclude that attempt.
		// We don't want to have two attempts for the same recipient.
		if ( false !== $this->get_send_attempts_for_recipient( $recipient ) ) {
			$this->send_attempts = array_filter(
				$this->send_attempts, function( SendAttempt $send_attempt ) use ( $recipient ) {
					return $send_attempt->recipient->ID !== $recipient->ID;
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
		// Convert the SendAttempts to arrays, to save it in the database.
		$send_attempts_formatted_for_database = array_map(
			function( SendAttempt $send_attempt ) {
				return $send_attempt->convert_to_array();
			}, $this->send_attempts
		);

		// Sort the array by recipient ID, from lowest to highest.
		usort( $send_attempts_formatted_for_database, function( $a, $b ) {
			return $a['recipient'] > $b['recipient'];
		} );

		// Save the sorted send attempts array.
		$update_attempt = update_post_meta( $this->wp_post->ID, 'rcptn_send_attempts', $send_attempts_formatted_for_database );

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
