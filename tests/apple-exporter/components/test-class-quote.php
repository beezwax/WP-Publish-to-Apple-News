<?php

require_once __DIR__ . '/class-component-testcase.php';

use Apple_Exporter\Components\Quote as Quote;

class Quote_Test extends Component_TestCase {

	public function testBuildingRemovesTags() {
		$component = new Quote( '<blockquote><p>my quote</p></blockquote>',
			null, $this->settings, $this->styles, $this->layouts );

		$result = $component->to_array();
		$this->assertEquals( 'quote', $result['role'] );
		$this->assertEquals( "my quote\n\n", $result['text'] );
		$this->assertEquals( 'markdown', $result['format'] );
		$this->assertEquals( 'default-pullquote', $result['textStyle'] );
		$this->assertEquals( 'quote-layout', $result['layout'] );
	}

}

