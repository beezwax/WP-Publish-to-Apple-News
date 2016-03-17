<?php

require_once __DIR__ . '/class-component-testcase.php';

use Apple_Exporter\Components\Intro as Intro;

class Intro_Test extends Component_TestCase {

	public function testBuildingRemovesTags() {
		$component = new Intro( 'Test intro text.', null, $this->settings,
			$this->styles, $this->layouts );

		$this->assertEquals(
			array(
				'role' => 'intro',
				'text' => "Test intro text.\n",
				'textStyle' => 'default-intro',
		 	),
			$component->to_array()
		);
	}

}

