<?php

require_once __DIR__ . '/../../includes/exporter/class-workspace.php';
require_once __DIR__ . '/../../includes/exporter/class-exporter.php';

use \Exporter\Exporter as Exporter;

class BodyTest extends PHPUnit_Framework_TestCase {

	private $prophet;

	protected function setup() {
		$this->prophet = new \Prophecy\Prophet;
	}

	protected function tearDown() {
		$this->prophet->checkPredictions();
	}

	public function testZipsWorkspaceOnExport() {
		$workspace = $this->prophet->prophesize( '\Exporter\Workspace' );
		$workspace->zip( 'content' )->willReturn( true )->shouldBeCalled();

		$exporter = new Exporter( '<p>Test body</p>' );
		$exporter->export();
	}

}

