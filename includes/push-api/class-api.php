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

	/**
	 * Credentials used for requests authentication.
	 *
	 * @var Credentials
	 * @since 0.0.0
	 */
	private $credentials;

	/**
	 * The endpoint to connect to.
	 *
	 * @var string
	 * @since 0.0.0
	 */
	private $endpoint;

	/**
	 * Whether or not to use a reverse proxy like Charles to send requests though.
	 *
	 * @var boolean
	 * @since 0.0.0
	 */
	private $debug;

	function __construct( $endpoint, $credentials, $debug = false ) {
		$this->endpoint    = $endpoint;
		$this->credentials = $credentials;
		$this->debug       = $debug;
	}

	/**
	 * Sends a new article to a given channel.
	 *
	 * @since 0.0.0
	 */
	public function post_article_to_channel( $article, $channel_uuid, $bundles = array() ) {
		$url = $this->endpoint . '/channels/' . $channel_uuid . '/articles';
		return $this->send_post_request( $url, $article, $bundles );
	}

	/**
	 * Gets a channel information.
	 *
	 * @since 0.0.0
	 */
	public function get_channel( $channel_uuid ) {
		$url = $this->endpoint . '/channels/' . $channel_uuid;
		return $this->send_get_request( $url );
	}

	/**
	 * Gets article information.
	 *
	 * @since 0.0.0
	 */
	public function get_article( $article_id ) {
		$url = $this->endpoint . '/articles/' . $article_id;
		return $this->send_get_request( $url );
	}

	/**
	 * Gets all sections in the given channel.
	 *
	 * @since 0.0.0
	 */
	public function get_sections( $channel_uuid ) {
		$url = $this->endpoint . '/channels/' . $channel_uuid . '/sections';
		return $this->send_get_request( $url );
	}

	/**
	 * Gets information for a section.
	 *
	 * @since 0.0.0
	 */
	public function get_section( $section_id ) {
		$url = $this->endpoint . '/sections/' . $section_id;
		return $this->send_get_request( $url );
	}

	private function send_get_request( $url ) {
		$request = new Request( $url, 'GET', $this->debug );
		$request->authenticate( $this->credentials );
		return $request->send();
	}

	private function send_post_request( $url, $article, $bundles ) {
		$request = new Request( $url, 'POST', $this->debug );
		$request->set_article( $article, $bundles );
		$request->authenticate( $this->credentials );
		return $request->send();
	}

}
