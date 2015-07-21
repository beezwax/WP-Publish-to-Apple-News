<?php
namespace Exporter\Components;

/**
 * Represents an HTML header.
 *
 * @since 0.2.0
 */
class Heading extends Component {

	/**
	 * Quotes can be anchor targets.
	 */
	protected $can_be_anchor_target = true;

	public static function node_matches( $node ) {
		if ( ! preg_match( '#h[1-6]#', $node->nodeName ) ) {
			return null;
		}

		$html = $node->ownerDocument->saveXML( $node );
		if ( preg_match( '#<img.*?>#si', $html ) ) {
			return self::split_image( $html );
		}

		return $node;
	}

	private static function split_image( $html ) {
		if ( empty( $html ) ) {
			return array();
		}

		// Find the first image inside
		preg_match( '#<img.*?>#si', $html, $matches );

		if ( ! $matches ) {
			return array( array( 'name' => 'heading', 'value' => $html ) );
		}

		$image_html   = $matches[0];
		$heading_html = str_replace( $image_html, '', $html );

		return array(
			array( 'name'  => 'heading', 'value' => self::clean_html( $heading_html ) ),
			array( 'name'  => 'img'    , 'value' => $image_html ),
		);
	}

	protected function build( $text ) {
		if ( 0 === preg_match( '#<h(\d).*?>(.*?)</h\1>#si', $text, $matches ) ) {
			return;
		}

		$level = intval( $matches[1] );
		// We won't be using markdown*, so we ignore all HTML tags, just fetch the
		// contents.
		// *: No markdown because the apple format doesn't support markdown with
		// textStyle in headings.
		$text  = preg_replace( '#</?\.+?>#', '', $matches[2] );

		$this->json = array(
			'role'   => 'heading' . $level,
			'text'   => trim( $this->markdown->parse( $text ) ),
			'format' => 'markdown',
		);

		$this->set_style( $level );
		$this->set_layout();
	}

	private function set_layout() {
		$this->json['layout'] = 'heading-layout';
		$this->register_layout( 'heading-layout', array(
			'margin' => array(
				'top' => 20,
			),
		) );
	}

	private function set_style( $level ) {
		$this->json[ 'textStyle' ] = 'default-heading-' . $level;
		$this->register_style( 'default-heading-' . $level, array(
			'fontName' => $this->get_setting( 'header_font' ),
			'fontSize' => $this->get_setting( 'header' . $level . '_size' ),
			'relativeLineHeight' => 1.5,
			'textColor' => $this->get_setting( 'header_color' ),
		) );
	}

}

