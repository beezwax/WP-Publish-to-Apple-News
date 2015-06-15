<?php

use \Exporter\Components\Heading as Heading;

class Heading_Test extends PHPUnit_Framework_TestCase {

	public function testInvalidInput() {
		$heading_component = new Heading( '<p>This is not a heading</p>', null );

		$this->assertEquals(
			null,
			$heading_component->value()
		);
	}

	public function testValidInput() {
		$heading_component = new Heading( '<h1>This is a heading</h1>', null );

		$this->assertEquals(
			array(
				'role' => 'heading1',
				'text' => "# This is a heading\n",
				'format' => 'markdown',
		 	),
			$heading_component->value()
		);
	}

}

