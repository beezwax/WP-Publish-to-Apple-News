<?php
namespace Exporter\Components;

/**
 * An HTML audio tag.
 *
 * @since 0.2.0
 */
class Audio extends Component {

	public static function node_matches( $node ) {
		// Is this an audio node?
		if ( 'audio' == $node->nodeName ) {
			return $node;
		}

		// Is there a node with tag 'audio' inside this one?
		//
		// FIXME: Because audio can't just be parsed by markdown we have to
		// forcefully extract them from the container, so if the container has
		// something besides this component, it will be ignored. See comment in
		// Components/Body.
		if ( $audio_node = self::node_find_by_tagname( $node, 'audio' ) ) {
			return $audio_node;
		}

		return null;
	}

	protected function build( $text ) {
		// Remove initial and trailing tags: <video><p>...</p></video>
		if ( ! preg_match( '/src="([^"]+)"/', $text, $match ) ) {
			return null;
		}

		$url = $match[1];
		$filename = basename( $url );

		if ( false !== strpos( $filename, '?' ) ) {
			$parts    = explode( '?', $filename );
			$filename = $parts[0];
		}

		// Save video into bundle
		$this->bundle_source( $filename, $url );

		$this->json = array(
			'role' => 'audio',
			'URL'  => 'bundle://' . $filename,
		);
	}

}

