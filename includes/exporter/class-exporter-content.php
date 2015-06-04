<?php
namespace Exporter;

/**
 * Represents a generic way to represent content that must be exported. This
 * can be filled based on a WordPress post for example.
 */
class Exporter_Content {

	private $id;
	private $title;
	private $content;
	private $intro;
	private $cover;

	function __construct( $id, $title, $content, $intro = null, $cover = null ) {
		$this->id				= $id;
		$this->title		= $title;
		$this->content	= $content;
		$this->intro		= $intro;
		$this->cover		= $cover;
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

}
