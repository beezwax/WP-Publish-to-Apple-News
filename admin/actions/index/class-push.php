<?php

namespace Actions\Index;

require_once plugin_dir_path( __FILE__ ) . '../class-api-action.php';
require_once plugin_dir_path( __FILE__ ) . 'class-export.php';

use Actions\API_Action as API_Action;

class Push extends API_Action {

	private $id;
	private $exporter;

	function __construct( $settings, $id ) {
		parent::__construct( $settings );
		$this->id       = $id;
		$this->exporter = null;
	}

	public function perform() {
		return $this->push();
	}

	/**
	 * Push the post using the API data.
	 */
	private function push() {
		// Check for "valid" API information
		if ( empty( $this->get_setting( 'api_key' ) )
			|| empty( $this->get_setting( 'api_secret' ) )
			|| empty( $this->get_setting( 'api_channel' ) ) )
		{
			wp_die( 'Your API settings seem to be empty. Please fill the API key, API
				secret and API channel fields in the plugin configuration page.' );
			return;
		}

		list( $json, $bundles ) = $this->generate_article();

		$error = null;
		try {
			// If there's an API ID, delete the post before pushing the new version
			$remote_id = get_post_meta( $this->id, 'apple_export_api_id', true );
			if ( $remote_id ) {
				$this->fetch_api()->delete_article( $remote_id );
			}

			$result = $this->fetch_api()->post_article_to_channel( $json, $this->get_setting( 'api_channel' ), $bundles );
			// Save the ID that was assigned to this post in by the API
			update_post_meta( $this->id, 'apple_export_api_id', $result->data->id );
			update_post_meta( $this->id, 'apple_export_api_created_at', $result->data->createdAt );
			update_post_meta( $this->id, 'apple_export_api_modified_at', $result->data->modifiedAt );
		} catch ( \Exception $e ) {
			$error = $e->getMessage();
		} finally {
			$this->clean_workspace();
			return $error;
		}
	}

	private function clean_workspace() {
		if( is_null( $this->exporter ) ) {
			return;
		}

		$this->exporter->workspace()->clean_up();
	}

	/**
	 * Use the export action to get an instance of the Exporter. Use that to
	 * manually generate the workspace for upload, then clean it up.
	 *
	 * @since 0.6.0
	 */
	private function generate_article() {
		$export_action = new Export( $this->settings, $this->id );
		$this->exporter = $export_action->fetch_exporter();
		$this->exporter->generate();

		$dir  = $this->exporter->workspace()->tmp_path();
		$json = file_get_contents( $dir . 'article.json' );

		$bundles = array();
		$files   = glob( $dir . '*', GLOB_BRACE );
		foreach ( $files as $file ) {
			if ( 'article.json' == basename( $file ) ) {
				continue;
			}

			$bundles[] = $file;
		}

		return array( $json, $bundles );
	}

}
