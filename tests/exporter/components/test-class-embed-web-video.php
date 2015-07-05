<?php

use \Exporter\Components\Embed_Web_Video as Embed_Web_Video;
use \Exporter\Settings as Settings;
use \Exporter\Component_Layouts as Component_Layouts;
use \Exporter\Component_Styles as Component_Styles;

class Embed_Web_Video_Test extends PHPUnit_Framework_TestCase {

	protected function setup() {
		$this->settings = new Settings();
		$this->styles   = new Component_Styles();
		$this->layouts  = new Component_Layouts( $this->settings );
	}

	public function testBuildingRemovesTags() {
		$component = new Embed_Web_Video( '<iframe width="560" height="315" src="https://exampleurl.com" frameborder="0" allowfullscreen></iframe>',
			null, $this->settings, $this->styles, $this->layouts );

		$this->assertEquals(
			array(
				'role' => 'embedwebvideo',
				'URL' => 'https://exampleurl.com',
				'aspectRatio' => '1.777',
				'caption' => 'test',
				'accessibilityCaption' => 'test',
		 	),
			$component->to_array()
		);
	}

}

