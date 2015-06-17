<?php
namespace Exporter\Components;

/**
 * Represents an HTML header.
 *
 * @since 0.2.0
 */
class Heading extends Component {

	public static function node_matches( $node ) {
		if ( preg_match( '#h[1-6]#', $node->nodeName ) ) {
			return $node;
		}

		return null;
	}

	protected function build( $text ) {
		if ( 0 === preg_match( '/<h(\d)>(.*?)<\/h\1>/im', $text, $matches ) ) {
			return;
		}

		$level = intval( $matches[1] );
		// We won't be using markdown*, so we ignore all HTML tags, just fetch the
		// contents.
		// *: No markdown because the apple format doesn't support markdown with
		// textStyle in headings.
		$text  = preg_replace( '#</?\.+?>#', '', $matches[2] );

		// NOTE: this is not using markdown format
		$this->json = array(
			'role' => 'heading' . $level,
			'text' => $text,
		);

		$this->set_style( $level );
	}

	private function set_style( $level ) {
		$this->json[ 'textStyle' ] = 'default-heading-' . $level;
		$this->register_style( 'default-heading-' . $level, array(
			'fontName' => $this->get_setting( 'header_font' ),
			'fontSize' => $this->get_setting( 'header' . $level . '_size' ),
			'relativeLineHeight' => 1.35,
			'textColor' => '#000',
		) );
	}

}

