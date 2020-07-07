<?php

require_once plugin_dir_path( __FILE__ ) . '../../mocks/class-mock-audio.php';
require_once __DIR__ . '/class-component-testcase.php';

use Apple_Exporter\Components\MockAudio as MockAudio;

class Audio_Test extends Component_TestCase {

	public function testGeneratedJSON() {
		$workspace = $this->prophet->prophesize( '\Exporter\Workspace' );

		// Pass the mock workspace as a dependency
		$component = new MockAudio( '<audio><source src="http://someurl.com/audio-file.mp3?some_query=string"></audio>',
			$workspace->reveal(), $this->settings, $this->styles, $this->layouts );

		$json = $component->to_array();
		$this->assertEquals( 'audio', $json['role'] );
		$this->assertEquals( 'http://someurl.com/audio-file.mp3?some_query=string', $json['URL'] );
	}

	/**
	 * Tests HTML formatting with captions.
	 *
	 * @access public
	 */
	public function testCaption() {
		$workspace = $this->prophet->prophesize( '\Exporter\Workspace' );

		// Pass the mock workspace as a dependency
		$component = new MockAudio( '<figure class="wp-block-audio"><audio controls="" src="https://www.someurl.com/Song-1.mp3"/><figcaption>caption</figcaption></figure>',
			$workspace->reveal(), $this->settings, $this->styles, $this->layouts );

		// Test.
		$this->assertEquals(
			array(
				'role' => 'container',
				'components' => array(
					array(
						'role' => 'audio',
						'URL' => 'https://www.someurl.com/Song-1.mp3',
					),
					array(
						'role' => 'caption',
						'text' => 'caption',
						'format' => 'html',
					)
				)
			),
			$component->to_array()
		);
	}

	public function testFilter() {
		$workspace = $this->prophet->prophesize( '\Exporter\Workspace' );

		// Pass the mock workspace as a dependency
		$component = new MockAudio( '<audio><source src="http://someurl.com/audio-file.mp3?some_query=string"></audio>',
			$workspace->reveal(), $this->settings, $this->styles, $this->layouts );

		add_filter( 'apple_news_audio_json', function( $json ) {
			$json['URL'] = 'http://someurl.com/audio-file.mp3?some_query=string';
			return $json;
		} );

		$json = $component->to_array();
		$this->assertEquals( 'audio', $json['role'] );
		$this->assertEquals( 'http://someurl.com/audio-file.mp3?some_query=string', $json['URL'] );
	}

}

