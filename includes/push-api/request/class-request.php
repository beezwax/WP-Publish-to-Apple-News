<?php
namespace Push_API\Request;

use \Push_API\MIME_Builder as MIME_Builder;

require_once __DIR__ . '/../class-mime-builder.php';
require_once __DIR__ . '/class-curl.php';

/**
 * An object capable of sending signed HTTP requests to the Push API.
 *
 * @since 0.2.0
 */
class Request {

	/**
	 * Helper class used to build the MIME parts of the request.
	 *
	 * @var MIME_Builder
	 * @since 0.2.0
	 */
	private $mime_builder;

	/**
	 * Whether or not we are debugging using a reverse proxy, like Charles.
	 *
	 * @var boolean
	 * @since 0.2.0
	 */
	private $debug;

	/**
	 * The credentials that will be used to sign sent requests.
	 *
	 * @var Credentials
	 * @since 0.2.0
	 */
	private $credentials;

	function __construct( $credentials, $debug = false, $mime_builder = null ) {
		$this->credentials  = $credentials;
		$this->debug        = $debug;
		$this->mime_builder = $mime_builder ?: new MIME_Builder();
	}

	/**
	 * Sends a POST request with the given article and bundles.
	 *
	 * @since 0.2.0
	 */
	public function post( $url, $article, $bundles = array() ) {
		$content   = $this->build_content( $article, $bundles );
		$signature = $this->sign( $url, $content );
		$response  = $this->curl_post( $url, $content, $this->mime_builder->boundary(), $signature );

		return $this->parse_response( $response );
	}

	/**
	 * Sends a GET request with the given article and bundles.
	 *
	 * @since 0.2.0
	 */
	public function get( $url ) {
		$signature = $this->sign( $url );
		$response  = $this->curl_get( $url, $signature );

		return $this->parse_response( $response );
	}

	private function parse_response( $response ) {
		if( ! $response ) {
			throw new Request_Exception( "Invalid response:" . $response );
		}

		if ( property_exists( $response, 'errors' ) ) {
			$string_errors = '';
			foreach ( $response->errors as $error ) {
				$string_errors .= $error->code . "\n";
			}
			throw new Request_Exception( "There has been an error with your request:\n$string_errors" );
		}

		return $response;
	}

	// TODO The exporter has an abstracted article class. Should we have
	// something similar here? That way this method could live there.
	private function build_content( $article, $bundles = array() ) {
		$content = $this->mime_builder->add_json_string( 'my_article', 'article.json', $article );
		foreach ( $bundles as $bundle ) {
			$content .= $this->mime_builder->add_content_from_file( $bundle );
		}
		$content .= $this->mime_builder->close();

		return $content;
	}

	private function sign( $url, $content = null ) {
		$current_date = date( 'c' );
		$verb         = is_null( $content ) ? 'GET' : 'POST';

		$request_info = $verb . $url . $current_date;
		if ( 'POST' == $verb ) {
			$content_type = 'multipart/form-data; boundary=' . $this->mime_builder->boundary();
			$request_info .= $content_type . $content;
		}

		$secret_key = base64_decode( $this->credentials->secret() );
		$hash       = hash_hmac( 'sha256', $request_info, $secret_key, true );
		$signature  = base64_encode( $hash );

		return 'Authorization: HHMAC; key=' . $this->credentials->key() . '; signature=' . $signature . '; date=' . $current_date;
	}

	// Isolate CURL dependency.
	// -------------------------------------------------------------------------

	private function curl_post( $url, $content, $boundary, $signature ) {
		$curl = new CURL( $url, $this->debug );
		return $curl->post( $content, $boundary, $signature );
	}

	private function curl_get( $url, $signature ) {
		$curl = new CURL( $url, $this->debug );
		return $curl->get( $signature );
	}

}

class Request_Exception extends \Exception {}
