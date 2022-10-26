<?php
/**
 * Publish to Apple News Tests: Apple_News_Component_Layouts_Test class
 *
 * Contains a class to test the functionality of the Apple_Exporter\Builders\Component_Layouts class.
 *
 * @package Apple_News
 * @subpackage Tests
 */

use Apple_Exporter\Builders\Component_Layouts;
use Apple_Exporter\Components\Component;

/**
 * A class to test the behavior of the Apple_Exporter\Builders\Component_Layouts class.
 *
 * @package Apple_News
 * @subpackage Tests
 */
class Apple_News_Component_Layouts_Test extends Apple_News_Testcase {

	/**
	 * Tests the behavior of registering layouts.
	 */
	public function test_register_layout() {
		$layouts = new Component_Layouts( $this->content, $this->settings );
		$layouts->register_layout( 'l1', 'val1' );
		$layouts->register_layout( 'l2', 'val2' );
		$result = $layouts->to_array();

		$this->assertEquals( 2, count( $result ) );
		$this->assertEquals( 'val1', $result['l1'] );
		$this->assertEquals( 'val2', $result['l2'] );
	}

	/**
	 * Tests the behavior of anchor layout left.
	 */
	public function test_left_layout_gets_added() {
		$layouts = new Component_Layouts( $this->content, $this->settings );

		$this->assertFalse( array_key_exists( 'anchor-layout-left', $layouts->to_array() ) );

		$component = $this->prophet->prophesize( '\Apple_Exporter\Components\Component' );
		$component->get_anchor_position()
			->willReturn( Component::ANCHOR_LEFT )
			->shouldBeCalled();
		$component->is_anchor_target()
			->willReturn( false )
			->shouldBeCalled();
		$component->set_json( 'layout', 'anchor-layout-left' )->shouldBeCalled();

		$layouts->set_anchor_layout_for( $component->reveal() );

		$this->assertTrue( array_key_exists( 'anchor-layout-left', $layouts->to_array() ) );
	}
}
