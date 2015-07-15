<?php
namespace Exporter\Components;

use \Exporter\Exporter as Exporter;

/**
 * Represents a simple image.
 *
 * @since 0.2.0
 */
class Image extends Component {

	public static function node_matches( $node ) {
		// Is this an image node?
		if ( 'img' == $node->nodeName || 'figure' == $node->nodeName ) {
			return $node;
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

		// If there's an align attribute, or a class which starts with 'align',
		// set as anchorable.
		if ( preg_match( '#align=#', $text ) || preg_match( '#class=".*?(?:alignleft|alignright).*?"#', $text ) ) {
			$this->set_anchorable( true );
		}

		// Check for caption
		if ( preg_match( '#<figcaption.*?>(.*?)</figcaption>#m', $text, $matches ) ) {
			$caption = trim( $matches[1] );
			$this->json['caption'] = $caption;
		}
	}

}

