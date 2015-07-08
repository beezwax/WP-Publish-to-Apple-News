<?php

require_once plugin_dir_path( __FILE__ ) . 'actions/index/class-push.php';
require_once plugin_dir_path( __FILE__ ) . 'actions/index/class-delete.php';

/**
 * This class is in charge of syncing posts creation, updates and deletions
 * with Apple's News API.
 *
 * @since 0.4.0
 */
class Admin_Post_Sync {

	private $settings;

	function __construct( $settings ) {
		$this->settings = $settings;

		add_action( 'publish_post', array( $this, 'on_publish' ), 10, 2 );
		add_action( 'before_delete_post', array( $this, 'on_delete' ) );
	}

	/**
	 * When a post is published, or a published post updated, trigger this
	 * function.
	 *
	 * TODO: UPDATE method is not yet supported by the API, for now, the
	 * Exporter's push method DELETEs a post if it has an API ID and then sends a
	 * POST request to create a new one.
	 *
	 * @since 0.4.0
	 */
	public function on_publish( $id, $post ) {
		$action = new Actions\Index\Push( $this->settings, $id );
		$error = $action->perform();
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

		$action = new Actions\Index\Delete( $this->settings, $id );
		$error  = $action->perform();
		if ( $error ) {
			wp_die( $error );
		}
	}

}
