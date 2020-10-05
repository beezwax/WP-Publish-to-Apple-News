<?php
/**
 * Publish to Apple News tests: Body_Test class
 *
 * @package Apple_News
 * @subpackage Tests
 */

require_once __DIR__ . '/class-component-testcase.php';

use Apple_Exporter\Components\Body;
use Apple_Exporter\Exporter;
use Apple_Exporter\Exporter_Content;

/**
 * A class to test the behavior of the
 * Apple_Exporter\Components\Body class.
 *
 * @package Apple_News
 * @subpackage Tests
 */
class Body_Test extends Component_TestCase {

	/**
	 * A data provider that supplies empty HTML signatures to ensure that they
	 * are not erroneously transformed into empty body elements.
	 *
	 * @return array An array of arrays representing function arguments.
	 */
	public function data_empty_html() {
		return [
			// Test classic editor, multiple line breaks.
			[
				<<<HTML
A



B
HTML
			],

			// Test classic editor, multiple line breaks with &nbsp.
			[
				<<<HTML
A

&nbsp;

B
HTML
			],

			// Test classic editor, extra line breaks at the end.
			[
				<<<HTML
A

B


HTML
			],

			// Test classic editor, extra line breaks at the end with a non-breaking space.
			[
				<<<HTML
A

B

&nbsp;
HTML
			],

			// Test Gutenberg editor, empty paragraph tag.
			[
				<<<HTML
<!-- wp:paragraph -->
<p>A</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>B</p>
<!-- /wp:paragraph -->
HTML
			],

			// Test Gutenberg editor, paragraph tag containing a single space.
			[
				<<<HTML
<!-- wp:paragraph -->
<p>A</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p> </p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>B</p>
<!-- /wp:paragraph -->
HTML
			],

			// Test Gutenberg editor, paragraph tag containing a non-breaking space.
			[
				<<<HTML
<!-- wp:paragraph -->
<p>A</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>&nbsp;</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>B</p>
<!-- /wp:paragraph -->
HTML
			],

			// Test Gutenberg editor, extra paragraph at the end.
			[
				<<<HTML
<!-- wp:paragraph -->
<p>A</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>B</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p></p>
<!-- /wp:paragraph -->
HTML
			],

			// Test Gutenberg editor, extra paragraph at the end containing a space.
			[
				<<<HTML
<!-- wp:paragraph -->
<p>A</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>B</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p> </p>
<!-- /wp:paragraph -->
HTML
			],

			// Test Gutenberg editor, extra paragraph at the end containing a non-breaking space.
			[
				<<<HTML
<!-- wp:paragraph -->
<p>A</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>B</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>&nbsp;</p>
<!-- /wp:paragraph -->
HTML
			],
		];
	}

	/**
	 * A filter function to modify the text style in the generated JSON.
	 *
	 * @param array $json The JSON array to modify.
	 *
	 * @access public
	 * @return array The modified JSON.
	 */
	public function filter_apple_news_body_json( $json ) {
		$json['textStyle'] = 'fancy-body';

		return $json;
	}

	/**
	 * A filter function to modify the HTML enabled flag for this component.
	 *
	 * @param bool $enabled Whether HTML support is enabled for this component.
	 *
	 * @access public
	 * @return bool Whether HTML support is enabled for this component.
	 */
	public function filter_apple_news_body_html_enabled( $enabled ) {
		return ! $enabled;
	}

	/**
	 * Tests handling for empty content.
	 *
	 * @access public
	 */
	public function test_empty_content() {

		// Setup.
		$this->settings->html_support = 'no';
		$html = '<p><a href="https://www.apple.com/">&nbsp;</a></p>';
		$component = new Body(
			$html,
			$this->workspace,
			$this->settings,
			$this->styles,
			$this->layouts
		);

		// Test.
		$this->assertEquals(
			array(),
			$component->to_array()
		);

		// Teardown.
		$this->settings->html_support = 'yes';
	}

