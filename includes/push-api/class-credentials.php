<?php
namespace Push_API;

class Credentials {

	/**
	 * The key used in the authentication process, this is provided as part of
	 * the API credentials and should be safely stored in the server, do not
	 * hard-code it in the source code.
	 *
	 * @var string
	 * @since 0.2.0
	 */
	private $key;

	/**
	 * The secret used in the authentication process, this is provided as part of
	 * the API credentials and should be safely stored in the server, do not
	 * hard-code it in the source code.
	 *
	 * @var string
	 * @since 0.2.0
	 */
	private $secret;


	function __construct( $key, $secret ) {
		$this->secret = $secret;
		$this->key    = $key;
	}

	function key() {
		return $this->key;
	}

	function secret() {
		return $this->secret;
	}

}
