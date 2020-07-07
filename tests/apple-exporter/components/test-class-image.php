<?php
/**
 * Publish to Apple News Tests: Image_Test class
 *
 * Contains a class which is used to test Apple_Exporter\Components\Image.
 *
 * @package Apple_News
 * @subpackage Tests
 */

require_once __DIR__ . '/class-component-testcase.php';

use Apple_Exporter\Components\Image;
use Apple_Exporter\Exporter;
use Apple_Exporter\Exporter_Content;
use Apple_Exporter\Workspace;

/**
 * A class which is used to test the Apple_Exporter\Components\Image class.
 */
class Image_Test extends Component_TestCase {

	/**
	 * A filter function to modify the text style in the generated JSON.
	 *
	 * @param array $json The JSON array to modify.
	 *
	 * @access public
	 * @return array The modified JSON.
	 */
	public function filter_apple_news_image_json( $json ) {
		$json['layout'] = 'default-image';

		return $json;
	}

	/**
	 * Test Image component matching and JSON
	 * output with HTML markup for an image.
	 *
	 * @access public
	 */
	public function testTransformImage() {
		$this->settings->set( 'html_support', 'yes' );
		$this->settings->set( 'use_remote_images', 'yes' );

		$html = '<img src="https://placeimg.com/640/480/any" alt="Example" align="left" />';

		$component = new Image(
			$html,
			new Workspace( 1 ),
			$this->settings,
			$this->styles,
			$this->layouts
		);

		// Build the node.
		$node = self::build_node( $html );

		// Test the match is not `null` and that it matches the original node.
		$this->assertNotNull( $component->node_matches( $node ) );
		$this->assertEquals(
			$component->node_matches( $node ),
			$node
		);

		// Get the JSON.
		$json = $component->to_array();

		// Test the JSON.
		$this->assertEquals( 'photo', $json['role'] );
		$this->assertEquals( 'https://placeimg.com/640/480/any', $json['URL'] );
	}

	/**
	 * Test Image component matching and JSON output
	 * with HTML5 markup for an image with a caption.
	 *
	 * @access public
	 */
	public function testTransformImageCaption() {
		$this->settings->set( 'html_support', 'yes' );
		$this->settings->set( 'use_remote_images', 'yes' );

		$html_caption = <<<HTML
	<figure class="wp-caption">
		<img src="https://placeimg.com/640/480/any" alt="Example" align="left" />
		<figcaption class="wp-caption-text">
			Sed <strong>ac metus</strong> sagittis <em>urna feugiat</em> interdum. Duis vel blandit nisi, id tempus sem. Credit: <a href="https://domain.suffix">Domain</a>
		</figcaption>
	</figure>
HTML;

		// Assign an Image component.
		$component = new Image(
			$html_caption,
			new Workspace( 1 ),
			$this->settings,
			$this->styles,
			$this->layouts
		);

		// Build the node.
		$node = self::build_node( $html_caption );

		// Test the match is not `null` and that it matches the original node.
		$this->assertNotNull( $component->node_matches( $node ) );
		$this->assertEquals(
			$component->node_matches( $node ),
			$node
		);

		// Get the JSON.
		$json = $component->to_array();

		// Test the JSON.
		$this->assertEquals( 'container', $json['role'] );
		$this->assertContains( $json['components'], $json );
		$this->assertEquals( 'photo', $json['components'][0]['role'] );
		$this->assertEquals( 'https://placeimg.com/640/480/any', $json['components'][0]['URL'] );
		$this->assertEquals( 'Sed <strong>ac metus</strong> sagittis <em>urna feugiat</em> interdum. Duis vel blandit nisi, id tempus sem. Credit: <a href="https://domain.suffix">Domain</a>', $json['components'][0]['caption'] );
	}

	/**
	 * Test empty src attribute.
	 *
	 * @access public
	 */
	public function testEmptySrc() {

		// Setup.
		$this->settings->set( 'use_remote_images', 'yes' );
		$workspace = new \Apple_Exporter\Workspace( 1 );
		$component = new Image(
			'<img src="" alt="Example" align="left" />',
			$workspace,
			$this->settings,
			$this->styles,
			$this->layouts
		);
		$result = $component->to_array();

		// Test.
		$this->assertEmpty( $result );
	}

	/**
	 * Test the `apple_news_image_json` filter.
	 *
	 * @access public
	 */
	public function testFilter() {

		// Setup.
		$this->settings->set( 'use_remote_images', 'no' );
		$workspace = $this->prophet->prophesize( '\Apple_Exporter\Workspace' );
		$workspace->bundle_source(
			'filename.jpg',
			'http://someurl.com/filename.jpg'
		)->shouldBeCalled();
		$component = new Image(
			'<img src="http://someurl.com/filename.jpg" alt="Example" />',
			$workspace->reveal(),
			$this->settings,
			$this->styles,
			$this->layouts
		);
		add_filter(
			'apple_news_image_json',
			array( $this, 'filter_apple_news_image_json' )
		);

		// Test.
		$result = $component->to_array();
		$this->assertEquals( 'default-image', $result['layout'] );

		// Teardown.
		remove_filter(
			'apple_news_image_json',
			array( $this, 'filter_apple_news_image_json' )
		);
	}

