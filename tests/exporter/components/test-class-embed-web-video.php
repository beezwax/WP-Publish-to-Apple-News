<?php

require_once __DIR__ . '/class-component-testcase.php';

use \Exporter\Components\Embed_Web_Video as Embed_Web_Video;

class Embed_Web_Video_Test extends Component_TestCase {

	public function testBuildingRemovesTags() {
		$component = new Embed_Web_Video( '<iframe width="560" height="315" src="https://exampleurl.com" frameborder="0" allowfullscreen></iframe>',
			null, $this->settings, $this->styles, $this->layouts );

		$this->assertEquals(
			array(
				'role' => 'embedwebvideo',
				'URL' => 'https://exampleurl.com',
				'aspectRatio' => '1.777',
		 	),
			$component->to_array()
		);
	}

}

