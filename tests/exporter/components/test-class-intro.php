<?php

use \Exporter\Components\Intro as Intro;
use \Exporter\Settings as Settings;
use \Exporter\Component_Layouts as Component_Layouts;
use \Exporter\Component_Styles as Component_Styles;

class Intro_Test extends PHPUnit_Framework_TestCase {

	protected function setup() {
		$this->settings = new Settings();
		$this->styles   = new Component_Styles();
		$this->layouts  = new Component_Layouts( $this->settings );
	}

	public function testBuildingRemovesTags() {
		$component = new Intro( 'Test intro text.', null, $this->settings,
			$this->styles, $this->layouts );

		$this->assertEquals(
			array(
				'role' => 'intro',
				'text' => "Test intro text.\n",
				'textStyle' => 'default-intro',
		 	),
			$component->to_array()
		);
	}

}

