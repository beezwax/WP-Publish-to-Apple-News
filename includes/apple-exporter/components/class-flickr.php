<?php
/**
 * Publish to Apple News Includes: Apple_Exporter\Components\Flickr class
 *
 * Contains a class which is used to transform Flickr embeds into Apple News format.
 *
 * @package Apple_News
 * @subpackage Apple_Exporter
 * @since 0.2.0
 */

namespace Apple_Exporter\Components;

/**
 * A class to transform an Flickr embed into an Flickr Apple News component.
 *
 * @since 0.2.0
 */
class Flickr extends Component {

	/**
	 * Look for node matches for this component.
	 *
	 * @param \DOMElement $node The node to examine for matches.
	 * @access public
	 * @return \DOMElement|null The node on success, or null on no match.
	 */
	public static function node_matches( $node ) {

    // Match the src attribute against a flickr regex
    if ( 1 === $node->nodeType && preg_match( '#https?:\/\/.*?flickr.*?jpg#', $node->getAttribute( 'src' ) ) ) {
			return $node;
		}

		return null;
	}

	/**
	 * Register all specs for the component.
	 *
	 * @access public
	 */
	public function register_specs() {
		$this->register_spec(
			'json',
			__( 'JSON', 'apple-news' ),
			array(
				'role' => 'photo',
				'URL'  => '#url#',
				'caption' => '#caption#'
			)
		);
	}

	/**
	 * Build the component.
	 *
	 * @param string $html The HTML to parse into text for processing.
	 * @access protected
	 */
	protected function build( $html ) {

    if ( preg_match( '#https?:\/\/.*?flickr.*?jpg#', $html, $matches ) ) {
      $url = $matches[0];
    }

		// Ensure we got a URL.
		if ( empty( $url ) ) {
			return;
		}

		$this->register_json(
			'json',
			array(
				'#url#' => esc_url_raw( $url ),
			)
		);
	}
}
