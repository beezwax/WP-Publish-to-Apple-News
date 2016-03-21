<?php

require_once __DIR__ . '/class-component-testcase.php';

use Apple_Exporter\Components\Image as Image;

class Image_Test extends Component_TestCase {

	public function testGeneratedJSON() {
		$workspace = $this->prophet->prophesize( '\Apple_Exporter\Workspace' );
		// get_file_contents and write_tmp_files must be caleld with the specified
		// params
		$workspace->bundle_source( 'filename.jpg', 'http://someurl.com/filename.jpg' )->shouldBeCalled();

		// Pass the mock workspace as a dependency
		$component = new Image( '<img src="http://someurl.com/filename.jpg" alt="Example" />',
			$workspace->reveal(), $this->settings, $this->styles, $this->layouts );

		$result = $component->to_array();
		$this->assertEquals( 'photo', $result['role'] );
		$this->assertEquals( 'bundle://filename.jpg', $result['URL'] );
		$this->assertEquals( 'anchored-image', $result['layout'] );
	}

}

