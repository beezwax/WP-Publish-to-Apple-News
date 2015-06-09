<?php

require_once __DIR__ . '/../../../includes/exporter/components/class-component.php';
require_once __DIR__ . '/../../../includes/exporter/components/class-gallery.php';
require_once __DIR__ . '/../../../includes/exporter/class-workspace.php';

use \Exporter\Components\Gallery as Gallery;

class Gallery_Test extends PHPUnit_Framework_TestCase {

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
		$workspace->get_file_contents( 'http://someurl.com/filename-1.jpg' )->willReturn( 'foo' )->shouldBeCalled();
		$workspace->get_file_contents( 'http://someurl.com/another-filename-2.jpg' )->willReturn( 'foo' )->shouldBeCalled();
		$workspace->write_tmp_file( 'filename-1.jpg', 'foo' )->willReturn( true )->shouldBeCalled();
		$workspace->write_tmp_file( 'another-filename-2.jpg', 'foo' )->willReturn( true )->shouldBeCalled();

		// Pass the mock workspace as a dependency
		$gallery_component = new Gallery( '<div class="gallery"><img
			src="http://someurl.com/filename-1.jpg" alt="Example" /><img
			src="http://someurl.com/another-filename-2.jpg" alt="Example" /></div>',
			$workspace->reveal() );

		// Test for valid JSON
		$this->assertEquals(
			array(
				'role' => 'gallery',
				'items' => array(
					array(
						'URL' => 'bundle://filename-1.jpg',
					),
					array(
						'URL' => 'bundle://another-filename-2.jpg',
					),
				),
			),
			$gallery_component->value()
		);
	}

}

