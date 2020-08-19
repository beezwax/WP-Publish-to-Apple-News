<?php
/**
 * Publish to Apple News tests: Quote_Test class
 *
 * @package Apple_News
 * @subpackage Tests
 */

use Apple_Exporter\Components\Quote;
use Apple_Exporter\Exporter;
use Apple_Exporter\Exporter_Content;

/**
 * A class to test the behavior of the
 * Apple_Exporter\Components\Quote class.
 *
 * @package Apple_News
 * @subpackage Tests
 */
class Quote_Test extends Component_TestCase {

	/**
	 * A data provider for the testTransformPullquote function.
	 *
	 * @see self::testTransformPullquote()
	 *
	 * @access public
	 * @return array Parameters to use when calling testTransformPullquote.
	 */
	public function dataTransformPullquote() {
		return array(
			array( 'my text', '<p>my text</p>', 'no' ),
			array( 'my text', '<p>“my text”</p>', 'yes' ),
			array( '"my text"', '<p>“my text”</p>', 'yes' ),
			array( '“my text”', '<p>“my text”</p>', 'yes' ),
		);
	}

	/**
	 * A filter function to modify the hanging punctuation text.
	 *
	 * @param string $modified_text The modified text to be filtered.
	 * @param string $text The original text for the quote.
	 *
	 * @access public
	 * @return string The modified text.
	 */
	public function filter_apple_news_apply_hanging_punctuation( $modified_text, $text ) {
		return '«' . trim( $modified_text, '“”' ) . '»';
	}

	/**
	 * A filter function to modify the text style in the generated JSON.
	 *
	 * @param array $json The JSON array to modify.
	 *
	 * @access public
	 * @return array The modified JSON.
	 */
	public function filter_apple_news_quote_json( $json ) {
		$json['textStyle'] = 'fancy-quote';

		return $json;
	}

	/**
	 * Test the `apple_news_apply_hanging_punctuation` filter.
	 *
	 * @access public
	 */
	public function testFilterHangingPunctuation() {

		// Setup.
		$this->set_theme_settings( [ 'pullquote_hanging_punctuation' => 'yes' ] );
		add_filter(
			'apple_news_apply_hanging_punctuation',
			array( $this, 'filter_apple_news_apply_hanging_punctuation' ),
			10,
			2
		);
		$component = new Quote(
			'<blockquote class="apple-news-pullquote"><p>my quote</p></blockquote>',
			$this->workspace,
			$this->settings,
			$this->styles,
			$this->layouts
		);

		// Test.
		$result = $component->to_array();
		$this->assertEquals(
			'<p>«my quote»</p>',
			$result['components'][0]['text']
		);

		// Teardown.
		remove_filter(
			'apple_news_apply_hanging_punctuation',
			array( $this, 'filter_apple_news_apply_hanging_punctuation' )
		);
	}

	/**
	 * Test the `apple_news_quote_json` filter.
	 *
	 * @access public
	 */
	public function testFilterJSON() {

		// Setup.
		$component = new Quote(
			'<blockquote><p>my quote</p></blockquote>',
			$this->workspace,
			$this->settings,
			$this->styles,
			$this->layouts
		);
		add_filter(
			'apple_news_quote_json',
			array( $this, 'filter_apple_news_quote_json' )
		);

		// Test.
		$result = $component->to_array();
		$this->assertEquals( 'fancy-quote', $result['textStyle'] );

		// Teardown.
		remove_filter(
			'apple_news_quote_json',
			array( $this, 'filter_apple_news_quote_json' )
		);
	}

