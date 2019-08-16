<?php
/**
 * Publish to Apple News Includes: Apple_Exporter\Components\Embed_Generic class
 *
 * Contains a class which is used to transform generic embeds into Apple News format.
 *
 * @package Apple_News
 * @subpackage Apple_Exporter
 * @since 2.0.0
 */

namespace Apple_Exporter\Components;

/**
 * A class to transform a generic embed into an Apple News component.
 *
 * @since 2.0.0
 */
class Embed_Generic extends Component {

	/**
	 * Look for node matches for this component.
	 *
	 * @param \DOMElement $node The node to examine for matches.
	 *
	 * @access public
	 * @return \DOMElement|null The node on success, or null on no match.
	 */
	public static function node_matches( $node ) {

		// TODO: Wire this up.
		/*
		if ( 1 === $node->nodeType && false !== strpos( $node->getAttribute('class'), 'is-provider-flickr' ) ) {
			return $node;
		}

		if (
			'figure' === $node->nodeName && self::node_has_class( $node, 'is-provider-spotify' )
			|| $node->hasChildNodes() && 'iframe' === $node->childNodes[0]->nodeName && self::validateUrl( $node->childNodes[0]->getAttribute( 'src' ) )
		) {
			return $node;
		}
		*/

		return null;
	}

	/**
	 * Register all specs for the component.
	 *
	 * @access public
	 */
	public function register_specs() {
		$this->register_spec(
			'embed-generic-json',
			__( 'Embed (generic) JSON', 'apple-news' ),
			[
				'layout'     => 'embed-generic-layout',
				'role'       => 'container',
				'components' => '#components#',
			]
		);

		$this->register_spec(
			'embed-generic-layout',
			__( 'Embed (generic) Layout', 'apple-news' ),
			[
				'margin'      => [
					'top'    => 15,
					'bottom' => 15,
				],
			]
		);
	}

	/**
	 * Build the component.
	 *
	 * @param string $html The HTML to parse into text for processing.
	 *
	 * @access protected
	 */
	protected function build( $html ) {
		$this->register_json(
			'embed-generic-json',
			[
				'#components#' => [
					[
						'role'   => 'heading2',
						'text'   => 'TITLE GOES HERE',
						'format' => 'html',
					],
					[
						'role'      => 'body',
						'text'      => '<a href="' . esc_url( 'https://example.com' ) . '">' . esc_html__( 'View on PROVIDER.', 'apple-news' ) . '</a>',
						'format'    => 'html',
						'textStyle' => [
							'fontSize' => 14,
						],
					],
				],
			]
		);
		$this->register_layout( 'embed-generic-layout', 'embed-generic-layout' );
	}
}
