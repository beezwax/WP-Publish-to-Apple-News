<?php

require_once __DIR__ . '/../../../includes/exporter/components/class-component.php';
require_once __DIR__ . '/../../../includes/exporter/components/class-cover.php';
require_once __DIR__ . '/../../../includes/exporter/class-workspace.php';

use \Exporter\Components\Cover as Cover;

class Cover_Test extends PHPUnit_Framework_TestCase {

	private $prophet;

	protected function setup() {
		$this->prophet = new \Prophecy\Prophet;
	}

	protected function tearDown() {
		$this->prophet->checkPredictions();
	}

	public function testGeneratedJSON() {
		$workspace = $this->prophet->prophesize( '\Exporter\Workspace' );
		// get_file_contents and write_tmp_files must be caleld with the specified
		// params
		$workspace->get_file_contents( 'http://someurl.com/filename.jpg' )->willReturn( 'foo' )->shouldBeCalled();
		$workspace->write_tmp_file( 'filename.jpg', 'foo' )->willReturn( true )->shouldBeCalled();

		$cover_component = new Cover( 'http://someurl.com/filename.jpg', $workspace->reveal() );

		$this->assertEquals(
			array(
				'role' => 'container',
				'layout' => 'headerContainerLayout',
				'style' => array(
					'fill' => array(
						'type' => 'image',
						'URL' => 'bundle://filename.jpg',
						'fillMode' => 'cover',
					),
				),
			),
			$cover_component->value()
		);
	}

}

