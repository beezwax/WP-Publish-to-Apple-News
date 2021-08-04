<?php
/**
 * Publish to Apple News tests: Body_Test class
 *
 * @package Apple_News
 * @subpackage Tests
 */

/**
 * A class to test the behavior of the Apple_Exporter\Components\Body class.
 *
 * @package Apple_News
 * @subpackage Tests
 */
class Body_Test extends Apple_News_Testcase {

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

			// Test Gutenberg editor, extra paragraph at the end containing a non-breaking space surrounded by a link tag.
			[
				<<<HTML
<!-- wp:paragraph -->
<p>A</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>B</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><a href="https://www.apple.com/">&nbsp;</a></p>
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
	 * @return bool Whether HTML support is enabled for this component.
	 */
	public function filter_apple_news_body_html_enabled( $enabled ) {
		return ! $enabled;
	}

	/**
	 * Tests code formatting.
	 */
	public function test_code_formatting() {
		$content = <<<HTML
<!-- wp:paragraph -->
<p>Lorem ipsum. <a href="https://www.wordpress.org">Dolor sit amet.</a></p>
<!-- /wp:paragraph -->

<!-- wp:preformatted -->
<pre class="wp-block-preformatted">Preformatted text.</pre>
<!-- /wp:preformatted -->

<!-- wp:paragraph -->
<p>Testing a <code>code sample</code>.</p>
<!-- /wp:paragraph -->
HTML;
		$post_id = self::factory()->post->create( [ 'post_content' => $content ] );
		$json    = $this->get_json_for_post( $post_id );
		$this->assertEquals( 'body', $json['components'][2]['role'] );
		$this->assertEquals( 'html', $json['components'][2]['format'] );
		$this->assertEquals( '<p>Lorem ipsum. <a href="https://www.wordpress.org">Dolor sit amet.</a></p>', $json['components'][2]['text'] );
		$this->assertEquals( 'body', $json['components'][3]['role'] );
		$this->assertEquals( 'html', $json['components'][3]['format'] );
		$this->assertEquals( '<pre>Preformatted text.</pre>', $json['components'][3]['text'] );
		$this->assertEquals( 'body', $json['components'][4]['role'] );
		$this->assertEquals( 'html', $json['components'][4]['format'] );
		$this->assertEquals( '<p>Testing a <code>code sample</code>.</p>', $json['components'][4]['text'] );
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
	 */
	public function test_filter() {
		add_filter( 'apple_news_body_json', [ $this, 'filter_apple_news_body_json' ] );

		// Create a test post and get JSON for it.
		$post_id = self::factory()->post->create();
		$json    = $this->get_json_for_post( $post_id );
		$this->assertEquals( 'body', $json['components'][2]['role'] );
		$this->assertEquals( 'fancy-body', $json['components'][2]['textStyle'] );

		// Teardown.
		remove_filter( 'apple_news_body_json', [ $this, 'filter_apple_news_body_json' ] );
	}

	/**
	 * Test the `apple_news_body_html_enabled` filter.
	 */
	public function test_filter_html() {
		// Test before filter.
		$post_id = self::factory()->post->create( [ 'post_content' => 'Test content.' ] );
		$json    = $this->get_json_for_post( $post_id );
		$this->assertEquals( 'body', $json['components'][2]['role'] );
		$this->assertEquals( 'html', $json['components'][2]['format'] );
		$this->assertEquals( '<p>Test content.</p>', $json['components'][2]['text'] );


		// Add filter and test to ensure HTML mode is not used.
		add_filter( 'apple_news_body_html_enabled', [ $this, 'filter_apple_news_body_html_enabled' ] );
		$json    = $this->get_json_for_post( $post_id );
		$this->assertEquals( 'body', $json['components'][2]['role'] );
		$this->assertEquals( 'markdown', $json['components'][2]['format'] );
		$this->assertEquals( 'Test content.', $json['components'][2]['text'] );
		remove_filter( 'apple_news_body_html_enabled', [ $this, 'filter_apple_news_body_html_enabled' ] );
	}

	/**
	 * Tests body settings.
	 */
	public function test_settings() {
		$this->set_theme_settings(
			[
				'body_font'                      => 'AmericanTypewriter',
				'body_size'                      => 20,
				'body_color'                     => '#abcdef',
				'body_color_dark'                => '#bcdef0',
				'body_link_color'                => '#fedcba',
				'body_link_color_dark'           => '#edcba0',
				'body_line_height'               => 28,
				'body_tracking'                  => 50,
				'dropcap_background_color'       => '#abcabc',
				'dropcap_background_color_dark'  => '#bcabc0',
				'dropcap_color'                  => '#defdef',
				'dropcap_color_dark'             => '#efdef0',
				'dropcap_font'                   => 'AmericanTypewriter-Bold',
				'dropcap_number_of_characters'   => 15,
				'dropcap_number_of_lines'        => 10,
				'dropcap_number_of_raised_lines' => 5,
				'dropcap_padding'                => 20,
			]
		);
		$content = <<<HTML
<!-- wp:paragraph -->
<p>Paragraph 1.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Paragraph 2.</p>
<!-- /wp:paragraph -->
HTML;
		$post_id = self::factory()->post->create( [ 'post_content' => $content ] );
		$json    = $this->get_json_for_post( $post_id );

		// Validate body settings in generated JSON.
		$this->assertEquals( 'AmericanTypewriter', $json['componentTextStyles']['default-body']['fontName'] );
		$this->assertEquals( 20, $json['componentTextStyles']['default-body']['fontSize'] );
		$this->assertEquals( '#abcdef', $json['componentTextStyles']['default-body']['textColor'] );
		$this->assertEquals( '#fedcba', $json['componentTextStyles']['default-body']['linkStyle']['textColor'] );
		$this->assertEquals( 28, $json['componentTextStyles']['default-body']['lineHeight'] );
		$this->assertEquals( 0.5, $json['componentTextStyles']['default-body']['tracking'] );
		$this->assertEquals( '#bcdef0', $json['componentTextStyles']['default-body']['conditional']['textColor'] );
		$this->assertEquals( '#edcba0', $json['componentTextStyles']['default-body']['conditional']['linkStyle']['textColor'] );
		$this->assertEquals( '#abcabc', $json['componentTextStyles']['dropcapBodyStyle']['dropCapStyle']['backgroundColor'] );
		$this->assertEquals( '#defdef', $json['componentTextStyles']['dropcapBodyStyle']['dropCapStyle']['textColor'] );
		$this->assertEquals( 'AmericanTypewriter-Bold', $json['componentTextStyles']['dropcapBodyStyle']['dropCapStyle']['fontName'] );
		$this->assertEquals( 15, $json['componentTextStyles']['dropcapBodyStyle']['dropCapStyle']['numberOfCharacters'] );
		$this->assertEquals( 10, $json['componentTextStyles']['dropcapBodyStyle']['dropCapStyle']['numberOfLines'] );
		$this->assertEquals( 5, $json['componentTextStyles']['dropcapBodyStyle']['dropCapStyle']['numberOfRaisedLines'] );
		$this->assertEquals( 20, $json['componentTextStyles']['dropcapBodyStyle']['dropCapStyle']['padding'] );
		$this->assertEquals( '#bcabc0', $json['componentTextStyles']['dropcapBodyStyle']['conditional']['dropCapStyle']['backgroundColor'] );
		$this->assertEquals( '#efdef0', $json['componentTextStyles']['dropcapBodyStyle']['conditional']['dropCapStyle']['textColor'] );
		$this->assertEquals( '#bcdef0', $json['componentTextStyles']['dropcapBodyStyle']['conditional']['textColor'] );
		$this->assertEquals( '#edcba0', $json['componentTextStyles']['dropcapBodyStyle']['conditional']['linkStyle']['textColor'] );
	}

	/**
	 * Tests 0 values in tokens.
	 */
	public function test_settings_zero_value_in_token() {
		$this->set_theme_settings( [ 'body_line_height' => 0 ] );
		$content = <<<HTML
<!-- wp:paragraph -->
<p>Paragraph 1.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Paragraph 2.</p>
<!-- /wp:paragraph -->
HTML;
		$post_id = self::factory()->post->create( [ 'post_content' => $content ] );
		$json    = $this->get_json_for_post( $post_id );
		$this->assertEquals( 0, $json['componentTextStyles']['default-body']['lineHeight'] );
	}

	/**
	 * Test the setting to disable the initial dropcap.
	 */
	public function test_without_dropcap() {
		$this->set_theme_settings( [ 'initial_dropcap' => 'no' ] );
		$content = <<<HTML
<!-- wp:paragraph -->
<p>Paragraph 1.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Paragraph 2.</p>
<!-- /wp:paragraph -->
HTML;
		$post_id = self::factory()->post->create( [ 'post_content' => $content ] );
		$json    = $this->get_json_for_post( $post_id );
		$this->assertEquals( '<p>Paragraph 1.</p>', $json['components'][2]['text'] );
		$this->assertEquals( 'default-body', $json['components'][2]['textStyle'] );
		$this->assertEquals( '<p>Paragraph 2.</p>', $json['components'][3]['text'] );
		$this->assertEquals( 'default-body', $json['components'][3]['textStyle'] );
	}
}
