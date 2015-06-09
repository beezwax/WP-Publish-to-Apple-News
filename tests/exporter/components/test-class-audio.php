<?php

use \Exporter\Components\Audio as Audio;

class Audio_Test extends PHPUnit_Framework_TestCase {

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
		$workspace->get_file_contents( 'http://someurl.com/audio-file.mp3?some_query=string' )->willReturn( 'foo' )->shouldBeCalled();
		$workspace->write_tmp_file( 'audio-file.mp3', 'foo' )->willReturn( true )->shouldBeCalled();

		// Pass the mock workspace as a dependency
		$audio_component = new Audio( '<audio><source src="http://someurl.com/audio-file.mp3?some_query=string"></audio>', $workspace->reveal() );

		// Test for valid JSON
		$this->assertEquals(
			array( 'role' => 'audio', 'URL' => 'bundle://audio-file.mp3' ),
			$audio_component->value()
		);
	}

}

