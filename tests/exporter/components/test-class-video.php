<?php

require_once __DIR__ . '/class-component-testcase.php';

use \Exporter\Components\Video as Video;

class Video_Test extends Component_TestCase {

	public function testGeneratedJSON() {
		$workspace = $this->prophet->prophesize( '\Exporter\Workspace' );

		// Pass the mock workspace as a dependency
		$component = new Video( '<video><source src="http://someurl.com/video-file.mp4?some_query=string"></video>',
			$workspace->reveal(), $this->settings, $this->styles, $this->layouts );

		// Test for valid JSON
		$this->assertEquals(
			array( 'role' => 'video', 'URL' => 'http://someurl.com/video-file.mp4?some_query=string' ),
			$component->to_array()
		);
	}

}

