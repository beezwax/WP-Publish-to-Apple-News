<?php

require_once __DIR__ . '/class-component-testcase.php';

use \Exporter\Components\Divider as Divider;

class Divider_Test extends Component_TestCase {

	public function testBuildingRemovesTags() {
		$component = new Divider( '<hr/>', null, $this->settings,
			$this->styles, $this->layouts );

		$this->assertEquals(
			array(
				'role'   => 'divider',
				'layout' => 'divider-layout',
		 	),
			$component->to_array()
		);
	}

}

