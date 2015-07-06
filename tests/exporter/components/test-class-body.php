<?php

require_once __DIR__ . '/class-component-testcase.php';

use \Exporter\Components\Body as Body;

class Body_Test extends Component_TestCase {

	public function testBuildingRemovesTags() {
		$body_component = new Body( '<p>my text</p>', null, $this->settings,
			$this->styles, $this->layouts );

		$this->assertEquals(
			array(
				'text' => "my text\n\n",
				'role' => 'body',
				'format' => 'markdown',
				'textStyle' => 'dropcapBodyStyle',
				'layout' => 'body-layout',
		 	),
			$body_component->to_array()
		);
	}

	public function testWithoutDropcap() {
		$this->settings->set( 'initial_dropcap', 'no' );
		$body_component = new Body( '<p>my text</p>', null, $this->settings,
			$this->styles, $this->layouts );

		$this->assertEquals(
			array(
				'text' => "my text\n\n",
				'role' => 'body',
				'format' => 'markdown',
				'textStyle' => 'default-body',
				'layout' => 'body-layout',
		 	),
			$body_component->to_array()
		);
	}

}

