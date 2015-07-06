<?php

require_once __DIR__ . '/class-component-testcase.php';

use \Exporter\Components\Byline as Byline;

class Byline_Test extends Component_TestCase {

	public function testWithoutDropcap() {
		$component = new Byline( 'This is the byline', null, $this->settings,
			$this->styles, $this->layouts );

		$this->assertEquals(
			array(
				'text' => "This is the byline",
				'role' => 'byline',
				'textStyle' => 'default-byline',
				'layout' => 'byline-layout',
		 	),
			$component->to_array()
		);
	}

}

