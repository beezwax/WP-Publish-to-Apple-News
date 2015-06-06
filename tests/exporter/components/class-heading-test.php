<?php

require_once __DIR__ . '/../../../includes/exporter/components/class-component.php';
require_once __DIR__ . '/../../../includes/exporter/components/class-heading.php';

use \Exporter\Components\Heading as Heading;

class HeadingTest extends PHPUnit_Framework_TestCase {

	public function testInvalidInput() {
		$heading_component = new Heading( '<p>This is not a heading</p>', null );

		// Test for valid JSON
		$this->assertEquals(
			null,
			$heading_component->value()
		);
	}

	public function testValidInput() {
		$heading_component = new Heading( '<h1>This is a heading</h1>', null );

		// Test for valid JSON
		$this->assertEquals(
			array(
				'role' => 'heading1',
				'text' => 'This is a heading',
				'textStyle' => 'title',
		 	),
			$heading_component->value()
		);
	}

}

