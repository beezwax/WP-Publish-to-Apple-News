<?php

require_once __DIR__ . '/class-component-testcase.php';

use \Exporter\Components\Cover as Cover;

class Cover_Test extends Component_TestCase {

	public function testGeneratedJSON() {
		$workspace = $this->prophet->prophesize( '\Exporter\Workspace' );
		// get_file_contents and write_tmp_files must be caleld with the specified
		// params
		$workspace->get_file_contents( 'http://someurl.com/filename.jpg' )->willReturn( 'foo' )->shouldBeCalled();
		$workspace->write_tmp_file( 'filename.jpg', 'foo' )->willReturn( true )->shouldBeCalled();

		$component = new Cover( 'http://someurl.com/filename.jpg',
			$workspace->reveal(), $this->settings, $this->styles, $this->layouts );

		$this->assertEquals(
			array(
				'role' => 'container',
				'layout' => 'headerContainerLayout',
				'style' => array(
					'fill' => array(
						'type' => 'image',
						'URL' => 'bundle://filename.jpg',
						'fillMode' => 'cover',
					),
				),
				'behavior' => array(
					'type' => 'background_parallax',
				),
			),
			$component->to_array()
		);
	}

}

