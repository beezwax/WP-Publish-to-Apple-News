<?php
/**
 * Publish to Apple News: \Apple_Exporter\Components\Link class
 *
 * @package Apple_News
 * @subpackage Apple_Exporter\Components
 */

namespace Apple_Exporter\Components;

/**
 * A Link/Anchor component.
 *
 * @since 2.0.0
 */
class Link_Button extends Component {

	/**
	 * Determines whether a node is an anchor tag with an HREF
	 * and a class of wp-block-button__link, which is the signature
	 * of a link button element.
	 *
	 * @param \DOMElement $node The node to examine for matches.
	 *
	 * @return bool True if the node is a link button, false if not.
	 */
	public static function is_link_button( $node ) {
		return 'a' === $node->nodeName
			&& self::node_has_class( $node, 'wp-block-button__link' )
			&& ! empty( $node->getAttribute( 'href' ) );
	}

	/**
	 * Look for node matches for this component.
	 *
	 * @param \DOMElement $node The node to examine for matches.
	 * @access public
	 * @return \DOMElement|null The node on success, or null on no match.
	 */
	public static function node_matches( $node ) {
		// Anchors with an href and the button class will match.
		if ( self::is_link_button( $node ) ) {
			return $node;
		}

		// DIVs for a single button will match.
		if ( 'div' === $node->nodeName
			&& self::node_has_class( $node, 'wp-block-button' )
			&& $node->hasChildNodes()
			&& self::is_link_button( $node->childNodes[0] )
		) {
			return $node;
		}

		// DIVs for button groups will match.
		if ( 'div' === $node->nodeName
			&& self::node_has_class( $node, 'wp-block-buttons' )
			&& $node->hasChildNodes()
			&& 'div' === $node->childNodes[0]->nodeName
			&& self::node_has_class( $node->childNodes[0], 'wp-block-button' )
			&& $node->childNodes[0]->hasChildNodes()
			&& self::is_link_button( $node->childNodes[0]->childNodes[0] )
		) {
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
				'role'   => 'link_button',
				'text'   => '#text#',
        'URL' => '#url#',
        'style' => 'default-link-button',
				'layout' => 'link-button-layout',
				'textStyle' => 'default-link-button-text-style',
			)
		);

		// Register the JSON for the link button layout.
		$this->register_spec(
			'link-button-layout',
			__( 'Button Layout', 'apple-news' ),
			array(
				'margin' => array(
					'bottom' => 20,
				),
				'padding' => array(
					'top' => 10,
					'bottom' => 10,
					'left' => 15,
					'right' => 15,
				),
			)
		);

		// Register the JSON for the link button style.
		$this->register_spec(
			'default-link-button',
			__( 'Link Button Style', 'apple-news' ),
			array(
				'backgroundColor' => '#DDD',
				'mask' => array(
					'type' => 'corners',
					'radius' => 25,
				),
			)
		);

		// Register the JSON for the link button text style.
		$this->register_spec(
			'default-link-button-text-style',
			__( 'Link Button Text Style', 'apple-news' ),
			array(
				'textColor' => '#000',
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

		// If there is no text for this element, bail.
		$check = trim( $html );
		if ( empty( $check ) ) {
			return;
		}

		if ( preg_match( '/^(<a.*?href="([^"]+)".*?>([^<]+)|<<\/a>)/', $html, $link_button_match ) ) {
			$this->register_json(
				'json',
				array(
					'#url#' => $link_button_match[2],
					'#text#' => $link_button_match[3],
				)
			);
		}

    // Register the layout for the link button.
		$this->register_layout( 'link-button-layout', 'link-button-layout' );

		// Register the style for the link button.
		$this->register_component_style(
			'default-link-button',
			'default-link-button'
		);
		// Register the style for the link button text.
		$this->register_style(
			'default-link-button-text-style',
			'default-link-button-text-style'
		);
	}
}

