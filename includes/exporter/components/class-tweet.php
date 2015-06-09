<?php
namespace Exporter\Components;

/**
 * A tweet embed code consists of a blockquote followed by a script tag. Parse
 * the blockquote only and ignore the script tag, as all we need is the URL.
 */
class Tweet extends Component {

	protected function build( $text ) {
		// Find tweeter URL in HTML string
		if( ! preg_match( '/https:\/\/twitter.com\/([^\/]*)\/status\/(\d+)/', $text, $matches ) ) {
			return null;
		}

		$url = $matches[0];
		$this->json = array(
			'role' => 'tweet',
			'URL'  => $url,
		);
	}

}

