<?php
namespace Push_API;

use \Push_API\Request\Request as Request;

/**
 * This class will post provided specified format articles to a channel using
 * the API.
 *
 * @since 0.2.0
 */
class API {

	/**
	 * The endpoint to connect to.
	 *
	 * @var string
	 * @since 0.2.0
	 */
	private $endpoint;

	/**
	 * The request object, used to send signed POST and GET requests to the
	 * endpoint.
	 *
	 * @var Request
	 * @since 0.2.0
	 */
	private $request;

	function __construct( $endpoint, $credentials, $debug = false ) {
		$this->endpoint = $endpoint;
		$this->request  = new Request( $credentials, $debug );
	}

	/**
	 * Sends a new article to a given channel.
	 *
	 * @param string $article The JSON string representing the article
	 * @param array  $bundles An array of file paths for the article attachments
	 *
	 * @since 0.2.0
	 */
	public function post_article_to_channel( $article, $channel_uuid, $bundles = array() ) {
		$url = $this->endpoint . '/channels/' . $channel_uuid . '/articles';
		return $this->send_post_request( $url, $article, $bundles );
	}

	/**
	 * Gets a channel information.
	 *
	 * @since 0.2.0
	 */
	public function get_channel( $channel_uuid ) {
		$url = $this->endpoint . '/channels/' . $channel_uuid;
		return $this->send_get_request( $url );
	}

	/**
	 * Gets article information.
	 *
	 * @since 0.2.0
	 */
	public function get_article( $article_id ) {
		$url = $this->endpoint . '/articles/' . $article_id;
		return $this->send_get_request( $url );
	}


	/**
	 * Deletes an article using a DELETE request.
	 *
	 * @since 0.4.0
	 */
	public function delete_article( $article_id ) {
		$url = $this->endpoint . '/articles/' . $article_id;
		return $this->send_delete_request( $url );
	}

	/**
	 * Gets all sections in the given channel.
	 *
	 * @since 0.2.0
	 */
	public function get_sections( $channel_uuid ) {
		$url = $this->endpoint . '/channels/' . $channel_uuid . '/sections';
		return $this->send_get_request( $url );
	}

	/**
	 * Gets information for a section.
	 *
	 * @since 0.2.0
	 */
	public function get_section( $section_id ) {
		$url = $this->endpoint . '/sections/' . $section_id;
		return $this->send_get_request( $url );
	}

	// Isolate request dependency.
	// -------------------------------------------------------------------------

	private function send_get_request( $url ) {
		return $this->request->get( $url );
	}

	private function send_delete_request( $url ) {
		return $this->request->delete( $url );
	}

	private function send_post_request( $url, $article, $bundles ) {
		return $this->request->post( $url, $article, $bundles );
	}

}
