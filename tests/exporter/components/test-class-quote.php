<?php

use \Exporter\Components\Quote as Quote;

class Quote_Test extends PHPUnit_Framework_TestCase {

	public function testBuildingRemovesTags() {
		$quote_component = new Quote( '<blockquote><p>my quote</p></blockquote>', null );

		$this->assertEquals(
			array(
				'role' => 'quote',
				'text' => 'my quote',
		 	),
			$quote_component->value()
		);
	}

}

