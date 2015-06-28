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

class Admin_Apple_Export extends Apple_Export {

	private $api;
	private $settings;

	function __construct() {
		// This is required to download files and setting headers.
		ob_start();

		// Register hooks
		add_action( 'admin_menu', array( $this, 'setup_pages' ) );

		// Initialize admin settings page
		$this->settings = new Admin_Settings();
		$this->initialize_api();
	}

	function initialize_api() {
		// Build credentials
		$key = $this->get_setting( 'api_key' );
		$secret = $this->get_setting( 'api_secret' );
		$credentials = new Credentials( $key, $secret );
		// Build API
		$endpoint = 'https://u48r14.digitalhub.com';
		$this->api = new API( $endpoint, $credentials );
	}

	/**
	 * Fetches an instance of Exporter.
	 */
	private function fetch_exporter( $id ) {
		// The WP_Post object representing the post.
		$post       = get_post( $id );
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
		if(  empty( $this->get_setting( 'api_key' ) )
			|| empty( $this->get_setting( 'api_secret' ) )
			|| empty( $this->get_setting( 'api_channel' ) ) ) {

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
			$result = $this->api->post_article_to_channel( $json, $this->get_setting( 'api_channel' ), $bundles );
			// Save the ID that was assigned to this post in by the API
			update_post_meta( $id, 'apple_export_api_id', $result->data->id );
			update_post_meta( $id, 'apple_export_api_created_at', $result->data->createdAt );
			update_post_meta( $id, 'apple_export_api_modified_at', $result->data->modifiedAt );
		} catch( \Exception $e ) {
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

	public function setup_pages() {
		// Only one page for now.
		$this->page_index();
	}

	/**
	 * Index page setup
	 */
	public function page_index() {
		add_menu_page(
			'Apple Export',
			'Apple Export',
			'manage_options',
			$this->plugin_name . '_index',
			array( $this, 'page_index_render' )
		);
	}

	public function page_index_render() {
		$id = intval( $_GET['post_id'] );

		// Show all posts if id is not set
		if( ! $id ) {
			$table = new Apple_Export_List_Table();
			$table->prepare_items();
			include plugin_dir_path( __FILE__ ) . 'partials/page_index.php';
			return;
		}

		$action = $_GET['action'];
		if( 'settings' == $action ) {
			if( $_POST ) {
				update_post_meta( $id, 'apple_export_pullquote', $_POST['pullquote'] );
				update_post_meta( $id, 'apple_export_pullquote_position', intval( $_POST['pullquote_position'] ) );
				$message = 'Settings saved.';
			}

			$post      = get_post( $id );
			$post_meta = get_post_meta( $id );
			include plugin_dir_path( __FILE__ ) . 'partials/page_single_settings.php';
			return;
		}

		if( 'export' == $action ) {
			$path = $this->export( $id );
			$this->download_zipfile( $path );
			return;
		}

		if( 'push' == $action ) {
			$error = $this->push( $id );
			if( is_null( $error ) ) {
				echo 'Your article has been pushed successfully!';
			} else {
				echo 'Oops, something happened: ' . $error;
			}
			return;
		}

		wp_die( 'Invalid action: ' . $action );
	}

}
