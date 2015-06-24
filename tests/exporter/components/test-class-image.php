<?php

use \Exporter\Components\Image as Image;
use \Exporter\Settings as Settings;
use \Exporter\Component_Layouts as Component_Layouts;
use \Exporter\Component_Styles as Component_Styles;

class Image_Test extends PHPUnit_Framework_TestCase {

	private $prophet;

	protected function setup() {
		$this->prophet = new \Prophecy\Prophet;
		$this->settings = new Settings();
		$this->styles   = new Component_Styles();
		$this->layouts  = new Component_Layouts();
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
		$image_component = new Image( '<img src="http://someurl.com/filename.jpg" alt="Example" />',
			$workspace->reveal(), $this->settings, $this->styles, $this->layouts );

		// Test for valid JSON
		$this->assertEquals(
			array( 'role' => 'photo', 'URL' => 'bundle://filename.jpg' ),
			$image_component->value()
		);
	}

}

