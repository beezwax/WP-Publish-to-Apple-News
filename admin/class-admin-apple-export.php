<?php
/**
 * Entry point for the admin side of the WP Plugin.
 *
 * @author  Federico Ramirez
 * @since   0.0.0
 */

require_once plugin_dir_path( __FILE__ ) . 'class-admin-settings.php';
require_once plugin_dir_path( __FILE__ ) . 'class-apple-export-list-table.php';
// Use exporter
require_once plugin_dir_path( __FILE__ ) . '../includes/exporter/class-exporter.php';
require_once plugin_dir_path( __FILE__ ) . '../includes/exporter/class-exporter-content.php';
require_once plugin_dir_path( __FILE__ ) . '../includes/exporter/class-exporter-content-settings.php';
// Use push API
require_once plugin_dir_path( __FILE__ ) . '../includes/push-api/class-api.php';
require_once plugin_dir_path( __FILE__ ) . '../includes/push-api/class-credentials.php';

use Exporter\Exporter as Exporter;
use Exporter\Exporter_Content as Exporter_Content;
use Exporter\Settings as Settings;
use Exporter\Exporter_Content_Settings as Exporter_Content_Settings;
use Push_API\API as API;
use Push_API\Credentials as Credentials;

/**
 * Main class for the plugin.
 * FIXME: The admin page does too much, split into several classes.
 */
class Admin_Apple_Export extends Apple_Export {

	private $api;
	private $settings;

	const API_ENDPOINT = 'https://u48r14.digitalhub.com';

	function __construct() {
		// This is required to download files and setting headers.
		ob_start();

		// Register hooks
		add_action( 'admin_menu', array( $this, 'setup_admin_page' ) );

		// Admin_Settings builds the settings page for the plugin. It also has
		// helper methods to query them.
		$this->settings = new Admin_Settings();
		$this->api      = null;
	}

	public function setup_admin_page() {
		// Set up main page. This page reads parameters and handles actions
		// accordingly.
		add_menu_page(
			'Apple Export',
			'Apple Export',
			'manage_options',
			$this->plugin_name . '_index',
			array( $this, 'admin_page' )
		);
	}

