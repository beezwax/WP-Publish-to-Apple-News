<?php
/**
 * Publish to Apple News tests: Heading_Test class
 *
 * @package Apple_News
 * @subpackage Tests
 */

use Apple_Exporter\Components\Heading;
use Apple_Exporter\Exporter;
use Apple_Exporter\Exporter_Content;
use Apple_Exporter\Theme;

/**
 * A class to test the behavior of the
 * Apple_Exporter\Components\Heading class.
 *
 * @package Apple_News
 * @subpackage Tests
 */
class Heading_Test extends Component_TestCase {

	/**
	 * A data provider for the testSettings function.
	 *
	 * @return array An array of arrays representing function arguments.
	 */
	public function data_headings() {
		return [
			[ 1, 'AmericanTypewriter', 10, '#111111', 11, 1 ],
			[ 2, 'AmericanTypewriter', 20, '#222222', 22, 2 ],
			[ 3, 'AmericanTypewriter', 30, '#222222', 33, 3 ],
			[ 4, 'AmericanTypewriter', 40, '#222222', 44, 4 ],
			[ 5, 'AmericanTypewriter', 50, '#222222', 55, 5 ],
			[ 6, 'AmericanTypewriter', 60, '#222222', 66, 6 ],
		];
	}

	/**
	 * A data provider for the testDarkColors function.
	 *
	 * @return array An array of arrays representing function arguments.
	 */
	public function data_headings_dark_colors() {
		return [
			[ 1, '#111111' ],
			[ 2, '#222222' ],
			[ 3, '#333333' ],
			[ 4, '#444444' ],
			[ 5, '#555555' ],
			[ 6, '#666666' ],
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
	public function filter_apple_news_heading_json( $json ) {
		$json['format'] = 'none';

		return $json;
	}

	/**
	 * Test the `apple_news_heading_json` filter.
	 *
	 * @access public
	 */
	public function testFilter() {

		// Setup.
		$component = new Heading(
			'<h1>This is a heading</h1>',
			$this->workspace,
			$this->settings,
			$this->styles,
			$this->layouts
		);
		add_filter(
			'apple_news_heading_json',
			array( $this, 'filter_apple_news_heading_json' )
		);

		// Test.
		$json = $component->to_array();
		$this->assertEquals( 'none', $json['format'] );

		// Teardown.
		remove_filter(
			'apple_news_heading_json',
			array( $this, 'filter_apple_news_heading_json' )
		);
	}

	/**
	 * Tests image splitting where the image is wrapped in a link.
	 *
	 * @access public
	 */
	public function testImageSplittingWithLink() {

		// Setup.
		$content = <<<HTML
<h2><a href="https://www.google.com/"><img src="/example-image.jpg" /></a></h2>
HTML;
		$cover   = $this->get_new_attachment();
		$content = new Exporter_Content( 3, 'Title', $content, null, $cover );

		// Run the export.
		$exporter = new Exporter( $content, $this->workspace, $this->settings );
		$json = $exporter->export();
		$this->ensure_tokens_replaced( $json );
		$json = json_decode( $json, true );

		// Validate image split in generated JSON.
		$this->assertEquals(
			array(
				'role'   => 'photo',
				'URL'    => 'http://example.org/example-image.jpg',
				'layout' => 'full-width-image',
			),
			$json['components'][1]['components'][1]
		);
	}

	/**
	 * Ensures that headings are not produced from paragraphs.
	 *
	 * @access public
	 */
	public function testInvalidInput() {

		// Setup.
		$component = new Heading(
			'<p>This is not a heading</p>',
			$this->workspace,
			$this->settings,
			$this->styles,
			$this->layouts
		);

		// Test.
		$this->assertEquals(
			null,
			$component->to_array()
		);
	}

	/**
	 * Tests settings.
	 *
	 * @dataProvider data_headings
	 *
	 * @param int    $level       Heading level. 1-6.
	 * @param string $font        The font to use for the heading.
	 * @param int    $size        The font size.
	 * @param string $color       The hex color for the font.
	 * @param int    $line_height The line height for the text.
	 * @param int    $tracking    The tracking value for the text.
	 *
	 * @access public
	 */
	public function testSettings( $level, $font, $size, $color, $line_height, $tracking ) {

		// Setup.
		$content = new Exporter_Content(
			3,
			'Title',
			sprintf(
				'<h%d>Heading</h%d>',
				$level,
				$level
			)
		);

		// Set header settings.
		$this->set_theme_settings(
			[
				'header' . $level . '_font'        => $font,
				'header' . $level . '_size'        => $size,
				'header' . $level . '_color'       => $color,
				'header' . $level . '_line_height' => $line_height,
				'header' . $level . '_tracking'    => $tracking,
			]
		);

		// Run the export.
		$exporter = new Exporter( $content, $this->workspace, $this->settings );
		$json     = $exporter->export();
		$this->ensure_tokens_replaced( $json );
		$json = json_decode( $json, true );

		// Validate header settings in generated JSON.
		$this->assertEquals(
			$font,
			$json['componentTextStyles']['default-heading-' . $level]['fontName']
		);
		$this->assertEquals(
			$size,
			$json['componentTextStyles']['default-heading-' . $level]['fontSize']
		);
		$this->assertEquals(
			$color,
			$json['componentTextStyles']['default-heading-' . $level]['textColor']
		);
		$this->assertEquals(
			$line_height,
			$json['componentTextStyles']['default-heading-' . $level]['lineHeight']
		);
		$this->assertEquals(
			$tracking / 100,
			$json['componentTextStyles']['default-heading-' . $level]['tracking']
		);
		$this->assertFalse(
			isset( $json['componentTextStyles']['default-heading-' . $level]['conditional'] )
		);
	}

	/**
	 * Tests dark color settings.
	 *
	 * @dataProvider data_headings_dark_colors
	 *
	 * @param int    $level       Heading level. 1-6.
	 * @param string $color       The hex color for the font.
	 *
	 * @access public
	 */
	public function testDarkColors( $level,  $color ) {
		// Set header settings.
		$this->set_theme_settings(
			[
				'header' . $level . '_color_dark'       => $color,
			]
		);
		// Setup.
		$content = new Exporter_Content(
			3,
			'Title',
			sprintf(
				'<h%d>Heading</h%d>',
				$level,
				$level
			)
		);

		// Run the export.
		$exporter = new Exporter( $content, $this->workspace, $this->settings );
		$json     = $exporter->export();
		$this->ensure_tokens_replaced( $json );
		$json = json_decode( $json, true );

		// Validate header settings in generated JSON.
		$this->assertEquals(
			$color,
			$json['componentTextStyles']['default-heading-' . $level]['conditional']['textColor']
		);
	}

	/**
	 * Tests the function to migrate legacy header settings.
	 *
	 * @see Apple_News::migrate_header_settings()
	 *
	 * @access public
	 */
	public function testSettingsMigration() {

		// Test with default settings.
		$this->assertEmpty( $this->settings->header_color );
		$this->assertEmpty( $this->settings->header_font );
		$this->assertEmpty( $this->settings->header_line_height );

		// Set legacy settings to test migration.
		$wp_settings = array(
			'header_color' => '#abcdef',
			'header_font' => 'AmericanTypewriter',
			'header_line_height' => 128,
		);
		update_option( Apple_News::$option_name, $wp_settings );

		// Delete all themes to force recreation.
		$themes = Theme::get_registry();
		foreach ( $themes as $theme_name ) {
			$theme = new Theme;
			$theme->set_name( $theme_name );
			$theme->delete();
		}

		// Delete the active theme by force.
		$active_theme = Theme::get_active_theme_name();
		$theme_key = Theme::theme_key( $active_theme );
		delete_option( $theme_key );
		delete_option( Theme::ACTIVE_KEY );

		// Run legacy settings through migrate script.
		$apple_news = new Apple_News;
		$apple_news->upgrade_to_1_3_0();

		// Ensure legacy settings have been stripped.
		$settings = get_option( Apple_News::$option_name );
		$this->assertTrue( empty( $settings['header_color'] ) );
		$this->assertTrue( empty( $settings['header_font'] ) );
		$this->assertTrue( empty( $settings['header_line_height'] ) );

		// Ensure legacy settings were applied to new values.
		$theme = new Theme;
		$theme->set_name( Theme::get_active_theme_name() );
		$this->assertTrue( $theme->load() );
		$settings = $theme->all_settings();
		$this->assertEquals( '#abcdef', $settings['header1_color'] );
		$this->assertEquals( '#abcdef', $settings['header2_color'] );
		$this->assertEquals( '#abcdef', $settings['header3_color'] );
		$this->assertEquals( '#abcdef', $settings['header4_color'] );
		$this->assertEquals( '#abcdef', $settings['header5_color'] );
		$this->assertEquals( '#abcdef', $settings['header6_color'] );
		$this->assertEquals( 'AmericanTypewriter', $settings['header1_font'] );
		$this->assertEquals( 'AmericanTypewriter', $settings['header2_font'] );
		$this->assertEquals( 'AmericanTypewriter', $settings['header3_font'] );
		$this->assertEquals( 'AmericanTypewriter', $settings['header4_font'] );
		$this->assertEquals( 'AmericanTypewriter', $settings['header5_font'] );
		$this->assertEquals( 'AmericanTypewriter', $settings['header6_font'] );
		$this->assertEquals( 128, $settings['header1_line_height'] );
		$this->assertEquals( 128, $settings['header2_line_height'] );
		$this->assertEquals( 128, $settings['header3_line_height'] );
		$this->assertEquals( 128, $settings['header4_line_height'] );
		$this->assertEquals( 128, $settings['header5_line_height'] );
		$this->assertEquals( 128, $settings['header6_line_height'] );
	}

	/**
	 * Ensures that headings are produced from heading tags.
	 *
	 * @access public
	 */
	public function testValidInput() {

		// Setup.
		$component = new Heading(
			'<h1>This is a heading</h1>',
			$this->workspace,
			$this->settings,
			$this->styles,
			$this->layouts
		);
		$json = $component->to_array();

		// Test.
		$this->assertEquals( 'heading1', $json['role'] );
		$this->assertEquals( 'This is a heading', $json['text'] );
		$this->assertEquals( 'html', $json['format'] );
	}
}
