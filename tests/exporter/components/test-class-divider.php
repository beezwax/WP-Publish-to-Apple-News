<?php

use \Exporter\Components\Divider as Divider;

class Divider_Test extends PHPUnit_Framework_TestCase {

	public function testBuildingRemovesTags() {
		$body_component = new Divider( '<hr/>', null );

		$this->assertEquals(
			array(
				'role' => 'divider',
		 	),
			$body_component->value()
		);
	}

}

