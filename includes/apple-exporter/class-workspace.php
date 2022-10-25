<?php
/**
 * Publish to Apple News: \Apple_Exporter\Workspace class
 *
 * @package Apple_News
 * @subpackage Apple_Exporter
 */

namespace Apple_Exporter;

/**
 * Manage the exporter's workspace.
 * For WordPress, this is entirely handled using meta fields
 * since the filesystem is unavailable on WordPress VIP and
 * potentially other enterprise WordPress hosts.
 *
 * @author  Federico Ramirez
 * @author  Bradford Campeau-Laurion
 * @since   0.2.0
 */
class Workspace {

	/**
	 * Meta key used to store the json content with the post.
	 *
	 * @var string
	 * @since 0.9.0
	 */
	const JSON_META_KEY = 'apple_news_api_json';

	/**
	 * Meta key used to store bundled assets with the post.
	 *
	 * @var string
	 * @since 0.9.0
	 */
	const BUNDLE_META_KEY = 'apple_news_api_bundle';

	/**
	 * Meta key used to store errors encountered with the post.
	 *
	 * @var string
	 * @since 0.9.0
	 */
	const ERRORS_META_KEY = 'apple_news_api_errors';

	/**
	 * Current ID of the content we are constructing a workspace for.
	 *
	 * @var int
	 * @since 0.9.0
	 */
	public $content_id;

	/**
	 * Initialize.
	 *
	 * @since 0.2.0
	 * @param int $content_id The ID of the post being exported.
	 * @access public
	 */
	public function __construct( $content_id ) {
		$this->content_id = $content_id;
	}

	/**
	 * Delete all bundle data from the post.
	 *
	 * @since 0.2.0
	 * @access public
	 */
	public function clean_up() {
		/**
		 * Actions to be taken before postmeta is cleaned up for a post.
		 *
		 * Cleaned postmeta includes the generated JSON, the bundles, and errors.
		 */
		do_action( 'apple_news_before_clean_up' );

		delete_post_meta( $this->content_id, self::JSON_META_KEY );
		delete_post_meta( $this->content_id, self::BUNDLE_META_KEY );
		delete_post_meta( $this->content_id, self::ERRORS_META_KEY );

		/**
		 * Actions to be taken after postmeta is cleaned up for a post.
		 *
		 * Cleaned postmeta includes the generated JSON, the bundles, and errors.
		 */
		do_action( 'apple_news_after_clean_up' );
	}

	/**
	 * Adds a source file to be included later in the bundle.
	 *
	 * @since 0.9.0
	 * @param string $filename The filename to be included.
	 * @param string $source   The full path to the source.
	 * @access public
	 */
	public function bundle_source( $filename, $source ) {
		/** This filter is documented in includes/apple-exporter/builders/class-builder.php */
		add_post_meta( $this->content_id, self::BUNDLE_META_KEY, esc_url_raw( apply_filters( 'apple_news_bundle_source', $source, $filename, $this->content_id ) ) );
	}

	/**
	 * Stores the JSON file for this workspace to be included in the bundle.
	 *
	 * @since 0.9.0
	 * @param string $content The JSON to be saved with the post.
	 * @access public
	 */
	public function write_json( $content ) {
		/**
		 * Similar to `apple_news_generate_json`, but modifies the JSON before
		 * it's written to the workspace.
		 *
		 * @param string $json    The JSON string, before it is written to the workspace.
		 * @param int    $post_id The post ID.
		 */
		$json = apply_filters( 'apple_news_write_json', $content, $this->content_id );

		/**
		 * JSON should be decoded before being stored.
		 * Otherwise, stripslashes_deep could potentially remove valid characters
		 * such as newlines (\n).
		 */
		$decoded_json = json_decode( $json );
		if ( null === $decoded_json ) {
			// This is invalid JSON.
			// Store as an empty string.
			$decoded_json = '';
		}
		update_post_meta( $this->content_id, self::JSON_META_KEY, $decoded_json );
	}

	/**
	 * Gets the JSON content.
	 *
	 * @since 0.9.0
	 * @access public
	 * @return string The JSON for this post.
	 */
	public function get_json() {
		$json = get_post_meta( $this->content_id, self::JSON_META_KEY, true );
		if ( ! empty( $json ) ) {
			$json = wp_json_encode( $json );
		}

		/**
		 * Similar to `apple_news_generate_json`, but modifies the JSON as it's
		 * retrieved from the workspace.
		 *
		 * @param string $json    The JSON string, after it is retrieved from the workspace.
		 * @param int    $post_id The post ID.
		 */
		return apply_filters( 'apple_news_get_json', $json, $this->content_id );
	}

	/**
	 * Gets any bundles.
	 *
	 * @since 0.9.0
	 * @access public
	 * @return array The bundles configured for this post.
	 */
	public function get_bundles() {
		/**
		 * Modifies the list of bundled assets. This is an array of images that
		 * were located in the post and need to be sent to Apple News.
		 *
		 * @param array $bundles The bundles for this post.
		 * @param int   $post_id The post ID.
		 */
		return apply_filters( 'apple_news_get_bundles', get_post_meta( $this->content_id, self::BUNDLE_META_KEY ), $this->content_id );
	}

	/**
	 * Logs errors encountered during publishing.
	 *
	 * @since 1.0.6
	 * @param string $key   The error key.
	 * @param string $value The error value.
	 * @access public
	 */
	public function log_error( $key, $value ) {
		// Get current errors.
		$errors = get_post_meta( $this->content_id, self::ERRORS_META_KEY, true );

		// Initialize if needed.
		if ( empty( $errors ) ) {
			$errors = [];
		}

		// Initialize the key if needed.
		if ( empty( $errors[ $key ] ) ) {
			$errors[ $key ] = [];
		}

		// Log the error.
		$errors[ $key ][] = $value;

		// Save the errors.
		update_post_meta( $this->content_id, self::ERRORS_META_KEY, $errors );
	}

	/**
	 * Gets errors encountered during publishing.
	 *
	 * @since 1.0.6
	 * @access public
	 * @return array An array of errors for this post.
	 */
	public function get_errors() {
		/**
		 * Modifies the list of errors encountered during publishing.
		 *
		 * This would allow you to manipulate this list prior to them being used
		 * for validation against your alert settings and before they are displayed
		 * as notices in the dashboard.
		 *
		 * @param array $errors  Errors for this post.
		 * @param int   $post_id The post ID.
		 */
		return apply_filters( 'apple_news_get_errors', get_post_meta( $this->content_id, self::ERRORS_META_KEY ), $this->content_id );
	}
}
