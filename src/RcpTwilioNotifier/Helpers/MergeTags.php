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
	 * Set internal values.
	 *
	 * @param RcpTwilioNotifier\Models\Member|int $member_identifier  The ID of the member for whom we’re substituting values, or the Member itself.
	 */
	public function __construct( $member_identifier ) {
		if ( is_numeric( $member_identifier ) ) {
			// It’s an ID, create a new Member object from it.
			$member = new Member( $member_identifier );
		} elseif ( $member_identifier instanceof Member ) {
			// It’s already a member object.
			$member = $member_identifier;
		}

		$this->member = $member;
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

}
