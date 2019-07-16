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
class Iframe_Generic extends Component {

	/**
	 * Look for node matches for this component.
	 *
	 * @param \DOMElement $node The node to examine for matches.
	 * @access public
	 * @return \DOMElement|null The node on success, or null on no match.
	 */
	public static function node_matches( $node ) {
		return (
			( 'iframe' === $node->nodeName )
		);
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
				'role'      => 'body',
				'text'      => '#text#',
				'format'    => 'html',
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
		$this->register_json(
			'json',
			array(
				'#text#' => '<p>SUP</p>',
			)
		);
	}
}