	/**
	 * Tests handling for empty HTML content.
	 *
	 * @dataProvider data_empty_html
	 *
	 * @param string $post_content The post content for the post.
	 */
	public function test_empty_html_content( $post_content ) {
		$post_id = self::factory()->post->create( [ 'post_content' => $post_content ] );
		$json    = $this->get_json_for_post( $post_id );

		// There should only be two body components, one containing A, one containing B.
		$this->assertEquals( 4, count( $json['components'] ) );
		$this->assertEquals( '<p>A</p>', $json['components'][2]['text'] );
		$this->assertEquals( '<p>B</p>', $json['components'][3]['text'] );
	}

	/**
	 * Test the `apple_news_body_json` filter.
	 *
	 * @access public
	 */
	public function testFilter() {

		// Setup.
		$this->settings->html_support = 'no';
		$this->set_theme_settings( [ 'initial_dropcap' => 'no' ] );
		$component = new Body(
			'<p>my text</p>',
			$this->workspace,
			$this->settings,
			$this->styles,
			$this->layouts
		);
		add_filter(
			'apple_news_body_json',
			array( $this, 'filter_apple_news_body_json' )
		);

		// Test.
		$result = $component->to_array();
		$this->assertEquals( 'fancy-body', $result['textStyle'] );

		// Teardown.
		remove_filter(
			'apple_news_body_json',
			array( $this, 'filter_apple_news_body_json' )
		);
		$this->settings->html_support = 'yes';
	}

	/**
	 * Test the `apple_news_body_html_enabled` filter.
	 *
	 * @access public
	 */
	public function testFilterHTML() {

		// Test before filter.
		$component = new Body(
			'<p>my text</p>',
			$this->workspace,
			$this->settings,
			$this->styles,
			$this->layouts
		);
		$this->assertEquals(
			array(
				'role'      => 'body',
				'text'      => '<p>my text</p>',
				'format'    => 'html',
				'textStyle' => 'dropcapBodyStyle',
				'layout'    => 'body-layout',
			),
			$component->to_array()
		);

		// Test after filter.
		add_filter(
			'apple_news_body_html_enabled',
			array( $this, 'filter_apple_news_body_html_enabled' )
		);
		$component = new Body(
			'<p>my text</p>',
			$this->workspace,
			$this->settings,
			$this->styles,
			$this->layouts
		);
		$this->assertEquals(
			array(
				'role'      => 'body',
				'text'      => 'my text' . "\n\n",
				'format'    => 'markdown',
				'textStyle' => 'default-body',
				'layout'    => 'body-layout',
			),
			$component->to_array()
		);

		// Teardown.
		remove_filter(
			'apple_news_body_html_enabled',
			array( $this, 'filter_apple_news_body_html_enabled' )
		);
	}

	/**
	 * Tests HTML formatting.
	 *
	 * @access public
	 */
	public function testHTML() {

		// Setup.
		$html = <<<HTML
<p>Lorem ipsum. <a href="https://wordpress.org">Dolor sit amet</a>.</p>
<pre>
	Preformatted text.
</pre>
<p>Testing a <code>code sample</code>.</p>
HTML;
		$component = new Body(
			$html,
			$this->workspace,
			$this->settings,
			$this->styles,
			$this->layouts
		);

		// Test.
		$this->assertEquals(
			array(
				'text' => $html,
				'role' => 'body',
				'format' => 'html',
				'textStyle' => 'dropcapBodyStyle',
				'layout' => 'body-layout',
			),
			$component->to_array()
		);
	}

	/**
	 * Tests the removal of script tags.
	 *
	 * @access public
	 */
	public function testRemoveScriptTags() {

		// Setup.
		$html = <<<HTML
<p><strong>Lorem ipsum dolor sit amet<script>if (1 > 0) { console.log('something'); }</script></strong></p>
HTML;
		$component = new Body(
			$html,
			$this->workspace,
			$this->settings,
			$this->styles,
			$this->layouts
		);

		// Test.
		$this->assertEquals(
			array(
				'text'      => '<p><strong>Lorem ipsum dolor sit amet</strong></p>',
				'role'      => 'body',
				'format'    => 'html',
				'textStyle' => 'dropcapBodyStyle',
				'layout'    => 'body-layout',
			),
			$component->to_array()
		);
	}

