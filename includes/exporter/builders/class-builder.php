<?php
namespace Exporter\Builders;

abstract class Builder {

	/**
	 * The content object to be exported.
	 *
	 * @since 0.4.0
	 */
	private $content;

	/**
	 * Exporter settings object.
	 *
	 * @since 0.4.0
	 */
	private $settings;

	function __construct( $content, $settings ) {
		$this->content  = $content;
		$this->settings = $settings;
	}

	public function to_array() {
		return $this->build();
	}

	protected abstract function build();

	// Isolate dependencies
	// ------------------------------------------------------------------------

	protected function content_id() {
		return $this->content->id();
	}

	protected function content_title() {
		return $this->content->title() ?: 'Untitled Article';
	}

	protected function content_text() {
		return $this->content->content();
	}

	protected function content_intro() {
		return $this->content->intro();
	}

	protected function content_cover() {
		return $this->content->cover();
	}

	protected function content_setting( $name ) {
		return $this->content->get_setting( $name );
	}

	protected function content_byline() {
		return $this->content->byline();
	}

	protected function content_nodes() {
		return $this->content->nodes();
	}

	protected function get_setting( $name ) {
		return $this->settings->get( $name );
	}

}
