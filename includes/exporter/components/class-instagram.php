<?php
namespace Exporter\Components;

/**
 * Instagram embed code consists of a blockquote followed by a script tag.
 * Parse the blockquote only and ignore the script tag, as all we need is the
 * URL.
 *
 * @since 0.2.0
 */
class Instagram extends Component {

	public static function node_matches( $node ) {
		if ( self::node_has_class( $node, 'instagram-media' ) ) {
			return $node;
		}

		return null;
	}

	protected function build( $text ) {
		// Find instagram URL in HTML string
		if ( ! preg_match( '#https?://instagr(\.am|am\.com)/p/([^/]+)/#', $text, $matches ) ) {
			return null;
		}

		$url = $matches[0];
		$this->json = array(
			'role' => 'instagram',
			'URL'  => $url,
		);
	}

}