	/**
	 * Tests the transformation process for an HTML entity (e.g., &amp;).
	 *
	 * @access public
	 */
	public function testTransformHtmlEntities() {

		// Setup.
		$this->settings->html_support = 'no';
		$body_component = new Body(
			'<p>my &amp; text</p>',
			$this->workspace,
			$this->settings,
			$this->styles,
			$this->layouts
		);

		// Test.
		$this->assertEquals(
			array(
				'text' => "my & text\n\n",
				'role' => 'body',
				'format' => 'markdown',
				'textStyle' => 'dropcapBodyStyle',
				'layout' => 'body-layout',
			),
			$body_component->to_array()
		);

		// Teardown.
		$this->settings->html_support = 'yes';
	}

	/**
	 * Tests transformation of lists with nested images.
	 *
	 * @access public
	 */
	public function testLists() {

		// Setup.
		$this->settings->html_support = 'no';
		$content = <<<HTML
<ul>
<li>item 1</li>
<li><img src="http://someurl.com/filename.jpg"><br />item 2</li>
<li>item 3</li>
</ul>
HTML;
		$cover = $this->get_new_attachment();
		$content = new Exporter_Content( 3, 'Title', $content, null, $cover );

		// Run the export.
		$exporter = new Exporter( $content, $this->workspace, $this->settings );
		$json = $exporter->export();
		$this->ensure_tokens_replaced( $json );
		$json = json_decode( $json, true );

		// Validate list split in generated JSON.
		$this->assertEquals(
			'body',
			$json['components'][1]['components'][1]['role']
		);
		$this->assertEquals(
			'- item 1',
			$json['components'][1]['components'][1]['text']
		);
		$this->assertEquals(
			'photo',
			$json['components'][1]['components'][2]['role']
		);
		$this->assertEquals(
			'http://someurl.com/filename.jpg',
			$json['components'][1]['components'][2]['URL']
		);
		$this->assertEquals(
			'body',
			$json['components'][1]['components'][3]['role']
		);
		$this->assertEquals(
			'- item 2' . "\n" . '- item 3',
			$json['components'][1]['components'][3]['text']
		);

		// Teardown.
		$this->settings->html_support = 'yes';
	}

