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
	 * Look for node matches for this component.
	 *
	 * @param \DOMElement $node The node to examine for matches.
	 * @access public
	 * @return array|null The node on success, or null on no match.
	 */
	public static function node_matches( $node ) {
		// We are only interested in p, pre, ul and ol.
		if ( 'a' !== $node->nodeName ) {
			return null;
		}

		// If the node is a AND it's empty, just ignore.
		if ( empty( $node->nodeValue ) ) {
			return null;
		}

		return $node;
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
        // 'style'  => 'default-link-button',
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

		if ( preg_match( '/[^<]*(<a href="([^"]+)">([^<]+)<\/a>)/', $html, $link_button_match ) ) {
			$this->register_json(
				'json',
				array(
					'#url#' => $link_button_match[2],
					'#text#' => $link_button_match[3],
				)
			);
		}

    $this->set_default_style();
	}

	/**
	 * Get the default style spec for the component.
	 *
	 * @return array
	 * @access private
	 */
	private function get_default_style_spec() {
		return array(
			'textAlignment'          => 'left',
			'fontName'               => '#body_font#',
			'fontSize'               => '#body_size#',
			'tracking'               => '#body_tracking#',
			'lineHeight'             => '#body_line_height#',
			'textColor'              => '#body_color#',
			'linkStyle'              => array(
				'textColor' => '#body_link_color#',
			),
			'paragraphSpacingBefore' => 18,
			'paragraphSpacingAfter'  => 18,
		);
	}

	/**
	 * Get the default style values for the component.
	 *
	 * @return array
	 * @access private
	 */
	private function get_default_style_values() {

		// Get information about the currently loaded theme.
		$theme = \Apple_Exporter\Theme::get_used();

		return array(
			'#body_font#'        => $theme->get_value( 'body_font' ),
			'#body_size#'        => intval( $theme->get_value( 'body_size' ) ),
			'#body_tracking#'    => intval( $theme->get_value( 'body_tracking' ) ) / 100,
			'#body_line_height#' => intval( $theme->get_value( 'body_line_height' ) ),
			'#body_color#'       => $theme->get_value( 'body_color' ),
			'#body_link_color#'  => $theme->get_value( 'body_link_color' ),
		);
	}

	/**
	 * Set the default style for the component.
	 *
	 * @access public
	 */
	public function set_default_style() {
		$this->register_style(
			'default-body',
			'default-body',
			$this->get_default_style_values(),
			'textStyle'
		);
	}
}

