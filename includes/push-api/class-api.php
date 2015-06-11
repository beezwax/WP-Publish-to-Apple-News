<?php
namespace Push_API;

use \Exception as Exception;

require_once __DIR__ . '/class-request.php';

/**
 * This class will post provided specified format articles to a channel using
 * the API.
 *
 * @since 0.0.0
 */
class API {

	private $credentials;

	/**
	 * The endpoint to connect to.
	 *
	 * @since 0.0.0
	 */
	private $endpoint;

	/**
	 * Whether or not to use a reverse proxy like Charles to send requests though.
	 *
	 * @since 0.0.0
	 */
	private $debug;

	function __construct( $endpoint, $credentials, $debug = false ) {
		$this->endpoint    = $endpoint;
		$this->credentials = $credentials;
		$this->debug       = $debug;
	}

	public function post_article_to_channel( $article, $channel_uuid, $bundles = array() ) {
		$url     = $this->endpoint . '/channels/' . $channel_uuid . '/articles';
		$request = new Request( $url, 'POST', $this->debug );
		$request->set_article( $article, $bundles );
		$request->authenticate( $this->credentials );
		return $request->send();
	}

}