	/**
	 * Tests body settings.
	 *
	 * @access public
	 */
	public function testSettings() {

		// Setup.
		$content = new Exporter_Content(
			3,
			'Title',
			'<p>Lorem ipsum.</p><p>Dolor sit amet.</p>'
		);

		// Set body settings.
		$this->set_theme_settings(
			[
				'body_font'                      => 'AmericanTypewriter',
				'body_size'                      => 20,
				'body_color'                     => '#abcdef',
				'body_link_color'                => '#fedcba',
				'body_line_height'               => 28,
				'body_tracking'                  => 50,
				'dropcap_background_color'       => '#abcabc',
				'dropcap_color'                  => '#defdef',
				'dropcap_font'                   => 'AmericanTypewriter-Bold',
				'dropcap_number_of_characters'   => 15,
				'dropcap_number_of_lines'        => 10,
				'dropcap_number_of_raised_lines' => 5,
				'dropcap_padding'                => 20,
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
			$json['componentTextStyles']['default-body']['fontName']
		);
		$this->assertEquals(
			20,
			$json['componentTextStyles']['default-body']['fontSize']
		);
		$this->assertEquals(
			'#abcdef',
			$json['componentTextStyles']['default-body']['textColor']
		);
		$this->assertEquals(
			'#fedcba',
			$json['componentTextStyles']['default-body']['linkStyle']['textColor']
		);
		$this->assertEquals(
			28,
			$json['componentTextStyles']['default-body']['lineHeight']
		);
		$this->assertEquals(
			0.5,
			$json['componentTextStyles']['default-body']['tracking']
		);
		$this->assertFalse(
			isset( $json['componentTextStyles']['default-body']['conditional'] )
		);
		$this->assertEquals(
			'#abcabc',
			$json['componentTextStyles']['dropcapBodyStyle']['dropCapStyle']['backgroundColor']
		);
		$this->assertEquals(
			'#defdef',
			$json['componentTextStyles']['dropcapBodyStyle']['dropCapStyle']['textColor']
		);
		$this->assertEquals(
			'AmericanTypewriter-Bold',
			$json['componentTextStyles']['dropcapBodyStyle']['dropCapStyle']['fontName']
		);
		$this->assertEquals(
			15,
			$json['componentTextStyles']['dropcapBodyStyle']['dropCapStyle']['numberOfCharacters']
		);
		$this->assertEquals(
			10,
			$json['componentTextStyles']['dropcapBodyStyle']['dropCapStyle']['numberOfLines']
		);
		$this->assertEquals(
			5,
			$json['componentTextStyles']['dropcapBodyStyle']['dropCapStyle']['numberOfRaisedLines']
		);
		$this->assertEquals(
			20,
			$json['componentTextStyles']['dropcapBodyStyle']['dropCapStyle']['padding']
		);
		$this->assertFalse(
			isset( $json['componentTextStyles']['dropCapStyle']['conditional'] )
		);
	}

	public function testDarkColorSettings() {
		// Setup.
		$this->set_theme_settings(
			[
				'body_color_dark'                     => '#abcdef',
				'body_link_color_dark'                => '#fedcba',
				'dropcap_background_color_dark'       => '#abcabc',
				'dropcap_color_dark'                  => '#defdef',
			]
		);

		$post_id = $this->factory->post->create( array(
			'post_content' => '<p>Lorem ipsum.</p><p>Dolor sit amet.</p>',
		) );

		$json    = $this->get_json_for_post( $post_id );
		$this->assertEquals(
			'#abcdef',
			$json['componentTextStyles']['default-body']['conditional']['textColor']
		);
		$this->assertEquals(
			'#fedcba',
			$json['componentTextStyles']['default-body']['conditional']['linkStyle']['textColor']
		);
		$this->assertEquals(
			'#abcabc',
			$json['componentTextStyles']['dropcapBodyStyle']['conditional']['dropCapStyle']['backgroundColor']
		);
		$this->assertEquals(
			'#defdef',
			$json['componentTextStyles']['dropcapBodyStyle']['conditional']['dropCapStyle']['textColor']
		);
	}

	/**
	 * Tests 0 values in tokens.
	 *
	 * @access public
	 */
	public function testSettingsZeroValueInToken() {

		// Setup.
		$content = new Exporter_Content(
			3,
			'Title',
			'<p>Lorem ipsum.</p><p>Dolor sit amet.</p>'
		);

		// Set body settings.
		$this->set_theme_settings( [ 'body_line_height' => 0 ] );

		// Run the export.
		$exporter = new Exporter( $content, $this->workspace, $this->settings );
		$json = $exporter->export();
		$this->ensure_tokens_replaced( $json );
		$json = json_decode( $json, true );

		// Validate body settings in generated JSON.
		$this->assertEquals(
			0,
			$json['componentTextStyles']['default-body']['lineHeight']
		);
	}

	/**
	 * Tests the transformation process from a paragraph to a Body component.
	 *
	 * @access public
	 */
	public function testTransform() {

		// Setup.
		$this->settings->html_support = 'no';
		$component = new Body(
			'<p>my text</p>',
			$this->workspace,
			$this->settings,
			$this->styles,
			$this->layouts
		);

		// Test.
		$this->assertEquals(
			array(
				'text' => "my text\n\n",
				'role' => 'body',
				'format' => 'markdown',
				'textStyle' => 'dropcapBodyStyle',
				'layout' => 'body-layout',
			),
			$component->to_array()
		);

		// Teardown.
		$this->settings->html_support = 'yes';
	}

	/**
	 * Test the setting to disable the initial dropcap.
	 *
	 * @access public
	 */
	public function testWithoutDropcap() {

		// Setup.
		$this->settings->html_support = 'no';
		$this->set_theme_settings( [ 'initial_dropcap' => 'no' ] );
		$body_component = new Body(
			'<p>my text</p>',
			$this->workspace,
			$this->settings,
			$this->styles,
			$this->layouts
		);

		// Test.
		$this->assertEquals(
			array(
				'text' => "my text\n\n",
				'role' => 'body',
				'format' => 'markdown',
				'textStyle' => 'default-body',
				'layout' => 'body-layout',
			),
			$body_component->to_array()
		);

		// Teardown.
		$this->settings->html_support = 'yes';
	}
}
