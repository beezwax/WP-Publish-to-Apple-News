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

		// Register update hooks if needed
		if ( 'yes' == $settings->get( 'api_autosync' ) ) {
			add_action( 'publish_post', array( $this, 'on_publish' ), 10, 2 );
			add_action( 'before_delete_post', array( $this, 'on_delete' ) );
			add_filter( 'redirect_post_location', array( $this, 'on_redirect' ) );
		}
	}

	/**
	 * When a post is published, or a published post updated, trigger this
	 * function.
	 *
	 * @since 0.4.0
	 */
	public function on_publish( $id, $post ) {
		// If the post has been marked as deleted from the API, ignore this update
		$deleted = get_post_meta( $id, 'apple_export_api_deleted', true );
		if ( $deleted ) {
			return;
		}

		$action = new Actions\Index\Push( $this->settings, $id );
		try {
			$action->perform();
		} catch ( Actions\Action_Exception $e ) {
			Flash::error( $e->getMessage() );
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
		try {
			$action->perform();
		} catch ( Actions\Action_Exception $e ) {
			Flash::error( $e->getMessage() );
		}
	}

	public function on_redirect( $location ) {
		if ( Flash::has_flash() ) {
			return 'admin.php?page=apple_export_index';
		}

		return $location;
	}

}