	/**
	 * Sets up all pages used in the plugin's admin page. Associate each route
	 * with an action. Actions are methods that end with "_action" and must
	 * perform a task and output HTML with the result.
	 *
	 * @since 0.4.0
	 */
	public function admin_page() {
		$id     = intval( $_GET['post_id'] );
		$action = htmlentities( $_GET['action'] );

		// Given an action and ID, map the attributes to corresponding actions.

		if ( ! $id ) {
			switch ( $action ) {
			case 'push':
				$article_list = $_REQUEST['article'];
				return $this->bulk_push_action( $article_list );
			default:
				return $this->show_post_list_action();
			}
		}

		switch ( $action ) {
		case 'settings':
			return $this->settings_action( $id );
		case 'export':
			return $this->export_action( $id );
		case 'push':
			return $this->push_action( $id );
		default:
			wp_die( 'Invalid action: ' . $action );
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

	/**
	 * Fetches an instance of Exporter.
	 */
	private function fetch_exporter( $id ) {
		// The WP_Post object representing the post.
		$post = get_post( $id );
		// The URL of the post's thumbnail (a.k.a featured image), if any.
		$post_thumb = wp_get_attachment_url( get_post_thumbnail_id( $id ) ) ?: null;

		$base_content = new Exporter_Content(
			$post->ID,
			$post->post_title,
			// post_content is not raw HTML, as WordPress editor cleans up
			// paragraphs and new lines, so we need to transform the content to
			// HTML. We use 'the_content' filter for that.
			apply_filters( 'the_content', $post->post_content ),
			$post->post_excerpt,
			$post_thumb,
			$this->fetch_content_settings( $id )
		);

		return new Exporter( $base_content, null, $this->fetch_settings() );
	}

	/**
	 * Loads settings for the Exporter_Content from the WordPress post metadata.
	 *
	 * @since 0.4.0
	 */
	private function fetch_content_settings( $post_id ) {
		$settings = new Exporter_Content_Settings();
		foreach ( get_post_meta( $post_id ) as $name => $value ) {
			if ( 0 === strpos( $name, 'apple_export_' ) ) {
				$name  = str_replace( 'apple_export_', '', $name );
				$value = $value[0];
				$settings->set( $name, $value );
			}
		}
		return $settings;
	}

	/**
	 * Given a post id, export the post into the custom format.
	 */
	private function export( $id ) {
		$exporter = $this->fetch_exporter( $id );
		return $exporter->export();
	}

	/**
	 * Given a post id, push the post using the API data.
	 */
	private function push( $id ) {
		// Check for "valid" API information
		if (  empty( $this->get_setting( 'api_key' ) )
			|| empty( $this->get_setting( 'api_secret' ) )
			|| empty( $this->get_setting( 'api_channel' ) ) )
		{
			wp_die( 'Your API settings seem to be empty. Please fill the API key, API
				secret and API channel fields in the plugin configuration page.' );
			return;
		}

		$exporter = $this->fetch_exporter( $id );
		$exporter->build_article();

		$dir  = $exporter->workspace()->tmp_path();
		$json = file_get_contents( $dir . 'article.json' );

		$bundles = array();
		$files   = glob( $dir . '*', GLOB_BRACE );
		foreach ( $files as $file ) {
			if ( 'article.json' == basename( $file ) ) {
				continue;
			}

			$bundles[] = $file;
		}

		$error = null;
		try {
			$result = $this->fetch_api()->post_article_to_channel( $json, $this->get_setting( 'api_channel' ), $bundles );
			// Save the ID that was assigned to this post in by the API
			update_post_meta( $id, 'apple_export_api_id', $result->data->id );
			update_post_meta( $id, 'apple_export_api_created_at', $result->data->createdAt );
			update_post_meta( $id, 'apple_export_api_modified_at', $result->data->modifiedAt );
		} catch ( \Exception $e ) {
			$error = $e->getMessage();
		} finally {
			$exporter->workspace()->clean_up();
			return $error;
		}
	}

	/**
	 * Gets an instance of Exporter Settings loaded from WordPress saved options.
	 *
	 * @since 0.4.0
	 */
	private function fetch_settings() {
		return $this->settings->fetch_settings();
	}

	/**
	 * Gets a setting by name which was loaded from WordPress options.
	 *
	 * @since 0.4.0
	 */
	private function get_setting( $name ) {
		return $this->fetch_settings()->get( $name );
	}

	/**
	 * Given the full path to a zip file INSIDE THE PLUGN DIRECTORY, redirect
	 * to the appropriate URL to download it.
	 */
	private function download_zipfile( $path ) {
		header( 'Content-Type: application/zip, application/octet-stream' );
		header( 'Content-Transfer-Encoding: Binary' );
		header( 'Content-Disposition: attachment; filename="' . basename( $path ) . '"' );

		ob_clean();
		flush();
		readfile( $path );
		exit;
	}

	// Actions
	// -------------------------------------------------------------------------

	private function bulk_push_action( $articles ) {
		$errors = $this->bulk_push( $articles );
		if ( $errors ) {
			// TODO: Show errors page
			var_dump( $errors );
		} else {
			// TODO: Show success page
			echo 'Pushed articles';
		}
	}

	private function show_post_list_action() {
		$table = new Apple_Export_List_Table();
		$table->prepare_items();
		include plugin_dir_path( __FILE__ ) . 'partials/page_index.php';
	}

	private function settings_action( $id ) {
		if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
			update_post_meta( $id, 'apple_export_pullquote', $_POST['pullquote'] );
			update_post_meta( $id, 'apple_export_pullquote_position', intval( $_POST['pullquote_position'] ) );
			$message = 'Settings saved.';
		}

		$post      = get_post( $id );
		$post_meta = get_post_meta( $id );
		include plugin_dir_path( __FILE__ ) . 'partials/page_single_settings.php';
	}

	private function export_action( $id ) {
		$path = $this->export( $id );
		$this->download_zipfile( $path );
	}

	private function push_action( $id ) {
		$error = $this->push( $id );
		if ( is_null( $error ) ) {
			echo 'Your article has been pushed successfully!';
		} else {
			echo 'Oops, something happened: ' . $error;
		}
	}

	private function bulk_push( $articles ) {
		$errors = array();
		if ( empty( $articles ) ) {
			$errors[] = 'No articles selected.';
			return $errors;
		}

		foreach ( $articles as $article_id ) {
			$error = $this->push( $article_id );
			if ( ! is_null( $error ) ) {
				$errors[] = $error;
			}
		}
		return $errors;
	}

}
