<?php

use \Exporter\Components\Body as Body;

class Body_Test extends PHPUnit_Framework_TestCase {

	public function testBuildingRemovesTags() {
		$body_component = new Body( '<p>my text</p>', null );

		$this->assertEquals(
			array(
				'text' => 'my text',
				'role' => 'body',
		 	),
			$body_component->value()
		);
	}

}

