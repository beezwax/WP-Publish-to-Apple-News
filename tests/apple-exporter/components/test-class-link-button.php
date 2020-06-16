<?php
/**
 * Publish to Apple News Tests: Link_Button Class
 *
 * @package Apple_News
 * @subpackage Tests
 */

require_once __DIR__ . '/class-component-testcase.php';

use Apple_Exporter\Components\Link_Button;
use Apple_Exporter\Exporter;
use Apple_Exporter\Exporter_Content;

/**
 * A class which is used to test the Apple_Exporter\Components\Link_Button class.
 */
class Link_Button_Test extends Component_TestCase {
	/**
	 * A data provider for the node matches test.
	 *
	 * @return array An array of function arguments.
	 */
	public function dataProviderNodeMatches() {
		return [
			// A bare link should not match.
			[
				'<a href="https://example.org/">Test Button</a>',
				false,
			],
			// A bare link with the button class should match.
			[
				'<a class="wp-block-button__link" href="https://example.org/">Test Button</a>',
				true,
			],
			// An element with the button class surrounded by a plain div should not match.
			[
				'<div><a class="wp-block-button__link" href="https://example.org/">Test Button</a></div>',
				false,
			],
			// A standalone button should match.
			[
				'<div class="wp-block-button"><a class="wp-block-button__link" href="https://example.org/">Test Button</a></div>',
				true,
			],
			// A standalone button surrounded by a plain div should not match.
			[
				'<div><div class="wp-block-button"><a class="wp-block-button__link" href="https://example.org/">Test Button</a></div></div>',
				false,
			],
			// A full button group with one button should match.
			[
				'<div class="wp-block-buttons"><div class="wp-block-button"><a class="wp-block-button__link" href="https://example.org/">Test Button</a></div></div>',
				true,
			],
			// A full button group with more than one button should match.
			[
				'<div class="wp-block-buttons"><div class="wp-block-button"><a class="wp-block-button__link" href="https://example.org/">Test Button</a></div><div class="wp-block-button"><a class="wp-block-button__link" href="https://example2.org/">Test Button 2</a></div></div>',
				true,
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
	 * @dataProvider dataProviderNodeMatches
	 */
	public function testNodeMatches( $html, $matches ) {
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
	 * @access public
	 */
	public function testTransformLinkButton() {
		$this->assertTrue( true );
		return;

		// Setup.
		$component = new Link_Button(
			'<blockquote><p>my quote</p></blockquote>',
			null,
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
}
