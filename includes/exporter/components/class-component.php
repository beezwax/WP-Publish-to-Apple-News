<?php
namespace Exporter\Components;

abstract class Component {

	protected $workspace;
	protected $text;
	protected $json = null;

	function __construct( $text, $workspace ) {
		$this->text = $text;
		$this->workspace = $workspace;
	}

	protected function write_to_workspace( $filename, $contents ) {
		$this->workspace->write_tmp_file( $filename, $contents );
	}

	protected function get_file_contents( $url ) {
		return $this->workspace->get_file_contents( $url );
	}

	public function value() {
		// Lazy value evaluation
		if ( is_null( $this->json ) ) {
			$this->build( $this->text );
		}

		return $this->json;
	}

	abstract protected function build( $text );

}