	/**
	 * Tests blockquote settings.
	 *
	 * @access public
	 */
	public function testSettingsBlockquote() {

		// Setup.
		$content = new Exporter_Content(
			3,
			'Title',
			'<blockquote><p>my quote</p></blockquote>'
		);

		// Set quote settings.
		$this->set_theme_settings(
			[
				'blockquote_font'             => 'AmericanTypewriter',
				'blockquote_size'             => 20,
				'blockquote_color'            => '#abcdef',
				'blockquote_line_height'      => 28,
				'blockquote_tracking'         => 50,
				'blockquote_background_color' => '#fedcba',
				'blockquote_border_color'     => '#012345',
				'blockquote_border_style'     => 'dashed',
				'blockquote_border_width'     => 10,
			]
		);

		// Run the export.
		$exporter = new Exporter( $content, $this->workspace, $this->settings );
		$json = $exporter->export();
		$this->ensure_tokens_replaced( $json );
		$json = json_decode( $json, true );

		// Validate body settings in generated JSON.
		$this->assertEquals(
			'AmericanTypewriter',
			$json['componentTextStyles']['default-blockquote-left']['fontName']
		);
		$this->assertEquals(
			20,
			$json['componentTextStyles']['default-blockquote-left']['fontSize']
		);
		$this->assertEquals(
			'#abcdef',
			$json['componentTextStyles']['default-blockquote-left']['textColor']
		);
		$this->assertEquals(
			28,
			$json['componentTextStyles']['default-blockquote-left']['lineHeight']
		);
		$this->assertEquals(
			0.5,
			$json['componentTextStyles']['default-blockquote-left']['tracking']
		);
		$this->assertFalse( isset( $json['componentTextStyles']['default-blockquote-left']['conditional'] ) );
		$this->assertEquals(
			'#fedcba',
			$json['components'][1]['style']['backgroundColor']
		);
		$this->assertEquals(
			'#012345',
			$json['components'][1]['style']['border']['all']['color']
		);
		$this->assertEquals(
			'dashed',
			$json['components'][1]['style']['border']['all']['style']
		);
		$this->assertEquals(
			10,
			$json['components'][1]['style']['border']['all']['width']
		);
		$this->assertFalse( isset( $json['components'][1]['style']['conditional'] ) );
	}

	/**
	 * Test Dark Color Settings For Blockquote.
	 *
	 * @access public
	 */
	public function testDarkColorsBlockquote() {

		// Set quote settings.
		$this->set_theme_settings(
			[
				'blockquote_color_dark'							=> '#abcdef',
				'blockquote_background_color_dark'	=> '#fedcba',
				'blockquote_border_color_dark'			=> '#012345',
			]
		);

		$post_id = self::factory()->post->create(
			[
				'post_content' => '<blockquote><p>my quote</p></blockquote>',
			]
		);
		$json    = $this->get_json_for_post( $post_id );
		$this->assertEquals(
			'#abcdef',
			$json['componentTextStyles']['default-blockquote-left']['conditional']['textColor']
		);
		$this->assertEquals(
			'#fedcba',
			$json['components'][3]['style']['conditional']['backgroundColor']
		);
		$this->assertEquals(
			'#012345',
			$json['components'][3]['style']['conditional']['border']['all']['color']
		);

		$this->assertEquals(
			'#abcdef',
			$json['componentTextStyles']['default-blockquote-left']['conditional']['textColor']
		);
		$this->assertEquals(
			'#fedcba',
			$json['components'][3]['style']['conditional']['backgroundColor']
		);
		$this->assertEquals(
			'#012345',
			$json['components'][3]['style']['conditional']['border']['all']['color']
		);
	}

	/**
	 * Test Dark Color Settings Pullquote
	 *
	 * @access public
	 */
	public function testDarkColorsPullquote() {

		// Setup.
		$content = new Exporter_Content(
			3,
			'Title',
			'<blockquote class="apple-news-pullquote"><p>my quote</p></blockquote>'
		);

		// Set quote settings.
		$this->set_theme_settings(
			[
				'pullquote_color_dark'               => '#abcdef',
				'pullquote_border_color_dark'        => '#abcdef',
			]
		);

		// Run the export.
		$exporter = new Exporter( $content, $this->workspace, $this->settings );
		$json = $exporter->export();
		$this->ensure_tokens_replaced( $json );
		$json = json_decode( $json, true );

		// Validate body settings in generated JSON.
		$this->assertEquals(
			'#abcdef',
			$json['componentTextStyles']['default-pullquote-left']['conditional']['textColor']
		);
		$this->assertEquals(
			'#abcdef',
			$json['components'][1]['style']['conditional']['border']['all']['color']
		);
	}

