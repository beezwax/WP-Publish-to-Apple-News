<?php
/**
 * This class will post provided specified format articles to a channel using
 * the API.
 */
class API {

	private $endpoint;

	private $key;

	private $secret;

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
    $mime_boundary = md5( time());

		// Build MIME content
    $mime_content  = $this->mime_add_content( 'my_article', 'article.json', $article, $mime_boundary );
    foreach ( $bundles as $bundle ) {
				$mime_content .= $this->mime_add_content_from_file( $mime_boundary, $bundle );
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

	private function mime_add_content( $name, $filename, $content, $mime_boundary ) {
    $eol  = "\r\n";
    $size = strlen( $content );

    $header  = '--' . $mime_boundary . $eol;
    $header .= 'Content-Type: application/json' . $eol;
    $header .= 'Content-Disposition: form-data; name=' . $name . '; filename=' . $filename . '; size=' . $size . $eol . $eol;
    $header .= $content . $eol;

    return $header;
	}

	private function mime_add_content_from_file( $mime_boundary, $filepath, $name = 'a_file' ) {
		$eol         = "\r\n";
		$filename		 = basename( $filepath );
		$filecontent = file_get_contents( $filepath );
    $filesize    = strlen( $filepath );
		$filemime    = $this->get_mime_type_for( $filepath );

    $content  = '--' . $mime_boundary . $eol;
    $content .= "Content-Type: " . $filemime . $eol;
    $content .= "Content-Disposition: form-data; filename=" . $filename . "; name=" . $name . "; size=" . $filesize . $eol . $eol;
    $content .= $filecontent . $eol;

    return $content;
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
