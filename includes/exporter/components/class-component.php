<?php
namespace Exporter\Components;

abstract class Component {

	protected $workspace;
	protected $json;

	function __construct( $text, $workspace ) {
		$this->workspace = $workspace;
		$this->build( $text );
	}

	public function value() {
		return $this->json;
	}

	abstract protected function build( $text );

}
