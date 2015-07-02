<?php

use \Exporter\Components\Title as Title;
use \Exporter\Settings as Settings;
use \Exporter\Component_Layouts as Component_Layouts;
use \Exporter\Component_Styles as Component_Styles;

class Title_Test extends PHPUnit_Framework_TestCase {

	public function setup() {
		$this->settings = new Settings();
		$this->styles   = new Component_Styles();
		$this->layouts  = new Component_Layouts( $this->settings );
	}

	public function testBuildingRemovesTags() {
		$body_component = new Title( 'Example Title', null, $this->settings,
			$this->styles, $this->layouts );

		$this->assertEquals(
			array(
				'role' => 'title',
				'text' => 'Example Title',
				'textStyle' => 'default-title',
		 	),
			$body_component->value()
		);
	}

}

