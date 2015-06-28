<?php

use \Exporter\Components\Gallery as Gallery;
use \Exporter\Settings as Settings;
use \Exporter\Component_Layouts as Component_Layouts;
use \Exporter\Component_Styles as Component_Styles;

class Gallery_Test extends PHPUnit_Framework_TestCase {

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
		$workspace->get_file_contents( 'http://someurl.com/filename-1.jpg' )->willReturn( 'foo' )->shouldBeCalled();
		$workspace->get_file_contents( 'http://someurl.com/another-filename-2.jpg' )->willReturn( 'foo' )->shouldBeCalled();
		$workspace->write_tmp_file( 'filename-1.jpg', 'foo' )->willReturn( true )->shouldBeCalled();
		$workspace->write_tmp_file( 'another-filename-2.jpg', 'foo' )->willReturn( true )->shouldBeCalled();

		// Pass the mock workspace as a dependency
		$gallery_component = new Gallery( '<div class="gallery"><img
			src="http://someurl.com/filename-1.jpg" alt="Example" /><img
			src="http://someurl.com/another-filename-2.jpg" alt="Example" /></div>',
			$workspace->reveal(), $this->settings, $this->styles, $this->layouts );

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

