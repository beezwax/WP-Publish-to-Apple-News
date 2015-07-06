<?php

/**
 * This class is in charge of syncing posts creation, updates and deletions
 * with Apple's News API.
 *
 * @since 0.4.0
 */
class Admin_Post_Sync {

	private $exporter;

	function __construct( $exporter ) {
		$this->exporter = $exporter;

		add_action( 'save_post', array( $this, 'on_post_saved' ), 10, 3 );
	}

	public function on_post_saved( $id, $post, $update ) {
		if ( wp_is_post_revision( $id ) || 'POST' != $_SERVER['REQUEST_METHOD'] ) {
			return;
		}

		$error = null;

		if ( get_post_meta( $id, 'apple_export_api_id', true ) ) {
			// TODO: Update not done yet
		} else {
			$error = $this->exporter->push( $id );
		}

		if ( $error ) {
			wp_die( $error );
		}
	}

}
