<?php

use \Exporter\Components\Embed_Web_Video as Embed_Web_Video;

class Embed_Web_Video_Test extends PHPUnit_Framework_TestCase {

	public function testBuildingRemovesTags() {
		$body_component = new Embed_Web_Video( '<iframe width="560" height="315" src="https://exampleurl.com" frameborder="0" allowfullscreen></iframe>', null );

		$this->assertEquals(
			array(
				'role' => 'embedwebvideo',
				'URL' => 'https://exampleurl.com',
				'aspectRatio' => '1.777',
				'caption' => 'test',
				'accessibilityCaption' => 'test',
		 	),
			$body_component->value()
		);
	}

}

