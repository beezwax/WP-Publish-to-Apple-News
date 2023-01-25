<?php
/**
 * Publish to Apple News Tests: Apple_News_Layout_Test class
 *
 * Contains a class to test the functionality of the Apple_Exporter\Builders\Layout class.
 *
 * @package Apple_News
 * @subpackage Tests
 */

use Apple_Exporter\Builders\Layout;
use Apple_Exporter\Theme;

/**
 * A class to test the behavior of the Apple_Exporter\Builders\Layout class.
 *
 * @package Apple_News
 * @subpackage Tests
 */
class Apple_News_Layout_Test extends Apple_News_Testcase {

	/**
	 * Tests the behavior of registering a layout.
	 */
	public function test_register_layout() {
		$theme                     = Theme::get_used();
		$settings                  = $theme->all_settings();
		$settings['layout_margin'] = 123;
		$settings['layout_gutter'] = 222;
		$settings['layout_width']  = 768;
		$theme->load( $settings );
		$this->assertTrue( $theme->save() );
		$layout = new Layout( $this->content, $this->settings );
		$result = $layout->to_array();

		$this->assertEquals( $theme->get_layout_columns(), $result['columns'] );
		$this->assertEquals( 768, $result['width'] );
		$this->assertEquals( 123, $result['margin'] );
		$this->assertEquals( 222, $result['gutter'] );
	}

	/**
	 * Test column override functionality.
	 */
	public function test_column_override() {
		$theme                     = Theme::get_used();
		$settings                  = $theme->all_settings();
		$settings['layout_margin'] = 123;
		$theme->load( $settings );
		$this->assertTrue( $theme->save() );
		$layout = new Layout( $this->content, $this->settings );
		$result = $layout->to_array();

		// Check default behavior.
		$this->assertEquals( $theme->get_layout_columns(), $result['columns'] );

		$settings['layout_columns_override'] = 6;
		$theme->load( $settings );
		$this->assertTrue( $theme->save() );
		$layout = new Layout( $this->content, $this->settings );
		$result = $layout->to_array();

		// Confirm override works after theme value change.
		$this->assertEquals( 6, $theme->get_layout_columns() );
		$this->assertEquals( 6, $result['columns'] );
	}
}
