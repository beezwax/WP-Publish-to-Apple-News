<?php
namespace Exporter\Components;

/**
 * An HTML video tag representation.
 *
 * @since 0.2.0
 */
class Video extends Component {

	public static function node_matches( $node ) {
		// Is this an video node?
		if ( 'video' == $node->nodeName && self::remote_file_exists( $node ) ) {
			return $node;
		}

		return null;
	}

	private static function remote_file_exists( $node ) {
		$html = $node->ownerDocument->saveXML( $node );
		preg_match( '/src="([^"]*?)"/im', $html, $matches );
		$path = $matches[1];

		// Is it an URL? Check the headers in case of 404
		if ( 0 === strpos( $path, 'http' ) ) {
			$file_headers = @get_headers( $path );
			return !preg_match( '#404 Not Found#', $file_headers[0] );
		}

		// It's not a valid URL
		return false;
	}

	protected function build( $text ) {
		// Remove initial and trailing tags: <video><p>...</p></video>
		if ( ! preg_match( '/src="([^"]+)"/', $text, $match ) ) {
			return null;
		}

		$url = $match[1];

		$this->json = array(
			'role' => 'video',
			'URL'  => $url,
		);
	}

}

