<?php

use \Exporter\Components\Heading as Heading;
use \Exporter\Settings as Settings;
use \Exporter\Component_Layouts as Component_Layouts;
use \Exporter\Component_Styles as Component_Styles;

class Heading_Test extends PHPUnit_Framework_TestCase {

	protected function setup() {
		$this->settings = new Settings();
		$this->styles   = new Component_Styles();
		$this->layouts  = new Component_Layouts( $this->settings );
	}

	public function testInvalidInput() {
		$component = new Heading( '<p>This is not a heading</p>', null,
			$this->settings, $this->styles, $this->layouts );

		$this->assertEquals(
			null,
			$component->value()
		);
	}

	public function testValidInput() {
		$component = new Heading( '<h1>This is a heading</h1>', null,
			$this->settings, $this->styles, $this->layouts );

		$this->assertEquals(
			array(
				'role' => 'heading1',
				'text' => 'This is a heading',
				'textStyle' => 'default-heading-1',
		 	),
			$component->value()
		);
	}

}