	/**
	 * Tests pullquote settings.
	 *
	 * @access public
	 */
	public function testSettingsPullquote() {

		// Setup.
		$content = new Exporter_Content(
			3,
			'Title',
			'<blockquote class="apple-news-pullquote"><p>my quote</p></blockquote>'
		);

		// Set quote settings.
		$this->set_theme_settings(
			[
				'pullquote_font'                => 'AmericanTypewriter',
				'pullquote_size'                => 20,
				'pullquote_color'               => '#abcdef',
				'pullquote_hanging_punctuation' => 'yes',
				'pullquote_line_height'         => 28,
				'pullquote_tracking'            => 50,
				'pullquote_transform'           => 'uppercase',
			]
		);

		// Run the export.
		$exporter = new Exporter( $content, $this->workspace, $this->settings );
		$json = $exporter->export();
		$this->ensure_tokens_replaced( $json );
		$json = json_decode( $json, true );

		// Validate body settings in generated JSON.
		$this->assertEquals(
			'AmericanTypewriter',
			$json['componentTextStyles']['default-pullquote-left']['fontName']
		);
		$this->assertEquals(
			20,
			$json['componentTextStyles']['default-pullquote-left']['fontSize']
		);
		$this->assertTrue(
			$json['componentTextStyles']['default-pullquote-left']['hangingPunctuation']
		);
		$this->assertEquals(
			'#abcdef',
			$json['componentTextStyles']['default-pullquote-left']['textColor']
		);
		$this->assertEquals(
			28,
			$json['componentTextStyles']['default-pullquote-left']['lineHeight']
		);
		$this->assertEquals(
			0.5,
			$json['componentTextStyles']['default-pullquote-left']['tracking']
		);
		$this->assertEquals(
			'uppercase',
			$json['componentTextStyles']['default-pullquote-left']['textTransform']
		);
	}

	/**
	 * Tests the transformation process from a blockquote to a Quote component.
	 *
	 * @access public
	 */
	public function testTransformBlockquote() {

		// Setup.
		$component = new Quote(
			'<blockquote><p>my quote</p></blockquote>',
			$this->workspace,
			$this->settings,
			$this->styles,
			$this->layouts
		);
		$result_wrapper = $component->to_array();
		$result = $result_wrapper['components'][0];

		// Test.
		$this->assertEquals( 'container', $result_wrapper['role'] );
		$this->assertEquals( 'quote', $result['role'] );
		$this->assertEquals( '<p>my quote</p>', $result['text'] );
		$this->assertEquals( 'html', $result['format'] );
		$this->assertEquals( 'default-blockquote-left', $result['textStyle'] );
		$this->assertEquals( 'blockquote-layout', $result['layout'] );
	}

	/**
	 * Tests the transformation process with text alignment checking.
	 *
	 * @access public
	 */
	public function testTransformBlockquoteAlignment() {

		// Setup.
		$componentLeft = new Quote(
			'<blockquote style="text-align:left" class="wp-block-quote"><p>Quote Text</p></blockquote>',
			$this->workspace,
			$this->settings,
			$this->styles,
			$this->layouts
		);

		$componentCenter = new Quote(
			'<blockquote style="text-align:center" class="wp-block-quote"><p>Quote Text</p></blockquote>',
			$this->workspace,
			$this->settings,
			$this->styles,
			$this->layouts
		);

		$componentRight = new Quote(
			'<blockquote style="text-align:right" class="wp-block-quote"><p>Quote Text</p></blockquote>',
			$this->workspace,
			$this->settings,
			$this->styles,
			$this->layouts
		);

		$result_wrapper = $componentLeft->to_array();
		$result = $result_wrapper['components'][0];

		// Test.
		$this->assertEquals( 'container', $result_wrapper['role'] );
		$this->assertEquals( 'quote', $result['role'] );
		$this->assertEquals( '<p>Quote Text</p>', $result['text'] );
		$this->assertEquals( 'html', $result['format'] );
		$this->assertEquals( 'default-blockquote-left', $result['textStyle'] );
		$this->assertEquals( 'blockquote-layout', $result['layout'] );

		$result_wrapper = $componentCenter->to_array();
		$result = $result_wrapper['components'][0];

		// Test.
		$this->assertEquals( 'container', $result_wrapper['role'] );
		$this->assertEquals( 'quote', $result['role'] );
		$this->assertEquals( '<p>Quote Text</p>', $result['text'] );
		$this->assertEquals( 'html', $result['format'] );
		$this->assertEquals( 'default-blockquote-center', $result['textStyle'] );
		$this->assertEquals( 'blockquote-layout', $result['layout'] );

		$result_wrapper = $componentRight->to_array();
		$result = $result_wrapper['components'][0];

		// Test.
		$this->assertEquals( 'container', $result_wrapper['role'] );
		$this->assertEquals( 'quote', $result['role'] );
		$this->assertEquals( '<p>Quote Text</p>', $result['text'] );
		$this->assertEquals( 'html', $result['format'] );
		$this->assertEquals( 'default-blockquote-right', $result['textStyle'] );
		$this->assertEquals( 'blockquote-layout', $result['layout'] );
	}