	/**
	 * Test src attribute that is just a fragment.
	 *
	 * @access public
	 */
	public function testFragmentSrc() {

		// Setup.
		$this->settings->set( 'use_remote_images', 'yes' );
		$workspace = new \Apple_Exporter\Workspace( 1 );
		$component = new Image(
			'<img src="#fragment" alt="Example" align="left" />',
			$workspace,
			$this->settings,
			$this->styles,
			$this->layouts
		);
		$result = $component->to_array();

		// Test.
		$this->assertEmpty( $result );
	}

	/**
	 * Test standard JSON export.
	 *
	 * @access public
	 */
	public function testGeneratedJSON() {

		// Setup.
		$this->settings->set( 'use_remote_images', 'no' );
		$workspace = $this->prophet->prophesize( '\Apple_Exporter\Workspace' );
		$workspace->bundle_source(
			'filename.jpg',
			'http://someurl.com/filename.jpg'
		)->shouldBeCalled();
		$component = new Image(
			'<img src="http://someurl.com/filename.jpg" alt="Example" align="left" />',
			$workspace->reveal(),
			$this->settings,
			$this->styles,
			$this->layouts
		);
		$result = $component->to_array();

		// Test.
		$this->assertEquals( 'photo', $result['role'] );
		$this->assertEquals( 'bundle://filename.jpg', $result['URL'] );
		$this->assertEquals( 'anchored-image', $result['layout'] );
	}

	/**
	 * Test remote image JSON export.
	 *
	 * @access public
	 */
	public function testGeneratedJSONRemoteImages() {

		// Setup.
		$this->settings->set( 'use_remote_images', 'yes' );
		$workspace = $this->prophet->prophesize( '\Apple_Exporter\Workspace' );
		$workspace->bundle_source(
			'filename.jpg',
			'http://someurl.com/filename.jpg'
		)->shouldNotBeCalled();
		$component = new Image(
			'<img src="http://someurl.com/filename.jpg" alt="Example" align="left" />',
			$workspace->reveal(),
			$this->settings,
			$this->styles,
			$this->layouts
		);
		$result = $component->to_array();

		// Test.
		$this->assertEquals( 'photo', $result['role'] );
		$this->assertEquals( 'http://someurl.com/filename.jpg', $result['URL'] );
		$this->assertEquals( 'anchored-image', $result['layout'] );
	}

	/**
	 * Test relative src attribute.
	 *
	 * @access public
	 */
	public function testRelativeSrc() {

		// Setup.
		$this->settings->set( 'use_remote_images', 'yes' );
		$workspace = new \Apple_Exporter\Workspace( 1 );
		$component = new Image(
			'<img src="/relative/path/to/image.jpg" alt="Example" align="left" />',
			$workspace,
			$this->settings,
			$this->styles,
			$this->layouts
		);
		$result = $component->to_array();

		// Test.
		$this->assertEquals( 'photo', $result['role'] );
		$this->assertEquals( 'http://example.org/relative/path/to/image.jpg', $result['URL'] );
		$this->assertEquals( 'anchored-image', $result['layout'] );
	}

	/**
	 * Tests image and image caption settings.
	 *
	 * @access public
	 */
	public function testSettings() {

		// Setup.
		$theme = \Apple_Exporter\Theme::get_used();
		$settings = $theme->all_settings();
		$this->settings->full_bleed_images = 'yes';
		$settings['caption_color'] = '#abcdef';
		$settings['caption_font'] = 'AmericanTypewriter';
		$settings['caption_line_height'] = 28;
		$settings['caption_size'] = 20;
		$settings['caption_tracking'] = 50;
		$theme->load( $settings );
		$this->assertTrue( $theme->save() );
		$html = <<<HTML
<figure>
	<img src="http://someurl.com/filename.jpg" alt="Example">
	<figcaption class="wp-caption-text">Caption Text</figcaption>
</figure>
HTML;
		$component = new Image(
			$html,
			new Workspace( 1 ),
			$this->settings,
			$this->styles,
			$this->layouts
		);
		$result = $component->to_array();

		// Test.
		$this->assertEquals( true, $result['layout']['ignoreDocumentMargin'] );
		$this->assertEquals(
			'#abcdef',
			$result['components'][1]['textStyle']['textColor']
		);
		$this->assertEquals(
			'AmericanTypewriter',
			$result['components'][1]['textStyle']['fontName']
		);
		$this->assertEquals(
			28,
			$result['components'][1]['textStyle']['lineHeight']
		);
		$this->assertEquals(
			20,
			$result['components'][1]['textStyle']['fontSize']
		);
		$this->assertEquals(
			0.5,
			$result['components'][1]['textStyle']['tracking']
		);
	}
}
