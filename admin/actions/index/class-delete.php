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
			// Delete the API references and mark as deleted
			delete_post_meta( $this->id, 'apple_export_api_id' );
			delete_post_meta( $this->id, 'apple_export_api_created_at' );
			delete_post_meta( $this->id, 'apple_export_api_modified_at' );
			update_post_meta( $this->id, 'apple_export_api_deleted', time() );
		} catch ( \Exception $e ) {
			return $e->getMessage();
		}
	}

}
