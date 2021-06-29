<?php
/**
 * Apple News Tests Mocks: coauthors function
 *
 * @package Apple_News
 * @subpackage Tests
 */

/**
 * A mock for the coauthors function from Co-Authors Plus.
 *
 * @param string $between     Delimiter that should appear between the co-authors
 * @param string $betweenLast Delimiter that should appear between the last two co-authors
 * @param string $before      What should appear before the presentation of co-authors
 * @param string $after       What should appear after the presentation of co-authors
 * @param bool   $echo        Whether the co-authors should be echoed or returned. Defaults to true.
 */
function coauthors( $between = ', ', $betweenLast = ' and ', $before = '', $after = '', $echo = true ) {
	// To use this function, put display names in a global array called $apple_news_coauthors.
	global $apple_news_coauthors;

	// Bail if we don't have coauthors.
	if ( empty( $apple_news_coauthors ) ) {
		return '';
	}

	// Get last index.
	$last_index = count( $apple_news_coauthors ) - 1;

	// Compute output.
	$output = $before
		. implode( $between, array_slice( $apple_news_coauthors, 0, $last_index ) )
		. ( 0 !== $last_index ? $betweenLast : '' )
		. $apple_news_coauthors[ $last_index ]
		. $after;

	// Fork for echo.
	if ( ! $echo ) {
		return $output;
	}

	echo $output;
}
