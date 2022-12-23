<?php
/**
 * Publish to Apple News tests: Apple_News_Link_Button_Test class
 *
 * @package Apple_News
 * @subpackage Tests
 */

use Apple_Exporter\Components\Link_Button;

/**
 * A class to test the behavior of the
 * Apple_Exporter\Components\Link_Button class.
 *
 * @package Apple_News
 * @subpackage Tests
 */
class Apple_News_Link_Button_Test extends Apple_News_Component_TestCase {

	/**
	 * Holds a WP_Post object containing test data.
	 *
	 * @var WP_Post
	 */
	public static $test_post;

	/**
	 * A fixture containing operations to be run before the test suite.
	 */
	public static function setUpBeforeClass(): void {
		parent::setUpBeforeClass();
		self::$test_post = self::factory()->post->create_and_get(
			[
				'post_status' => 'publish',
				'post_title'  => 'test-post',
			]
		);
	}

	/**
	 * A fixture containing operations to be run before each test.
	 */
	public function setUp(): void {
		parent::setUp();
		global $post;
		$post = self::$test_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
	}

	/**
	 * A data provider for the node matches test.
	 *
	 * @return array An array of function arguments.
	 */
	public function data_provider_node_matches() {
		return [
			// A bare link should not match.
			[
				'<a href="https://www.example.org/">Test Button</a>',
				false,
			],
			// A button link with the button class but no href should not match.
			[
				'<a class="wp-block-button__link">Test Button</a>',
				false,
			],
			// A button link with the button class and an empty href should not match.
			[
				'<a class="wp-block-button__link" href="">Test Button</a>',
				false,
			],
			// A button link with the button class and an href but no button text should not match.
			[
				'<a class="wp-block-button__link" href="https://www.example.org/"></a>',
				false,
			],
			// A button link with the button class should match.
			[
				'<a class="wp-block-button__link" href="https://www.example.org/">Test Button</a>',
				true,
			],
		];
	}

	/**
	 * A data provider for the testTransform function.
	 *
	 * @return array An array of function arguments for the test function.
	 */
	public function data_provider_transform() {
		return [
			// Test a normal button.
			[
				'<a class="wp-block-button__link" href="https://www.example.org/">Test Button</a>',
				[
					'role'      => 'link_button',
					'text'      => 'Test Button',
					'URL'       => 'https://www.example.org/',
					'style'     => 'default-link-button',
					'layout'    => 'link-button-layout',
					'textStyle' => 'default-link-button-text-style',
				],
			],
			// Test a root-relative URL.
			[
				'<a class="wp-block-button__link" href="/test">Test Button</a>',
				[
					'role'      => 'link_button',
					'text'      => 'Test Button',
					'URL'       => 'https://www.example.org/test',
					'style'     => 'default-link-button',
					'layout'    => 'link-button-layout',
					'textStyle' => 'default-link-button-text-style',
				],
			],
			// Test an anchor button.
			[
				'<a class="wp-block-button__link" href="#test">Test Button</a>',
				[
					'role'      => 'link_button',
					'text'      => 'Test Button',
					'URL'       => 'https://www.example.org/test-post/#test',
					'style'     => 'default-link-button',
					'layout'    => 'link-button-layout',
					'textStyle' => 'default-link-button-text-style',
				],
			],
		];
	}

	/**
	 * A data provider for the test_styles function.
	 *
	 * @return array An array of function arguments for the test function.
	 */
	public function data_provider_styles() {
		return [
			// Test button styles.
			[
				<<<HTML
<a class="wp-block-button__link" href="https://www.example.org/">Test Button</a>
HTML
				,
				[
					'background_color'     => '#ffffff',
					'text_color'           => '#000000',
					'font_face'            => 'HelveticaNeue-Medium',
					'font_size'            => 15,
					'border_color'         => '#000000',
					'border_radius'        => 18,
					'border_width'         => 1,
					'horizontal_alignment' => 'center',
				],
			],
		];
	}

	/**
	 * Tests the behavior of node_matches to ensure that the scope of
	 * node matching is sufficiently narrow.
	 *
	 * @param string $html    The HTML to test.
	 * @param bool   $matches Whether the node matches or not.
	 *
	 * @dataProvider data_provider_node_matches
	 */
	public function test_node_matches( $html, $matches ) {
		$node   = self::build_node( $html );
		$result = Link_Button::node_matches( $node );
		if ( $matches ) {
			$this->assertNotNull( $result );
		} else {
			$this->assertNull( $result );
		}
	}

	/**
	 * Tests the transformation process from a button to a Link_Button component.
	 *
	 * @param string $html     The HTML to transform into a Link Button.
	 * @param array  $expected The expected result from the component's to_array method.
	 *
	 * @dataProvider data_provider_transform
	 */
	public function test_transform( $html, $expected ) {
		// Setup.
		$component = new Link_Button(
			$html,
			$this->workspace,
			$this->settings,
			$this->styles,
			$this->layouts,
			null,
			$this->component_styles
		);
		$result    = $component->to_array();
		$this->assertEquals( $expected, $result );
	}
}
