<?php

namespace Actions\Index;

require_once plugin_dir_path( __FILE__ ) . '../class-api-action.php';

use Actions\API_Action as API_Action;

class Delete extends API_Action {

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
		if ( ! $this->is_api_configuration_valid() ) {
			throw new \Actions\Action_Exception( 'Your API settings seem to be empty. Please fill the API key, API
				secret and API channel fields in the plugin configuration page.' );
		}

		$remote_id = get_post_meta( $this->id, 'apple_export_api_id', true );
		if ( ! $remote_id ) {
			throw new \Actions\Action_Exception( 'This post has not been pushed to Apple News, cannot delete.' );
		}

		$error = null;
		try {
			$this->fetch_api()->delete_article( $remote_id );
			// Delete the API references and mark as deleted
			delete_post_meta( $this->id, 'apple_export_api_id' );
			delete_post_meta( $this->id, 'apple_export_api_created_at' );
			delete_post_meta( $this->id, 'apple_export_api_modified_at' );
			delete_post_meta( $this->id, 'apple_export_api_share_url' );
			update_post_meta( $this->id, 'apple_export_api_deleted', time() );
		} catch ( \Exception $e ) {
			return $e->getMessage();
		}
	}

}
