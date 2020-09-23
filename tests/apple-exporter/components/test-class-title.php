<?php
/**
 * Publish to Apple News tests: Title_Test class
 *
 * @package Apple_News
 * @subpackage Tests
 */

use Apple_Exporter\Components\Title;

/**
 * A class to test the behavior of the
 * Apple_Exporter\Components\Title class.
 *
 * @package Apple_News
 * @subpackage Tests
 */
class Title_Test extends Component_TestCase {

	/**
	 * Tests a basic build.
	 */
	public function testBuildingRemovesTags() {
		$body_component = new Title(
			'Example Title',
			$this->workspace,
			$this->settings,
			$this->styles,
			$this->layouts
		);

		$this->assertEquals(
			array(
				'role' => 'title',
				'text' => 'Example Title',
				'format' => 'html',
				'textStyle' => 'default-title',
				'layout' => 'title-layout',
		 	),
			$body_component->to_array()
		);
	}

	/**
	 * Tests the behavior of the apple_news_title_json filter.
	 */
	public function testFilter() {
		$body_component = new Title(
			'Example Title',
			$this->workspace,
			$this->settings,
			$this->styles,
			$this->layouts
		);

		add_filter(
			'apple_news_title_json',
			function ( $json ) {
				$json['textStyle'] = 'fancy-title';
				return $json;
			}
		);

		$this->assertEquals(
			array(
				'role' => 'title',
				'text' => 'Example Title',
				'format' => 'html',
				'textStyle' => 'fancy-title',
				'layout' => 'title-layout',
		 	),
			$body_component->to_array()
		);
	}
}
