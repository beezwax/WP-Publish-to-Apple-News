<?php
/**
 * Publish to Apple News Tests: Metadata_Test class
 *
 * Contains a class which is used to test Apple_Exporter\Builders\Metadata.
 *
 * @package Apple_News
 * @subpackage Tests
 */

use Apple_Exporter\Exporter_Content;
use Apple_Exporter\Settings;
use Apple_Exporter\Builders\Metadata;

/**
 * A class which is used to test the Apple_Exporter\Builders\Metadata class.
 */
class Metadata_Test extends WP_UnitTestCase {

	/**
	 * Actions to be run before each test in this class is executed.
	 *
	 * @access public
	 */
	public function setup() {
		$this->settings = new Settings();
	}

	/**
	 * Ensures that the cover image is properly set in metadata.
	 *
	 * @access public
	 */
	public function testCover() {

		// Setup.
		$this->settings->set( 'use_remote_images', 'no' );
		$content = new Exporter_Content(
			1,
			'My Title',
			'<p>Hello, World!</p>',
			null,
			'/etc/somefile.jpg'
		);
		$builder = new Metadata( $content, $this->settings );
		$result = $builder->to_array();

		// Test.
		$this->assertEquals(
			5,
			count( $result )
		);
		$this->assertEquals(
			'bundle://somefile.jpg',
			$result['thumbnailURL']
		);
	}

	/**
	 * Ensures that a remote cover image is properly set in metadata.
	 *
	 * @access public
	 */
	public function testCoverRemoteImages() {

		// Setup.
		$this->settings->set( 'use_remote_images', 'yes' );
		$content = new Exporter_Content(
			1,
			'My Title',
			'<p>Hello, World!</p>',
			null,
			'http://someurl.com/somefile.jpg'
		);
		$builder = new Metadata( $content, $this->settings );
		$result = $builder->to_array();

		// Test.
		$this->assertEquals(
			5,
			count( $result )
		);
		$this->assertEquals(
			'http://someurl.com/somefile.jpg',
			$result['thumbnailURL']
		);
	}

	/**
	 * Ensure dates are properly set in metadata.
	 *
	 * @access public
	 */
	public function testDates() {

		// Setup.
		$title = 'My Title';
		$content = '<p>Hello, World!</p>';
		$post_id = $this->factory->post->create( array(
			'post_title' => $title,
			'post_content' => $content,
			'post_date' => '2016-04-01 00:00:00',
		) );
		$content = new Exporter_Content(
			$post_id,
			$title,
			$content,
			null,
			'/etc/somefile.jpg'
		);
		$builder = new Metadata( $content, $this->settings );
		$result = $builder->to_array();

		// Test.
		$this->assertEquals(
			8,
			count( $result )
		);
		$this->assertEquals(
			'2016-04-01T00:00:00+00:00',
			$result['dateCreated']
		);
		$this->assertEquals(
			'2016-04-01T00:00:00+00:00',
			$result['dateModified']
		);
		$this->assertEquals(
			'2016-04-01T00:00:00+00:00',
			$result['datePublished']
		);
	}

	/**
	 * Ensures that the intro text is properly set in metadata.
	 *
	 * @access public
	 */
	public function testIntro() {

		// Setup.
		$content = new Exporter_Content(
			1,
			'My Title',
			'<p>Hello, World!</p>',
			'This is an intro.'
		);
		$builder = new Metadata( $content, $this->settings );
		$result = $builder->to_array();

		// Test.
		$this->assertEquals(
			5,
			count( $result )
		);
		$this->assertEquals(
			'This is an intro.',
			$result['excerpt']
		);
	}

	/**
	 * Ensures that the cover image and intro text are properly set in metadata.
	 *
	 * @access public
	 */
	public function testIntroAndCover() {

		// Setup.
		$this->settings->set( 'use_remote_images', 'no' );
		$content = new Exporter_Content(
			1,
			'My Title',
			'<p>Hello, World!</p>',
			'This is an intro.',
			'/etc/somefile.jpg'
		);
		$builder = new Metadata( $content, $this->settings );
		$result = $builder->to_array();

		// Test.
		$this->assertEquals(
			6,
			count( $result )
		);
		$this->assertEquals(
			'This is an intro.',
			$result['excerpt']
		);
		$this->assertEquals(
			'bundle://somefile.jpg',
			$result['thumbnailURL']
		);
	}

	/**
	 * Ensures that a remote cover image and intro text are properly set in metadata.
	 *
	 * @access public
	 */
	public function testIntroAndCoverRemoteImages() {

		// Setup.
		$this->settings->set( 'use_remote_images', 'yes' );
		$content = new Exporter_Content(
			1,
			'My Title',
			'<p>Hello, World!</p>',
			'This is an intro.',
			'http://someurl.com/somefile.jpg'
		);
		$builder = new Metadata( $content, $this->settings );
		$result = $builder->to_array();

		// Test.
		$this->assertEquals(
			6,
			count( $result )
		);
		$this->assertEquals(
			'This is an intro.',
			$result['excerpt']
		);
		$this->assertEquals(
			'http://someurl.com/somefile.jpg',
			$result['thumbnailURL']
		);
	}

	/**
	 * Ensures metadata is properly generated when no intro and no cover are given.
	 *
	 * @access public
	 */
	public function testNoIntroNoCover() {

		// Setup.
		$content = new Exporter_Content( 1, 'My Title', '<p>Hello, World!</p>' );
		$builder = new Metadata( $content, $this->settings );
		$result = $builder->to_array();

		// Test.
		$this->assertEquals(
			4,
			count( $result )
		);
	}

	/**
	 * Ensures video metadata is properly added.
	 *
	 * @access public
	 */
	public function testVideo() {

		// Setup.
		$this->settings->set( 'use_remote_images', 'yes' );
		$html = <<<HTML
<video class="wp-video-shortcode" id="video-71-1" width="525" height="295" poster="https://example.com/wp-content/uploads/2017/02/ExamplePoster.jpg" preload="metadata" controls="controls">
	<source type="video/mp4" src="https://example.com/wp-content/uploads/2017/02/example-video.mp4?_=1" />
	<a href="https://example.com/wp-content/uploads/2017/02/example-video.mp4">https://example.com/wp-content/uploads/2017/02/example-video.mp4</a>
</video>
HTML;
		$content = new Exporter_Content( 1, 'My Title', $html );
		$builder = new Metadata( $content, $this->settings );
		$result = $builder->to_array();

		// Test.
		$this->assertEquals(
			'https://example.com/wp-content/uploads/2017/02/ExamplePoster.jpg',
			$result['thumbnailURL']
		);
		$this->assertEquals(
			'https://example.com/wp-content/uploads/2017/02/example-video.mp4?_=1',
			$result['videoURL']
		);
	}
}
