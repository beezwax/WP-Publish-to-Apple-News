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
require_once plugin_dir_path( __FILE__ ) . '../includes/exporter/class-exporter-content-settings.php';

use Exporter\Exporter as Exporter;
use Exporter\Exporter_Content as Exporter_Content;
use Exporter\Settings as Settings;
use Exporter\Exporter_Content_Settings as Exporter_Content_Settings;

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
			$post_thumb,
			$this->fetch_content_settings( $id )
		);

		$exporter = new Exporter( $base_content, null, $this->fetch_settings() );
		$this->download_zipfile( $exporter->export() );
	}

	/**
	 * Loads the global settings from the WordPress options.
	 *
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
			include plugin_dir_path( __FILE__ ) . 'partials/page_index.php';
			return;
		}

		// Confirmed post export
		if ( $_POST && wp_verify_nonce( $_POST['apple-export-nonce'], 'export' ) ) {
			// Save post metadata
			update_post_meta( $id, 'apple_export_pullquote', $_POST['pullquote'] );
			update_post_meta( $id, 'apple_export_pullquote_position', intval( $_POST['pullquote_position'] ) );
			// Export
			$this->export( $id );
			return;
		}

		// Show single post
		$post      = get_post( $id );
		$post_meta = get_post_meta( $id );
		include plugin_dir_path( __FILE__ ) . 'partials/page_single.php';
	}

}
