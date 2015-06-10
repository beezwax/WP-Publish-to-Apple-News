<?php
namespace Exporter\Components;

/**
 * Represents a simple image.
 *
 * @since 0.0.0
 */
class Image extends Component {

	public static function node_matches( $node ) {
		// Is this an image node?
		if( 'img' == $node->nodeName ) {
			return $node;
		}

		// Is there a node with tag 'img' inside this one?
		if ( $image_node = self::node_find_by_tagname( $node, 'img' ) ) {
			return $image_node;
		}

		return null;
	}

	protected function build( $text ) {
		$matches = array();
		preg_match( '/src="([^"]*?)"/im', $text, $matches );
		$url = $matches[1];
		$filename = basename( $url );

		// Save image into bundle
		$this->bundle_source( $filename, $url );

		$this->json = array(
			'role' => 'photo',
			'URL'  => 'bundle://' . $filename,
		);
	}

}

