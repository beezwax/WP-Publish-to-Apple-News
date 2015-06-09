<?php

use \Exporter\Components\Intro as Intro;

class Intro_Test extends PHPUnit_Framework_TestCase {

	public function testBuildingRemovesTags() {
		$intro_component = new Intro( 'Test intro text.', null );

		$this->assertEquals(
			array(
				'role' => 'intro',
				'text' => 'Test intro text.',
		 	),
			$intro_component->value()
		);
	}

}

