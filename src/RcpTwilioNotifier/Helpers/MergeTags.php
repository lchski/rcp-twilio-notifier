<?php
/**
 * RCP: RcpTwilioNotifier\Helpers\MergeTags class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Helpers
 */

namespace RcpTwilioNotifier\Helpers;

use RcpTwilioNotifier\Models\Member;

/**
 * Methods to enable merge tags (string substitution).
 */
class MergeTags {

	/**
	 * The member for whom we’re substituting values.
	 *
	 * @var RcpTwilioNotifier\Models\Member
	 */
	private $member;

	/**
	 * Data necessary to process strings.
	 *
	 * @var array
	 */
	private $additional_data;

	/**
	 * Set internal values.
	 *
	 * @param RcpTwilioNotifier\Models\Member|int $member_identifier  The ID of the member for whom we’re substituting values, or the Member itself.
	 * @param array                               $additional_data  Additional data required to process a string.
	 */
	public function __construct( $member_identifier, $additional_data ) {
		if ( is_numeric( $member_identifier ) ) {
			// It’s an ID, create a new Member object from it.
			$member = new Member( $member_identifier );
		} elseif ( $member_identifier instanceof Member ) {
			// It’s already a member object.
			$member = $member_identifier;
		}

		$this->member = $member;
		$this->additional_data = $additional_data;
	}

	/**
	 * Processes a string, replacing merge tags with their values.
	 *
	 * @param string $string  The string to process.
	 *
	 * @return string  The processed string.
	 */
	public function replace_tags( $string ) {
		$merge_tags = $this->identify_merge_tags( $string );

		// If there are no merge tags present, we can return immediately.
		if ( 0 === count( $merge_tags ) ) {
			return $string;
		}

		// Process each merge tag individually.
		foreach ( $merge_tags as $merge_tag ) {
			$string = str_replace( $merge_tag, $this->replace_tag( $merge_tag ), $string );
		}

		return $string;
	}

	/**
	 * Find the database value for a given merge tag.
	 *
	 * @param string $tag  The tag to replace.
	 *
	 * @return string  The value of the tag, or the tag itself if we don’t have a method to replace it.
	 */
	private function replace_tag( $tag ) {
		switch ( $tag ) {
			case '|*FIRST_NAME*|':
				return $this->member->first_name;
			case '|*LAST_NAME*|':
				return $this->member->get( 'last_name' );
			case '|*ALERT_LINK*|':
				return get_permalink( $this->additional_data['post_ID'] );
			default:
				return $tag;
		}
	}

	/**
	 * Find all the merge tags in a string.
	 *
	 * @param string $string  The string with the merge tags.
	 *
	 * @return array  The array of merge tags.
	 */
	private function identify_merge_tags( $string ) {
		$matches = array();

		// Matches strings like “|*FIRST_NAME*|” and “|*STRING*|”.
		preg_match_all( '~(\|\*[A-Z_]*\*\|)~', $string, $matches );

		return array_unique( $matches[0] );
	}

	/**
	 * Get the descriptions for the given merge tags.
	 *
	 * @param array $enabled_merge_tags  The merge tags.
	 *
	 * @return array  The descriptions of the merge tags.
	 */
	public static function get_merge_tag_descriptions( $enabled_merge_tags ) {
		$descriptions = array(
			'|*FIRST_NAME*|' => __( '|*FIRST_NAME*| for the member’s first name.', 'rcptn' ),
			'|*LAST_NAME*|'  => __( '|*LAST_NAME*| for the member’s last name.', 'rcptn' ),
			'|*ALERT_LINK*|' => __( '|*ALERT_LINK*| to link to this alert.', 'rcptn' ),
		);

		$verifier = function( $merge_tag ) use ( $enabled_merge_tags ) {
			return in_array( $merge_tag, $enabled_merge_tags, true );
		};

		return array_filter( $descriptions, $verifier, ARRAY_FILTER_USE_KEY );
	}

}
