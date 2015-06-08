<?php

require_once __DIR__ . '/../../includes/exporter/class-workspace.php';
require_once __DIR__ . '/../../includes/exporter/class-exporter.php';

use \Exporter\Exporter as Exporter;

class Exporter_Test extends WP_UnitTestCase {

	private $prophet;

	public function setup() {
		$this->prophet = new \Prophecy\Prophet;
	}

	public function tearDown() {
		$this->prophet->checkPredictions();
	}

	public function testExport() {
		$workspace = $this->prophet->prophesize( '\Exporter\Workspace' );
		// Creates the article.json file
		$workspace
			->write_tmp_file( 'article.json', \Prophecy\Argument::type( 'string' ) )
			->willReturn( true )
			->shouldBeCalled();
		// Creates a zipfile with the id
		$workspace
			->zip( 'article-3.zip' )
			->willReturn( true )
			->shouldBeCalled();

		$content  = new \Exporter\Exporter_Content( 3, 'Title', '<p>Example content</p>' );
		$exporter = new Exporter( $content, $workspace->reveal() );
		$exporter->export();
	}

}

