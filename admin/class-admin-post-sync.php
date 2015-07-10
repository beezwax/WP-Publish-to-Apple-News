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

		add_action( 'publish_post', array( $this, 'on_publish' ), 10, 2 );
		add_action( 'before_delete_post', array( $this, 'on_delete' ) );
	}

	/**
	 * When a post is published, or a published post updated, trigger this
	 * function.
	 *
	 * @since 0.4.0
	 */
	public function on_publish( $id, $post ) {
		// TODO: UPDATE method is not yet supported by the API, for now, the
		// Exporter's push method DELETEs a post if it has an API ID and then sends
		// a POST request to create a new one.
		$error = $this->exporter->push( $id );
		if ( $error ) {
			wp_die( $error );
		}
	}

	/**
	 * When a post is deleted, remove it from Apple News.
	 *
	 * @since 0.4.0
	 */
	public function on_delete( $id ) {
		// If it does not have a remote API ID just ignore
		if ( ! get_post_meta( $id, 'apple_export_api_id', true ) ) {
			return;
		}

		$error = $this->exporter->delete( $id );
		if ( $error ) {
			wp_die( $error );
		}
	}

}
