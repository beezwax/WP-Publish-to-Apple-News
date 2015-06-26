<?php
namespace Exporter;

require_once plugin_dir_path( __FILE__ ) . 'class-exporter-content-settings.php';

/**
 * Represents a generic way to represent content that must be exported. This
 * can be filled based on a WordPress post for example.
 *
 * @since 0.2.0
 */
class Exporter_Content {

	private $id;
	private $title;
	private $content;
	private $intro;
	private $cover;
	private $settings;

	function __construct( $id, $title, $content, $intro = null, $cover = null, $settings = null ) {
		$this->id       = $id;
		$this->title    = $title;
		$this->content  = $content;
		$this->intro    = $intro;
		$this->cover    = $cover;
		$this->settings = $settings ?: new Exporter_Content_Settings();
	}

	public function id() {
		return $this->id;
	}

	public function title() {
		return $this->title;
	}

	public function content() {
		return $this->content;
	}

	public function intro() {
		return $this->intro;
	}

	public function cover() {
		return $this->cover;
	}

	public function get_setting( $name ) {
		return $this->settings->get( $name );
	}

}
