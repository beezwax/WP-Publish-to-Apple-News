<?php

require_once __DIR__ . '/class-component-testcase.php';

use Apple_Exporter\Components\Embed_Web_Video as Embed_Web_Video;

class Embed_Web_Video_Test extends Component_TestCase {

	public function testHTTPYouTubeWatchLink() {
		$component = new Embed_Web_Video( '<p>http://youtube.com/watch?v=0qwALOOvUik</p>',
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

	public function testHTTPSYouTubeWatchLink() {
		$component = new Embed_Web_Video( '<p>https://youtube.com/watch?v=0qwALOOvUik</p>',
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

	public function testHTTPWWWYouTubeWatchLink() {
		$component = new Embed_Web_Video( '<p>http://www.youtube.com/watch?v=0qwALOOvUik</p>',
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

	public function testHTTPSWWWYouTubeWatchLink() {
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

	public function testHTTPYouTubeEmbedLink() {
		$component = new Embed_Web_Video( '<p>http://youtube.com/embed/0qwALOOvUik</p>',
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

	public function testHTTPSYouTubeEmbedLink() {
		$component = new Embed_Web_Video( '<p>https://youtube.com/embed/0qwALOOvUik</p>',
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

	public function testHTTPWWWYouTubeEmbedLink() {
		$component = new Embed_Web_Video( '<p>http://www.youtube.com/embed/0qwALOOvUik</p>',
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

	public function testHTTPSWWWYouTubeEmbedLink() {
		$component = new Embed_Web_Video( '<p>https://www.youtube.com/embed/0qwALOOvUik</p>',
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

	public function testHTTPYouTubeShortLink() {
		$component = new Embed_Web_Video( '<p>http://youtu.be/0qwALOOvUik</p>',
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

	public function testHTTPSYouTubeShortLink() {
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

	public function testFilter() {
		$component = new Embed_Web_Video( '<p>https://vimeo.com/12819723</p>',
			null, $this->settings, $this->styles, $this->layouts );

		add_filter( 'apple_news_embed_web_video_json', function( $json ) {
			$json['aspectRatio'] = '1.4';
			return $json;
		} );

		$this->assertEquals(
			array(
				'role' => 'embedwebvideo',
				'URL' => 'https://player.vimeo.com/video/12819723',
				'aspectRatio' => '1.4',
		 	),
			$component->to_array()
		);
	}

}

