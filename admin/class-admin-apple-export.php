<?php
/**
 * Entry point for the admin side of the WP Plugin.
 *
 * @author  Federico Ramirez
 * @since   0.0.0
 */

require_once plugin_dir_path( __FILE__ ) . 'class-admin-settings.php';
require_once plugin_dir_path( __FILE__ ) . '../includes/exporter/class-exporter.php';
require_once plugin_dir_path( __FILE__ ) . '../includes/exporter/class-exporter-content.php';

use Exporter\Exporter as Exporter;
use Exporter\Exporter_Content as Exporter_Content;
use Exporter\Settings as Settings;

class Admin_Apple_Export extends Apple_Export {

	function __construct() {
		// This is required to download files and setting headers.
		ob_start();

		// Register hooks
		add_action( 'admin_menu', array( $this, 'setup_pages' ) );

		// Initialize admin settings
		new Admin_Settings();
	}

	/**
	 * Given a post id, export the post into the custom format.
	 */
	private function export( $id ) {
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
			$post_thumb
		);

		$exporter = new Exporter( $base_content, null, $this->fetch_settings() );
		$this->download_zipfile( $exporter->export() );
	}

	/**
	 * Loads the initial settings with the WordPress ones.
	 * @since 0.4.0
	 */
	private function fetch_settings() {
		$settings = new Settings();
		foreach ( $settings->all() as $key => $value ) {
			$wp_value = esc_attr( get_option( $key ) ) ?: $value;
			$settings->set( $key, $wp_value );
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
		if ( $id > 0 ) {
			$this->export( $id );
			return;
		}

		include plugin_dir_path( __FILE__ ) . 'partials/page_index.php';
	}

}
