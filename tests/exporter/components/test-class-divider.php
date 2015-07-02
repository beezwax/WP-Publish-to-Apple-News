<?php

use \Exporter\Components\Divider as Divider;
use \Exporter\Settings as Settings;
use \Exporter\Component_Layouts as Component_Layouts;
use \Exporter\Component_Styles as Component_Styles;

class Divider_Test extends PHPUnit_Framework_TestCase {

	protected function setup() {
		$this->settings = new Settings();
		$this->styles   = new Component_Styles();
		$this->layouts  = new Component_Layouts( $this->settings );
	}

	public function testBuildingRemovesTags() {
		$component = new Divider( '<hr/>', null, $this->settings,
			$this->styles, $this->layouts );

		$this->assertEquals(
			array(
				'role'   => 'divider',
				'layout' => 'divider-layout',
		 	),
			$component->to_array()
		);
	}

}

