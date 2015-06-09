<?php

use \Exporter\Components\Image as Image;

class Image_Test extends PHPUnit_Framework_TestCase {

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

		// Pass the mock workspace as a dependency
		$image_component = new Image( '<img src="http://someurl.com/filename.jpg" alt="Example" />', $workspace->reveal() );

		// Test for valid JSON
		$this->assertEquals(
			array( 'role' => 'photo', 'URL' => 'bundle://filename.jpg' ),
			$image_component->value()
		);
	}

}

