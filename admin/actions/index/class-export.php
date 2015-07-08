<?php

namespace Actions\Index;

require_once plugin_dir_path( __FILE__ ) . '../class-action.php';
require_once plugin_dir_path( __FILE__ ) . '../../../includes/exporter/autoload.php';

use Actions\Action as Action;
use Exporter\Exporter as Exporter;
use Exporter\Exporter_Content as Exporter_Content;
use Exporter\Exporter_Content_Settings as Exporter_Content_Settings;

class Export extends Action {

	private $id;

	function __construct( $settings, $id ) {
		parent::__construct( $settings );
		$this->id = $id;
	}

	public function perform() {
		$exporter = $this->fetch_exporter();
		return $exporter->export();
	}

	/**
	 * Fetches an instance of Exporter.
	 */
	public function fetch_exporter() {
		// Fetch WP_Post object, and all required post information to fill up the
		// Exporter_Content instance.
		$post       = get_post( $this->id );
		$post_thumb = wp_get_attachment_url( get_post_thumbnail_id( $this->id ) ) ?: null;
		$author     = get_the_author_meta( 'display_name', $post->post_author );
		$date       = date( 'M j, Y | g:i A', strtotime( $post->post_date ) );
		$byline     = 'by ' . ucfirst( $author ) . ' | ' . $date ;

		$base_content = new Exporter_Content(
			$post->ID,
			$post->post_title,
			// post_content is not raw HTML, as WordPress editor cleans up
			// paragraphs and new lines, so we need to transform the content to
			// HTML. We use 'the_content' filter for that.
			apply_filters( 'the_content', $post->post_content ),
			$post->post_excerpt,
			$post_thumb,
			$byline,
			$this->fetch_content_settings()
		);

		return new Exporter( $base_content, null, $this->settings );
	}

	/**
	 * Loads settings for the Exporter_Content from the WordPress post metadata.
	 *
	 * @since 0.4.0
	 */
	private function fetch_content_settings() {
		$settings = new Exporter_Content_Settings();
		foreach ( get_post_meta( $this->id ) as $name => $value ) {
			if ( 0 === strpos( $name, 'apple_export_' ) ) {
				$name  = str_replace( 'apple_export_', '', $name );
				$value = $value[0];
				$settings->set( $name, $value );
			}
		}
		return $settings;
	}

}
