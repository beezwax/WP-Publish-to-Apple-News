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
		$theme = Theme::get_used();
		$theme->set_value( 'layout_margin', 123 );
		$theme->set_value( 'layout_gutter', 222 );
		$theme->set_value( 'layout_width', 768 );
		$this->assertTrue( $theme->save() );

		$post_id = self::factory()->post->create( [ 'post_content' => '' ] );
		$json    = $this->get_json_for_post( $post_id );

		$this->assertEquals( $theme->get_layout_columns(), $json['layout']['columns'] );
		$this->assertEquals( 768, $json['layout']['width'] );
		$this->assertEquals( 123, $json['layout']['margin'] );
		$this->assertEquals( 222, $json['layout']['gutter'] );
	}

	/**
	 * Test column override functionality.
	 */
	public function test_column_override() {
		$theme   = Theme::get_used();
		$post_id = self::factory()->post->create( [ 'post_content' => '' ] );
		$json    = $this->get_json_for_post( $post_id );

		// Check default, override-less behavior.
		$this->assertEquals( $theme->get_layout_columns(), $json['layout']['columns'] );

		$theme->set_value( 'layout_columns_override', 6 );
		$this->assertTrue( $theme->save() );
		$json = $this->get_json_for_post( $post_id );

		// Confirm override applies after 'layout_columns_override' theme value change.
		$this->assertEquals( 6, $theme->get_layout_columns() );
		$this->assertEquals( 6, $json['layout']['columns'] );
	}
}
