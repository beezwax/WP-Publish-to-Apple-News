<?php

namespace Actions;

require_once plugin_dir_path( __FILE__ ) . 'class-action.php';
require_once plugin_dir_path( __FILE__ ) . '../../includes/push-api/autoload.php';

use Actions\Action as Action;
use Push_API\API as API;
use Push_API\Credentials as Credentials;

/**
 * A base class that API-related actions can extend.
 */
abstract class API_Action extends Action {

	const API_ENDPOINT = 'https://u48r14.digitalhub.com';

	protected function fetch_api() {
		if ( is_null( $this->api ) ) {
			$this->api = new API( self::API_ENDPOINT, $this->fetch_credentials() );
		}

		return $this->api;
	}

	private function fetch_credentials() {
		$key    = $this->get_setting( 'api_key' );
		$secret = $this->get_setting( 'api_secret' );
		return new Credentials( $key, $secret );
	}

}
