<?php
/**
 * Entry point for the admin side of the WP Plugin.
 *
 * @author  Federico Ramirez
 * @since   0.0.0
 */

require_once plugin_dir_path( __FILE__ ) . '../includes/exporter/class-exporter.php';
require_once plugin_dir_path( __FILE__ ) . '../includes/exporter/class-exporter-content.php';

class Admin_Apple_Export extends Apple_Export {

	function __construct() {
		// This is required to download files and setting headers.
		ob_start();

		// Register hooks
		add_action( 'admin_menu', array( $this, 'setup_pages' ) );
	}

	/**
	 * Given a post id, export the post into the custom format.
	 */
	private function export( $id ) {
		// The WP_Post object representing the post.
		$post       = get_post( $id );
		// The URL of the post's thumbnail (a.k.a featured image), if any.
		$post_thumb = wp_get_attachment_url( get_post_thumbnail_id( $id ) ) ?: null;

		$base_content = new Exporter\Exporter_Content(
			$post->ID,
			$post->post_title,
			// post_content is not raw HTML, as WordPress editor cleans up
			// paragraphs and new lines, so we need to transform the content to
			// HTML. We use 'the_content' filter for that.
			apply_filters( 'the_content', $post->post_content ),
			$post->post_excerpt,
			$post_thumb
		);

		$exporter = new Exporter\Exporter( $base_content );
		$this->download_zipfile( $exporter->export() );
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
		$this->page_index();
		$this->page_options();
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

	/**
	 * Options page setup
	 */
	public function page_options() {
		add_options_page(
			'Apple Export Options',
			'Apple Export',
			'manage_options',
			$this->plugin_name . '_options',
			array( $this, 'page_options_render' )
		);
	}

	public function page_options_render() {
		if ( ! current_user_can( 'manage_options' ) )
			wp_die( __( 'You do not have permissions to access this page.' ) );

		include plugin_dir_path( __FILE__ ) . 'partials/page_options.php';
	}

}
