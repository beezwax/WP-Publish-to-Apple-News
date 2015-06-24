<?php
namespace Exporter\Components;

/**
 * Represents a simple image.
 *
 * @since 0.2.0
 */
class Image extends Component {

	public static function node_matches( $node ) {
		// Is this an image node?
		if ( 'img' == $node->nodeName ) {
			return $node;
		}

		// Is there a node with tag 'img' inside this one? If so return all image
		// nodes.
		//
		// FIXME: Because images can't just be parsed by markdown we have to
		// forcefully extract them from the container, so if a paragraph has
		// something besides the images it will be ignored.
		if ( $images = self::node_find_all_by_tagname( $node, 'img' ) ) {
			return $images;
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

