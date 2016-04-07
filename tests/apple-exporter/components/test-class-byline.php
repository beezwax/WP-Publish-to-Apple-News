<?php

require_once __DIR__ . '/class-component-testcase.php';

use Apple_Exporter\Components\Byline as Byline;

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

	public function testFilter() {
		$component = new Byline( 'This is the byline', null, $this->settings,
			$this->styles, $this->layouts );

		add_filter( 'apple_news_byline_json', function( $json ) {
			$json['layout'] = 'fancy-layout';
			return $json;
		} );

		$this->assertEquals(
			array(
				'text' => "This is the byline",
				'role' => 'byline',
				'textStyle' => 'default-byline',
				'layout' => 'fancy-layout',
		 	),
			$component->to_array()
		);
	}

}

