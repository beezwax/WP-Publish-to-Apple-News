<?php

namespace Actions\Index;

require_once __DIR__ . '/../class-api-action.php';

class Delete extends \Actions\API_Action {

	private $id;

	function __construct( $settings, $id ) {
		parent::__construct( $settings );
		$this->id = $id;
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

}
