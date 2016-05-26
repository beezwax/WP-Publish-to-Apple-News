<?php

require_once __DIR__ . '/class-component-testcase.php';

use Apple_Exporter\Components\Quote as Quote;

class Quote_Test extends Component_TestCase {

	public function testBuildingRemovesTags() {
		$component = new Quote( '<blockquote><p>my quote</p></blockquote>',
			null, $this->settings, $this->styles, $this->layouts );

		$result_wrapper = $component->to_array();
		$result = $result_wrapper['components'][0];
		$this->assertEquals( 'container', $result_wrapper['role'] );
		$this->assertEquals( 'quote', $result['role'] );
		$this->assertEquals( "my quote\n\n", $result['text'] );
		$this->assertEquals( 'markdown', $result['format'] );
		$this->assertEquals( 'default-pullquote', $result['textStyle'] );
		$this->assertEquals( 'quote-layout', $result['layout'] );
	}

	public function testFilter() {
		$component = new Quote( '<blockquote><p>my quote</p></blockquote>',
			null, $this->settings, $this->styles, $this->layouts );

		add_filter( 'apple_news_quote_json', function( $json ) {
			$json['textStyle'] = 'fancy-quote';
			return $json;
		} );

		$result = $component->to_array();
		$this->assertEquals( 'fancy-quote', $result['textStyle'] );
	}

}