	/**
	 * Tests the transformation process when using a gutenberg pullquote with text alignment checking.
	 *
	 * @access public
	 */
	public function testTransformGutenbergBlockquoteAlignment() {

		// Setup.
		$componentLeft = new Quote(
			'<figure class="wp-block-pullquote alignleft"><blockquote><p>Quote Text</p></blockquote></figure>',
			$this->workspace,
			$this->settings,
			$this->styles,
			$this->layouts
		);

		$componentCenter = new Quote(
			'<figure class="wp-block-pullquote alignwide"><blockquote><p>Quote Text</p></blockquote></figure>',
			$this->workspace,
			$this->settings,
			$this->styles,
			$this->layouts
		);

		$componentRight = new Quote(
			'<figure class="wp-block-pullquote alignright"><blockquote><p>Quote Text</p></blockquote></figure>',
			$this->workspace,
			$this->settings,
			$this->styles,
			$this->layouts
		);

		$result_wrapper = $componentLeft->to_array();
		$result = $result_wrapper['components'][0];

		// Test.
		$this->assertEquals( 'container', $result_wrapper['role'] );
		$this->assertEquals( 'quote', $result['role'] );
		$this->assertEquals( '<p>Quote Text</p>', $result['text'] );
		$this->assertEquals( 'html', $result['format'] );
		$this->assertEquals( 'default-pullquote-left', $result['textStyle'] );
		$this->assertEquals( 'pullquote-layout', $result['layout'] );

		$result_wrapper = $componentCenter->to_array();
		$result = $result_wrapper['components'][0];

		// Test.
		$this->assertEquals( 'container', $result_wrapper['role'] );
		$this->assertEquals( 'quote', $result['role'] );
		$this->assertEquals( '<p>Quote Text</p>', $result['text'] );
		$this->assertEquals( 'html', $result['format'] );
		$this->assertEquals( 'default-pullquote-center', $result['textStyle'] );
		$this->assertEquals( 'pullquote-layout', $result['layout'] );

		$result_wrapper = $componentRight->to_array();
		$result = $result_wrapper['components'][0];

		// Test.
		$this->assertEquals( 'container', $result_wrapper['role'] );
		$this->assertEquals( 'quote', $result['role'] );
		$this->assertEquals( '<p>Quote Text</p>', $result['text'] );
		$this->assertEquals( 'html', $result['format'] );
		$this->assertEquals( 'default-pullquote-right', $result['textStyle'] );
		$this->assertEquals( 'pullquote-layout', $result['layout'] );
	}

	/**
	 * Tests the transformation process from a pullquote to a Quote component.
	 *
	 * @dataProvider dataTransformPullquote
	 *
	 * @param string $text The text to use in the blockquote element.
	 * @param string $expected The expected text node value after compilation.
	 * @param string $hanging_punctuation The setting value for hanging punctuation.
	 *
	 * @access public
	 */
	public function testTransformPullquote( $text, $expected, $hanging_punctuation ) {

		// Setup.
		$this->set_theme_settings( [ 'pullquote_hanging_punctuation' => $hanging_punctuation ] );
		$component = new Quote(
			'<blockquote class="apple-news-pullquote"><p>' . $text . '</p></blockquote>',
			$this->workspace,
			$this->settings,
			$this->styles,
			$this->layouts
		);
		$result_wrapper = $component->to_array();
		$result = $result_wrapper['components'][0];

		// Test.
		$this->assertEquals( 'container', $result_wrapper['role'] );
		$this->assertEquals( 'quote', $result['role'] );
		$this->assertEquals( $expected, $result['text'] );
		$this->assertEquals( 'html', $result['format'] );
		$this->assertEquals( 'default-pullquote-left', $result['textStyle'] );
		$this->assertEquals( 'pullquote-layout', $result['layout'] );
	}
}
