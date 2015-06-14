<?php
namespace Exporter\Components;

/**
 * Represents an HTML header.
 *
 * @since 0.0.0
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

		$this->json = array(
			'role' => 'heading' . $matches[1],
			'text' => $this->markdown->parse( $text ),
			'format' => 'markdown',
		);
	}

}

