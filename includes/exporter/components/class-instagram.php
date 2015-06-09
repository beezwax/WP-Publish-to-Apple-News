<?php
namespace Exporter\Components;

/**
 * A tweet embed code consists of a blockquote followed by a script tag. Parse
 * the blockquote only and ignore the script tag, as all we need is the URL.
 */
class Instagram extends Component {

	protected function build( $text ) {
		// Find tweeter URL in HTML string
		if( ! preg_match( '/https:\/\/instagram.com\/p\/([^\/]+)\//', $text, $matches ) ) {
			return null;
		}

		$url = $matches[0];
		$this->json = array(
			'role' => 'instagram',
			'URL'  => $url,
		);
	}

}

