<?php

require_once __DIR__ . '/class-component-testcase.php';

use \Exporter\Components\Heading as Heading;

class Heading_Test extends Component_TestCase {

	public function testInvalidInput() {
		$component = new Heading( '<p>This is not a heading</p>', null,
			$this->settings, $this->styles, $this->layouts );

		$this->assertEquals(
			null,
			$component->to_array()
		);
	}

	public function testValidInput() {
		$component = new Heading( '<h1>This is a heading</h1>', null,
			$this->settings, $this->styles, $this->layouts );
		$json = $component->to_array();

		$this->assertEquals( 'heading1', $json['role'] );
		$this->assertEquals( 'This is a heading', $json['text'] );
		$this->assertEquals( 'markdown', $json['format'] );
	}

}

