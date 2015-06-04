<?php

require_once __DIR__ . '/../../../includes/exporter/components/class-component.php';
require_once __DIR__ . '/../../../includes/exporter/components/class-body.php';

use \Exporter\Components\Body as Body;

class BodyTest extends PHPUnit_Framework_TestCase {

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

