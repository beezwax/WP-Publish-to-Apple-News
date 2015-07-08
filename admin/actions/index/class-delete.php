<?php

namespace Actions\Index;

require_once __DIR__ . '/../class-action.php';

use Actions\Action as Action;
use Push_API\API as API;
use Push_API\Credentials as Credentials;

class Delete extends Action {

	const API_ENDPOINT = 'https://u48r14.digitalhub.com';

	private $id;

	function __construct( $settings, $id ) {
		parent::__construct( $settings );
		$this->id       = $id;
	}

	/**
	 * Must be implemented when extending Action. Performs the action and returns
	 * errors if any, null otherwise.
	 *
	 * @since 0.6.0
	 */
	public function perform() {
		return $this->delete();
	}

	/**
	 * Push the post using the API data.
	 */
	private function delete() {
		// Check for "valid" API information
		if ( empty( $this->get_setting( 'api_key' ) )
			|| empty( $this->get_setting( 'api_secret' ) )
			|| empty( $this->get_setting( 'api_channel' ) ) )
		{
			wp_die( 'Your API settings seem to be empty. Please fill the API key, API
				secret and API channel fields in the plugin configuration page.' );
			return;
		}

		$remote_id = get_post_meta( $this->id, 'apple_export_api_id', true );
		if ( ! $remote_id ) {
			wp_die( 'This post has not been pushed to Apple News, cannot delete.' );
			return;
		}

		$error = null;
		try {
			$this->fetch_api()->delete_article( $remote_id );
			delete_post_meta( $this->id, 'apple_export_api_id' );
			delete_post_meta( $this->id, 'apple_export_api_created_at' );
			delete_post_meta( $this->id, 'apple_export_api_modified_at' );
		} catch ( \Exception $e ) {
			$error = $e->getMessage();
		} finally {
			return $error;
		}
	}

	private function fetch_api() {
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
