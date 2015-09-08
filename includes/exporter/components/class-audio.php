<?php
namespace Exporter\Components;

/**
 * An HTML audio tag.
 *
 * @since 0.2.0
 */
class Audio extends Component {

	/**
	 * Look for node matches for this component.
	 *
	 * @param DomNode $node
	 * @return mixed
	 * @static
	 * @access public
	 */
	public static function node_matches( $node ) {
		// Is this an audio node?
		if ( 'audio' == $node->nodeName && self::remote_file_exists( $node ) ) {
			return $node;
		}

		return null;
	}

	/**
	 * Check if the remote file exists for this video.
	 *
	 * @param DomNode $node
	 * @return boolean
	 * @static
	 * @access private
	 */
	private static function remote_file_exists( $node ) {
		$html = $node->ownerDocument->saveXML( $node );
		preg_match( '/src="([^"]*?)"/im', $html, $matches );
		$path = $matches[1];

		// Is it a URL? Check the headers in case of 404
		if ( false !== filter_var( $path, FILTER_VALIDATE_URL ) ) {
			$file_headers = @get_headers( $path );
			return !preg_match( '#404 Not Found#', $file_headers[0] );
		}

		// It's not a valid URL
		return false;
	}

	/**
	 * Build the component.
	 *
	 * @param string $text
	 * @access protected
	 */
	protected function build( $text ) {
		// Remove initial and trailing tags: <video><p>...</p></video>
		if ( ! preg_match( '/src="([^"]+)"/', $text, $match ) ) {
			return null;
		}

		$url = $match[1];

		$this->json = array(
			'role' => 'audio',
			'URL'  => $url,
		);
	}

}

