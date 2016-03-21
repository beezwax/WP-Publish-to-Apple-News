<?php

require_once __DIR__ . '/class-component-testcase.php';

use Apple_Exporter\Components\Gallery as Gallery;

class Gallery_Test extends Component_TestCase {

	public function testGeneratedJSON() {
		$workspace = $this->prophet->prophesize( '\Apple_Exporter\Workspace' );
		// get_file_contents and write_tmp_files must be caleld with the specified
		// params
		$workspace->bundle_source( 'filename-1.jpg', 'http://someurl.com/filename-1.jpg' )->shouldBeCalled();
		$workspace->bundle_source( 'another-filename-2.jpg', 'http://someurl.com/another-filename-2.jpg' )->shouldBeCalled();

		// Pass the mock workspace as a dependency
		$component = new Gallery( '<div class="gallery"><img
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
				'layout' => 'gallery-layout',
			),
			$component->to_array()
		);
	}

}

