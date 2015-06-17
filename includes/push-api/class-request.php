<?php
namespace Push_API;

require_once __DIR__ . '/class-mime-builder.php';
require_once __DIR__ . '/class-request-curl.php';

/**
 * An object capable of sending signed HTTP requests to the Push API.
 *
 * @since 0.0.0
 */
class Request {

	/**
	 * The URL the request will be sent to.
	 *
	 * @var string
	 * @since 0.0.0
	 */
	private $url;

	/**
	 * The method we'll use for the request. Assumes GET. Can be either POST or
	 * GET.
	 *
	 * @var string Can either be POST or GET. If it's invalid, assumes GET.
	 * @since 0.0.0
	 */
	private $verb;

	/**
	 * Helper class used to build the MIME parts of the request.
	 *
	 * @var MIME_Builder
	 * @since 0.0.0
	 */
	private $mime_builder;

	/**
	 * Whether or not we are debugging using a reverse proxy, like Charles.
	 *
	 * @var boolean
	 * @since 0.0.0
	 */
	private $debug;

	/**
	 * The signature used to authenticate the sent request.
	 *
	 * @var string
	 * @since 0.0.0
	 */
	private $signature;

	/**
	 * The content this request holds, in MIME format.
	 *
	 * @var string
	 * @since 0.0.0
	 */
	private $content;

	function __construct( $url, $verb = 'GET', $debug = false, $mime_builder = null ) {
		$this->url          = $url;
		$this->verb         = $verb;
		$this->mime_builder = $mime_builder ?: new MIME_Builder();
		$this->debug        = $debug;
		$this->signature    = null;
		$this->content      = null;
	}

	/**
	 * Given an article, builds the request's content.
	 *
	 * TODO: Should article be an Export_Content?
	 *
	 * @param string $article The JSON contents of the article
	 * @param array  $bundles The paths of the article's bundled files. Names
	 *                        must match the ones specified in the JSON spec.
	 *
	 * @since 0.0.0
	 */
	public function set_article( $article, $bundles = array() ) {
		$this->content = $this->mime_builder->add_json_string( 'my_article', 'article.json', $article );
		foreach ( $bundles as $bundle ) {
			$this->content .= $this->mime_builder->add_content_from_file( $bundle );
		}
		$this->content .= $this->mime_builder->close();
	}

	/**
	 * Authenticates the content we are sending, "signing" it with the
	 * credentials passed.
	 *
	 * @param Push_API/Credentials $credentials The credentials that will be used
	 *                                          to sign the request.
	 */
	public function authenticate( $credentials ) {
		if ( 'POST' == $this->verb && is_null( $this->content ) ) {
			throw new Request_Exception( 'POST requests must add content before signing it.' );
		}

		$current_date = date( 'c' );
		$request_info = $this->verb . $this->url . $current_date;

		if ( 'POST' == $this->verb ) {
			$content_type = 'multipart/form-data; boundary=' . $this->mime_builder->boundary();
			$request_info .= $content_type . $this->content;
		}

		$secret_key = base64_decode( $credentials->secret() );
		$hash       = hash_hmac( 'sha256', $request_info, $secret_key, true );
		$signature  = base64_encode( $hash );

		$this->signature = 'Authorization: HHMAC; key=' . $credentials->key() . '; signature=' . $signature . '; date=' . $current_date;
	}

	/**
	 * Send the request using CURL.
	 *
	 * @since 0.0.0
	 */
	public function send() {
		$curl = new Request_CURL( $this->url, $this->debug );

		$response = null;
		if ( 'POST' == $this->verb ) {
			$response = $curl->post( $this->content, $this->mime_builder->boundary(), $this->signature  );
		} else {
			$response = $curl->get( $this->signature  );
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

}

class Request_Exception extends \Exception {}
