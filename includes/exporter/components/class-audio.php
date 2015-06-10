<?php
namespace Exporter\Components;

/**
 * An HTML audio tag.
 */
class Audio extends Component {

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
		$content = $this->get_file_contents( $url );
		$this->write_to_workspace( $filename, $content );

		$this->json = array(
			'role' => 'audio',
			'URL'  => 'bundle://' . $filename,
		);
	}

}

