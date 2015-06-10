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

	/**
	 * Given a source (either a file or an URL) gets the contents and writes
	 * them into a file.
	 */
	protected function bundle_source( $filename, $source ) {
		$content = $this->workspace->get_file_contents( $source );
		$this->workspace->write_tmp_file( $filename, $contents );
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
