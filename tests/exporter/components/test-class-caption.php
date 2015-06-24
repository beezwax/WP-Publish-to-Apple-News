<?php

use \Exporter\Components\Caption as Caption;
use \Exporter\Settings as Settings;
use \Exporter\Component_Layouts as Component_Layouts;
use \Exporter\Component_Styles as Component_Styles;

class Caption_Test extends PHPUnit_Framework_TestCase {

	public function testSingleCaption() {
		$settings  = new Settings();
		$styles    = new Component_Styles();
		$layouts   = new Component_Layouts();
		$component = new Caption( '<figcaption>my text</figcaption>', null, $settings, $styles, $layouts );

		$this->assertEquals(
			array(
				'role' => 'caption',
				'text' => 'my text',
		 	),
			$component->value()
		);
	}

}

