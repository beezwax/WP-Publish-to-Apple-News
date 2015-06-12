<?php
namespace Exporter\Components;

/**
 * One of the simplest components. It's just a paragraph.
 *
 * @since 0.0.0
 */
class Body extends Component {

	public static function node_matches( $node ) {
		if ( in_array( $node->nodeName, array( 'p', 'ul', 'ol' ) ) ) {
			return $node;
		}

		return null;
	}

	protected function build( $text ) {
		// Remove initial and trailing tags
		// $text = substr( $text, 3, -4 );

		$this->json = array(
			'role' => 'body',
			'text' => $this->markdown->parse( $text ),
			'format' => 'markdown',
		);
	}

}

