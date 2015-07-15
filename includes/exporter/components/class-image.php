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

		// IMAGE ALIGNMENT is defined as follows:
		// 1. If there is a left or right alignment specified, respect it.
		// 2. If there is a center alignment specified:
		//	2.a If the image is big enough, use a full-width image
		//	2.b Otherwise auto-align
		// 3. Otherwise, if there is no alignment specified or set to "none", auto-align.
		if ( preg_match( '#align="left"#', $text ) || preg_match( '#class=".*?(?:alignleft).*?"#', $text ) ) {
			$this->set_anchor_position( Component::ANCHOR_LEFT );
		} else if ( preg_match( '#align="right"#', $text ) || preg_match( '#class=".*?(?:alignright).*?"#', $text ) ) {
			$this->set_anchor_position( Component::ANCHOR_RIGHT );
		} else if ( preg_match( '#align="center"#', $text ) || preg_match( '#class=".*?(?:aligncenter).*?"#', $text ) ) {
			list( $width, $height ) = getimagesize( $url );
			if ( $width < Exporter::LAYOUT_WIDTH ) {
				$this->set_anchor_position( Component::ANCHOR_AUTO );
			} else {
				$this->set_anchor_position( Component::ANCHOR_NONE );
			}
		} else {
			$this->set_anchor_position( Component::ANCHOR_AUTO );
		}

		// Check for caption
		if ( preg_match( '#<figcaption.*?>(.*?)</figcaption>#m', $text, $matches ) ) {
			$caption = trim( $matches[1] );
			$this->json['caption'] = $caption;
		}
	}

}

