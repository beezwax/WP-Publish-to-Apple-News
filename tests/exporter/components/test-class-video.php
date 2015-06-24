<?php

use \Exporter\Components\Video as Video;
use \Exporter\Settings as Settings;
use \Exporter\Component_Layouts as Component_Layouts;
use \Exporter\Component_Styles as Component_Styles;

class Video_Test extends PHPUnit_Framework_TestCase {

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
		$workspace->get_file_contents( 'http://someurl.com/video-file.mp4?some_query=string' )->willReturn( 'foo' )->shouldBeCalled();
		$workspace->write_tmp_file( 'video-file.mp4', 'foo' )->willReturn( true )->shouldBeCalled();

		// Pass the mock workspace as a dependency
		$component = new Video( '<video><source src="http://someurl.com/video-file.mp4?some_query=string"></video>',
			$workspace->reveal(), $this->settings, $this->styles, $this->layouts );

		// Test for valid JSON
		$this->assertEquals(
			array( 'role' => 'video', 'URL' => 'bundle://video-file.mp4' ),
			$component->value()
		);
	}

}

