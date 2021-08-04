<?php
/**
 * Publish to Apple News tests: Heading_Test class
 *
 * @package Apple_News
 * @subpackage Tests
 */

use Apple_Exporter\Theme;

/**
 * A class to test the behavior of the Apple_Exporter\Components\Heading class.
 *
 * @package Apple_News
 * @subpackage Tests
 */
class Heading_Test extends Apple_News_Testcase {

	/**
	 * A data provider for the test_settings function.
	 *
	 * @return array An array of arrays representing function arguments.
	 */
	public function data_headings() {
		return [ [ 1 ], [ 2 ], [ 3 ], [ 4 ], [ 5 ], [ 6 ] ];
	}

	/**
	 * A filter function to modify the text style in the generated JSON.
	 *
	 * @param array $json The JSON array to modify.
	 *
	 * @return array The modified JSON.
	 */
	public function filter_apple_news_heading_json( $json ) {
		$json['layout'] = 'fancy-layout';

		return $json;
	}

	/**
	 * Test the `apple_news_heading_json` filter.
	 */
	public function test_filter() {
		add_filter( 'apple_news_heading_json', [ $this, 'filter_apple_news_heading_json' ] );

		// Create a test post and get JSON for it.
		$content = <<<HTML
<!-- wp:heading -->
<h2>Heading Level 2</h2>
<!-- /wp:heading -->
HTML;
		$post_id = self::factory()->post->create( [ 'post_content' => $content ] );
		$json    = $this->get_json_for_post( $post_id );
		$this->assertEquals( 'heading2', $json['components'][2]['role'] );
		$this->assertEquals( 'fancy-layout', $json['components'][2]['layout'] );

		// Teardown.
		remove_filter( 'apple_news_heading_json', [ $this, 'filter_apple_news_heading_json' ] );
	}

	/**
	 * Ensures HTML is allowed in headings.
	 */
	public function test_html_in_headings() {
		$content = <<<HTML
<!-- wp:heading -->
<h2>Heading <strong>Level</strong> 2</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Lorem ipsum dolor sit amet.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h3>Heading <em>Level</em> 3</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Adipiscing dolor sit.</p>
<!-- /wp:paragraph -->
HTML;
		$post_id = self::factory()->post->create( [ 'post_content' => $content ] );
		$json    = $this->get_json_for_post( $post_id );
		$this->assertEquals( 'heading2', $json['components'][2]['role'] );
		$this->assertEquals( 'Heading <strong>Level</strong> 2', $json['components'][2]['text'] );
		$this->assertEquals( 'heading3', $json['components'][4]['role'] );
		$this->assertEquals( 'Heading <em>Level</em> 3', $json['components'][4]['text'] );
	}

	/**
	 * Tests image splitting where the image is wrapped in a link.
	 */
	public function test_image_splitting_with_link() {
		$content = <<<HTML
<!-- wp:heading -->
<h2><a href="https://www.google.com/"><img src="/example-image.jpg" /></a></h2>
<!-- /wp:heading -->
HTML;
		$post_id = self::factory()->post->create( [ 'post_content' => $content ] );
		$cover   = $this->get_new_attachment();
		set_post_thumbnail( $post_id, $cover );
		$json = $this->get_json_for_post( $post_id );

		// Validate image split in generated JSON.
		$this->assertEquals( 'photo', $json['components'][1]['components'][2]['role'] );
		$this->assertEquals( 'http://example.org/example-image.jpg', $json['components'][1]['components'][2]['URL'] );
	}

	/**
	 * Ensures that headings are produced from heading tags.
	 *
	 * @dataProvider data_headings
	 *
	 * @param int $level Heading level. 1-6.
	 */
	public function test_render( $level ) {
		$content = <<<HTML
<!-- wp:heading -->
<h{$level}>Heading Level {$level}</h>
<!-- /wp:heading -->
HTML;
		$post_id = self::factory()->post->create( [ 'post_content' => $content ] );
		$json    = $this->get_json_for_post( $post_id );
		$this->assertEquals( 'html', $json['components'][2]['format'] );
		$this->assertEquals( 'heading' . $level, $json['components'][2]['role'] );
		$this->assertEquals( 'Heading Level ' . $level, $json['components'][2]['text'] );
	}

	/**
	 * Tests settings.
	 *
	 * @dataProvider data_headings
	 *
	 * @param int $level Heading level. 1-6.
	 */
	public function test_settings( $level ) {
		$this->set_theme_settings(
			[
				'header' . $level . '_font'        => 'AmericanTypewriter',
				'header' . $level . '_size'        => 12,
				'header' . $level . '_color'       => '#abcdef',
				'header' . $level . '_color_dark'  => '#fedcba',
				'header' . $level . '_line_height' => 34,
				'header' . $level . '_tracking'    => 56,
			]
		);
		$content = <<<HTML
<!-- wp:heading -->
<h{$level}>Heading Level {$level}</h2>
<!-- /wp:heading -->
HTML;
		$post_id = self::factory()->post->create( [ 'post_content' => $content ] );
		$json    = $this->get_json_for_post( $post_id );
		$this->assertEquals( 'AmericanTypewriter', $json['componentTextStyles']['default-heading-' . $level]['fontName'] );
		$this->assertEquals( 12, $json['componentTextStyles']['default-heading-' . $level]['fontSize'] );
		$this->assertEquals( '#abcdef', $json['componentTextStyles']['default-heading-' . $level]['textColor'] );
		$this->assertEquals( 34, $json['componentTextStyles']['default-heading-' . $level]['lineHeight'] );
		$this->assertEquals( 0.56, $json['componentTextStyles']['default-heading-' . $level]['tracking'] );
		$this->assertEquals( '#fedcba', $json['componentTextStyles']['default-heading-' . $level]['conditional']['textColor'] );
	}

	/**
	 * Tests the function to migrate legacy header settings.
	 *
	 * @see Apple_News::migrate_header_settings()
	 */
	public function test_settings_migration() {
		// Set legacy settings to test migration.
		$wp_settings = [
			'header_color'       => '#abcdef',
			'header_font'        => 'AmericanTypewriter',
			'header_line_height' => 128,
		];
		update_option( Apple_News::$option_name, $wp_settings );

		// Delete all themes to force recreation.
		$themes = Theme::get_registry();
		foreach ( $themes as $theme_name ) {
			$theme = new Theme();
			$theme->set_name( $theme_name );
			$theme->delete();
		}

		// Delete the active theme by force.
		$active_theme = Theme::get_active_theme_name();
		$theme_key = Theme::theme_key( $active_theme );
		delete_option( $theme_key );
		delete_option( Theme::ACTIVE_KEY );

		// Run legacy settings through migrate script.
		$apple_news = new Apple_News();
		$apple_news->upgrade_to_1_3_0();

		// Ensure legacy settings have been stripped.
		$settings = get_option( Apple_News::$option_name );
		$this->assertTrue( empty( $settings['header_color'] ) );
		$this->assertTrue( empty( $settings['header_font'] ) );
		$this->assertTrue( empty( $settings['header_line_height'] ) );

		// Ensure legacy settings were applied to new values.
		$theme = new Theme();
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
}
