<?php

require_once __DIR__ . '/class-component-testcase.php';

use \Exporter\Components\Video as Video;

class Video_Test extends Component_TestCase {

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
			$component->to_array()
		);
	}

}

