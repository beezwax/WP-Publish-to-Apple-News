<?php
/**
 * Publish to Apple News tests: Audio_Test class
 *
 * @package Apple_News
 * @subpackage Tests
 */

use Apple_Exporter\Components\Audio;

/**
 * A class to test the behavior of the
 * Apple_Exporter\Components\Audio class.
 *
 * @package Apple_News
 * @subpackage Tests
 */
class Audio_Test extends Component_TestCase {

	/**
	 * Tests basic JSON generation.
	 */
	public function testGeneratedJSON() {
		$component = new Audio(
			'<audio><source src="http://someurl.com/audio-file.mp3?some_query=string"></audio>',
			$this->workspace,
			$this->settings,
			$this->styles,
			$this->layouts
		);

		$json = $component->to_array();
		$this->assertEquals( 'audio', $json['role'] );
		$this->assertEquals( 'http://someurl.com/audio-file.mp3?some_query=string', $json['URL'] );
	}

	/**
	 * Tests HTML formatting with captions.
	 */
	public function testCaption() {
		$component = new Audio(
			'<figure class="wp-block-audio"><audio controls="" src="https://www.someurl.com/Song-1.mp3"/><figcaption>caption</figcaption></figure>',
			$this->workspace,
			$this->settings,
			$this->styles,
			$this->layouts
		);

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

	/**
	 * Tests the behavior of the apple_news_audio_json filter.
	 */
	public function testFilter() {
		$component = new Audio(
			'<audio><source src="http://someurl.com/audio-file.mp3?some_query=string"></audio>',
			$this->workspace,
			$this->settings,
			$this->styles,
			$this->layouts
		);

		add_filter(
			'apple_news_audio_json',
			function( $json ) {
				$json['URL'] = 'http://someurl.com/audio-file.mp3?some_query=string';
				return $json;
			}
		);

		$json = $component->to_array();
		$this->assertEquals( 'audio', $json['role'] );
		$this->assertEquals( 'http://someurl.com/audio-file.mp3?some_query=string', $json['URL'] );
	}
}
