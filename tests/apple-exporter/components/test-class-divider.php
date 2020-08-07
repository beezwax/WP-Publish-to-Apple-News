<?php
/**
 * Publish to Apple News tests: Divider_Test class
 *
 * @package Apple_News
 * @subpackage Tests
 */

use Apple_Exporter\Components\Divider;

/**
 * A class to test the behavior of the
 * Apple_Exporter\Components\Divider class.
 *
 * @package Apple_News
 * @subpackage Tests
 */
class Divider_Test extends Component_TestCase {

	/**
	 * Ensures that an <hr/> tag gets converted to a Divider component.
	 */
	public function testBuildingRemovesTags() {
		$component = new Divider(
			'<hr/>',
			$this->workspace,
			$this->settings,
			$this->styles,
			$this->layouts
		);
		$result = $component->to_array();

		$this->assertEquals( 'divider', $result['role'] );
		$this->assertEquals( 'divider-layout', $result['layout'] );
		$this->assertNotNull( $result['stroke'] );
	}

	/**
	 * Tests the behavior of the apple_news_divider_json filter.
	 */
	public function testFilter() {
		$component = new Divider(
			'<hr/>',
			$this->workspace,
			$this->settings,
			$this->styles,
			$this->layouts
		);

		add_filter(
			'apple_news_divider_json',
			function( $json ) {
				$json['layout'] = 'fancy-layout';
				return $json;
			}
		);

		$result = $component->to_array();
		$this->assertEquals( 'fancy-layout', $result['layout'] );
	}
}
