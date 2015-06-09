<?php

require_once __DIR__ . '/../../../includes/exporter/components/class-component.php';
require_once __DIR__ . '/../../../includes/exporter/components/class-body.php';

use \Exporter\Components\Quote as Quote;

class Quote_Test extends PHPUnit_Framework_TestCase {

	public function testBuildingRemovesTags() {
		$body_component = new Quote( '<blockquote><p>my quote</p></blockquote>', null );

		$this->assertEquals(
			array(
				'role' => 'quote',
				'text' => 'my quote',
		 	),
			$body_component->value()
		);
	}

}

