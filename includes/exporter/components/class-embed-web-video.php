<?php
namespace Exporter\Components;

/**
 * An embedded video from Youtube or Vimeo, for example. For now, assume
 * any iframe is an embedded video.
 *
 * @since 0.0.0
 */
class Embed_Web_Video extends Component {

	public static function node_matches( $node ) {
		// Is this node an iframe?
		if ( 'iframe' == $node->nodeName ) {
			return $node;
		}

		// Does this node contain an iframe?
		if ( $ewv = self::node_find_by_tagname( $node, 'iframe' ) ) {
			return $ewv;
		}

		return null;
	}

	protected function build( $text ) {
		$attributes = array();
		preg_match_all( '/(\w+)="([^"]*?)"/im', $text, $matches, PREG_SET_ORDER );
		foreach ( $matches as $match ) {
			$attributes[ $match[1] ] = $match[2];
		}

		$aspect_ratio = substr( ( $attributes['width'] / $attributes['height'] ), 0, 5 );

		$this->json = array(
			'role' => 'embedwebvideo',
			'aspectRatio' => $aspect_ratio,
			'URL' => $attributes['src'],
			'caption' => 'test',
			'accessibilityCaption' => 'test',
		);
	}

}

