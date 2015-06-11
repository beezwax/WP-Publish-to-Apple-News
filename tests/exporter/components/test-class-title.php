<?php

use \Exporter\Components\Title as Title;

class Title_Test extends PHPUnit_Framework_TestCase {

	public function testBuildingRemovesTags() {
		$body_component = new Title( 'Example Title', null );

		$this->assertEquals(
			array(
				'role' => 'title',
				'text' => 'Example Title',
		 	),
			$body_component->value()
		);
	}

}

