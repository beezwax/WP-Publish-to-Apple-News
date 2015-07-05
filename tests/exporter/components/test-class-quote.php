<?php

require_once __DIR__ . '/class-component-testcase.php';

use \Exporter\Components\Quote as Quote;

class Quote_Test extends Component_TestCase {

	public function testBuildingRemovesTags() {
		$component = new Quote( '<blockquote><p>my quote</p></blockquote>',
			null, $this->settings, $this->styles, $this->layouts );

		$this->assertEquals(
			array(
				'role' => 'quote',
				'text' => "my quote\n\n",
				'textStyle' => 'default-pullquote',
				'format' => 'markdown',
		 	),
			$component->to_array()
		);
	}

}

