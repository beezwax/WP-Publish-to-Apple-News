<?php
/**
 * This class will post provided specified format articles to a channel using
 * the API.
 *
 * @since 0.0.0
 */
class API {

	/**
	 * The endpoint to connect to.
	 *
	 * @since 0.0.0
	 */
	private $endpoint;

	/**
	 * The key used in the authentication process, this is provided as part of
	 * the API credentials and should be safely stored in the server, do not
	 * hard-code it in the source code.
	 *
	 * @since 0.0.0
	 */
	private $key;

	/**
	 * The secret used in the authentication process, this is provided as part of
	 * the API credentials and should be safely stored in the server, do not
	 * hard-code it in the source code.
	 *
	 * @since 0.0.0
	 */
	private $secret;

	/**
	 * Whether or not to use a reverse proxy like Charles to send requests though.
	 *
	 * @since 0.0.0
	 */
	private $debug;

	function __construct( $endpoint, $key, $secret, $debug = false ) {
		$this->endpoint = $endpoint;
		$this->key      = $key;
		$this->secret   = $secret;
		$this->debug    = $debug;
	}

	private function build_header( $verb, $url, $content_type = null, $post_body = null ) {
    $current_date = date( 'c' );

    if ( 'GET' == $verb ) {
        $request_info = $verb . $url . $current_date;
    } else if ( $verb === 'POST' ) {
        $request_info = $verb . $url . $current_date . $content_type . $post_body;
    } else {
			throw new Exception( 'Unrecognized verb. Only GET and POST are allowed.' );
    }

		$secret_key = base64_decode( $this->secret );
    $hash       = hash_hmac( 'sha256', $request_info, $secret_key, true );
		$signature  = base64_encode( $hash );

    return 'Authorization: HHMAC; key=' . $this->key . '; signature=' . $signature . '; date=' . $current_date;
	}

	public function post_article_to_channel( $article, $channel_uuid, $bundles = array() ) {
		$url = $this->endpoint . '/channels/' . $channel_uuid . '/articles';
		$content_length = strlen( $article );

		// This is used all around to generate the MIME request
    $mime_boundary = md5( time() );

		// Build MIME content
    $mime_content  = $this->mime_add_json_string( 'my_article', 'article.json', $article, $mime_boundary );
    foreach ( $bundles as $bundle ) {
				$mime_content .= $this->mime_add_content_from_file( $bundle, $mime_boundary );
    }
		$mime_content .= $this->mime_close( $mime_boundary );


		// TODO: Make a request object to wrap CURL requests
		// Set CURL options
		$curl = curl_init( $url );

		// If we want to debug using a reverse proxy, like Charles.
    if ( $this->debug ) {
        curl_setopt( $curl, CURLOPT_PROXY, "127.0.0.1" );
        curl_setopt( $curl, CURLOPT_PROXYPORT, 8888 );
    }

    curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt( $curl, CURLOPT_INFILESIZE, $content_length );
		// Make curl_exec return the request result rather than just true.
    curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );

    $content_type = 'multipart/form-data; boundary=' . $mime_boundary;
    $auth_header = $this->build_header( 'POST', $url, $content_type, $mime_content );
    curl_setopt( $curl, CURLOPT_HTTPHEADER, array(
        'Content-Length: ' . strlen( $mime_content ),
        'Content-Type: multipart/form-data; boundary=' . $mime_boundary,
        $auth_header
      )
    );
    curl_setopt( $curl, CURLOPT_POST, true );
    curl_setopt( $curl, CURLOPT_POSTFIELDS, $mime_content );

		// Execute CURL request
    $curl_response = curl_exec( $curl );
    if ( false === $curl_response ) {
				$error = curl_error( $curl );
        curl_close( $curl );
				throw new Exception( "Curl request failed: $error" );
    }

    curl_close($curl);
    $response = json_decode( $curl_response );
    if ( isset( $response->response->status ) && $response->response->status == 'ERROR' ) {
        die('error occured: ' . $response->response->errormessage);
				throw new Exception( "Server error: " . $response->response->errormessage );
    }

    return $response;
	}

	private function mime_build_attachment( $mime_boundary, $name, $filename, $content, $mime_type ) {
    $eol  = "\r\n";
    $size = strlen( $content );

    $attachment  = '--' . $mime_boundary . $eol;
    $attachment .= 'Content-Type: ' . $mime_type . $eol;
    $attachment .= 'Content-Disposition: form-data; name=' . $name . '; filename=' . $filename . '; size=' . $size . $eol . $eol;
    $attachment .= $content . $eol;

		return $attachment;
	}

	private function mime_add_json_string( $name, $filename, $content, $mime_boundary ) {
		return $this->mime_build_attachment( $mime_boundary, $name, $filename, $content, 'application/json' );
	}

	private function mime_add_content_from_file( $filepath, $mime_boundary, $name = 'a_file' ) {
		$filename		 = basename( $filepath );
		$filecontent = file_get_contents( $filepath );
		$filemime    = $this->get_mime_type_for( $filepath );

		return $this->mime_build_attachment( $mime_boundary, $name, $filename, $filecontent, $filemime );
	}

	private function mime_close( $mime_boundary ) {
		return '--' . $mime_boundary . '--';
	}

	private function get_mime_type_for( $filepath ) {
		$finfo = finfo_open( FILEINFO_MIME_TYPE );
		$type  = finfo_file( $finfo, $filepath );

		if( $this->is_valid_mime_type( $type ) ) {
			return $type;
		}

		return 'application/octet-stream';
	}

	private function is_valid_mime_type( $type ) {
		return in_array( $type, array (
			'image/jpeg',
			'image/png',
			'image/gif',
			'application/font-sfnt',
			'application/x-font-truetype',
			'application/font-truetype',
			'application/vnd.ms-opentype',
			'application/x-font-opentype',
			'application/font-opentype',
			'application/octet-stream',
		) );
	}

}
