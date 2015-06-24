<?php

use \Exporter\Components\Quote as Quote;
use \Exporter\Settings as Settings;
use \Exporter\Component_Layouts as Component_Layouts;
use \Exporter\Component_Styles as Component_Styles;

class Quote_Test extends PHPUnit_Framework_TestCase {

	public function setup() {
		$this->settings = new Settings();
		$this->styles   = new Component_Styles();
		$this->layouts  = new Component_Layouts();
	}

	public function testBuildingRemovesTags() {
		$component = new Quote( '<blockquote><p>my quote</p></blockquote>',
			null, $this->settings, $this->styles, $this->layouts );

		$this->assertEquals(
			array(
				'role' => 'quote',
				'text' => 'my quote',
				'textStyle' => 'default-pullquote',
		 	),
			$component->value()
		);
	}

}

