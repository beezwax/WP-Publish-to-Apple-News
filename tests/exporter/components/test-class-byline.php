<?php

use \Exporter\Components\Byline as Byline;
use \Exporter\Settings as Settings;
use \Exporter\Component_Layouts as Component_Layouts;
use \Exporter\Component_Styles as Component_Styles;

class Byline_Test extends PHPUnit_Framework_TestCase {

	public function setup() {
		$this->settings = new Settings();
		$this->styles   = new Component_Styles();
		$this->layouts  = new Component_Layouts( $this->settings );
	}

	public function testWithoutDropcap() {
		$body_component = new Byline( 'This is the byline', null, $this->settings,
			$this->styles, $this->layouts );

		$this->assertEquals(
			array(
				'text' => "This is the byline",
				'role' => 'byline',
				'textStyle' => 'default-byline',
				'layout' => 'byline-layout',
		 	),
			$body_component->value()
		);
	}

}

