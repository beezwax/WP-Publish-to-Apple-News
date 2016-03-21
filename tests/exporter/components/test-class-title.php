<?php

require_once __DIR__ . '/class-component-testcase.php';

use \Exporter\Components\Title as Title;

class Title_Test extends Component_TestCase {

	public function testBuildingRemovesTags() {
		$body_component = new Title( 'Example Title', null, $this->settings,
			$this->styles, $this->layouts );

		$this->assertEquals(
			array(
				'role' => 'title',
				'text' => 'Example Title',
				'textStyle' => 'default-title',
		 	),
			$body_component->to_array()
		);
	}

}

