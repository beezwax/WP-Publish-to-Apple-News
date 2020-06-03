<?php
/**
 * Publish to Apple News: \Apple_Exporter\Components\Button class
 *
 * @package Apple_News
 * @subpackage Apple_Exporter\Components
 */

namespace Apple_Exporter\Components;

/**
 * A button normally describes who wrote the article, the date, etc.
 *
 * @since 0.2.0
 */
class Button extends Component {

	/**
	 * Look for node matches for this component.
	 *
	 * @param \DOMElement $node The node to examine for matches.
	 * @access public
	 * @return \DOMElement|null The node on success, or null on no match.
	 */
	public static function node_matches( $node ) {
		// If we aren't on an element node, bail out.
		if ( 1 !== $node->nodeType ) {
			return null;
		}

		// Check for Gutenberg-style embeds, which have a helpful signature.
		if ( 'div' === $node->nodeName
			&& false !== strpos( $node->getAttribute( 'class' ), 'wp-block-buttons' )
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
				'role'      => 'link_button',
				'text'      => '#text#',
				'URL'       => '#url#',
			)
		);

		$this->register_spec(
			'default-button',
			__( 'Style', 'apple-news' ),
			array(
				'backgroundColor' => '#background_color#',
				'textColor'       => '#text_color#',
			)
		);

		$this->register_spec(
			'button-layout',
			__( 'Layout', 'apple-news' ),
			array(
				'margin' => array(
					'top'    => 30,
					'bottom' => 0,
				),
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
		$url   = '';
		if ( empty( $check ) ) {
			return;
		}

		// Get location for button.
		if ( preg_match( '/<a[^>]+?rel=[\'"]([^\'"]+)/', $html, $rel_matches ) ) {
			$url = $rel_matches[1];
		}

		// If the URL is protocol-less, default it to https.
		if ( 0 === strpos( $url, '//' ) ) {
			$url = 'https:' . $url;
		}

		// If no URL was found, bail out.
		if ( empty( $url ) ) {
			return;
		}

		if ( preg_match( '/<\s*?a\b[^>]*>(.*?)<\/a\b[^>]*>/', $html, $matches ) ) {
			$html = $matches[1];
		}

		$this->register_json(
			'json',
			array(
				'#text#' => $html,
				'#url#'  => $url,
			)
		);

		$this->set_style();
		$this->set_layout();
	}

/**
	 * Set the style for the component.
	 *
	 * @access private
	 */
	private function set_style() {

		// Get information about the currently loaded theme.
		$theme = \Apple_Exporter\Theme::get_used();

		$this->register_style(
			'default-button',
			'default-button',
			array(
				'#background_color#'     => 'white',
				'#text_color#'           => 'black',
			),
			'textStyle'
		);
	}

	/**
	 * Set the layout for the component.
	 *
	 * @access private
	 */
	private function set_layout() {
		$this->register_full_width_layout(
			'button-layout',
			'button-layout',
			array(),
			'layout'
		);
	}
}
