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
		if ( wp_is_post_revision( $id ) ) {
			return;
		}

		if ( $update ) {
			$this->update_post( $id );
		} else {
			$this->insert_post( $id );
		}
	}

	private function update_post( $post_id ) {
		// TODO: No UPDATE interface in API yet
	}

	private function insert_post( $post_id ) {
		$this->exporter->push( $post_id );
	}

}
