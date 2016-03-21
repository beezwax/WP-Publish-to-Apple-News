<?php

require_once __DIR__ . '/class-component-testcase.php';

use Apple_Exporter\Components\Embed_Web_Video as Embed_Web_Video;

class Embed_Web_Video_Test extends Component_TestCase {

	public function testYouTubeLink() {
		$component = new Embed_Web_Video( '<p>https://www.youtube.com/watch?v=0qwALOOvUik</p>',
			null, $this->settings, $this->styles, $this->layouts );

		$this->assertEquals(
			array(
				'role' => 'embedwebvideo',
				'URL' => 'https://www.youtube.com/embed/0qwALOOvUik',
				'aspectRatio' => '1.777',
		 	),
			$component->to_array()
		);
	}

	public function testYouTubeShortLink() {
		$component = new Embed_Web_Video( '<p>https://youtu.be/0qwALOOvUik</p>',
			null, $this->settings, $this->styles, $this->layouts );

		$this->assertEquals(
			array(
				'role' => 'embedwebvideo',
				'URL' => 'https://www.youtube.com/embed/0qwALOOvUik',
				'aspectRatio' => '1.777',
		 	),
			$component->to_array()
		);
	}

	public function testVimeo() {
		$component = new Embed_Web_Video( '<p>https://vimeo.com/12819723</p>',
			null, $this->settings, $this->styles, $this->layouts );

		$this->assertEquals(
			array(
				'role' => 'embedwebvideo',
				'URL' => 'https://player.vimeo.com/video/12819723',
				'aspectRatio' => '1.777',
		 	),
			$component->to_array()
		);
	}

}

