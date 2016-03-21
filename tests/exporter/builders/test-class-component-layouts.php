<?php

use \Exporter\Exporter_Content as Exporter_Content;
use \Exporter\Settings as Settings;
use \Exporter\Builders\Component_Layouts as Component_Layouts;
use \Exporter\Components\Component as Component;

class Component_Layouts_Test extends PHPUnit_Framework_TestCase {

	protected $prophet;

	protected function setup() {
		$this->prophet  = new \Prophecy\Prophet;
		$this->settings = new Settings();
		$this->content  = new Exporter_Content( 1, 'My Title', '<p>Hello, World!</p>' );
	}

	protected function tearDown() {
		$this->prophet->checkPredictions();
	}

	public function testRegisterLayout() {
		$layouts = new Component_Layouts( $this->content, $this->settings );
		$layouts->register_layout( 'l1', 'val1' );
		$layouts->register_layout( 'l2', 'val2' );
		$result = $layouts->to_array();

		$this->assertEquals( 2, count( $result ) );
		$this->assertEquals( 'val1', $result[ 'l1' ] );
		$this->assertEquals( 'val2', $result[ 'l2' ] );
	}

	public function testLeftLayoutGetsAdded() {
		$layouts = new Component_Layouts( $this->content, $this->settings );

		$this->assertFalse( array_key_exists( 'anchor-layout-left', $layouts->to_array() ) );

		$component = $this->prophet->prophesize( '\Exporter\Components\Component' );
		$component->get_anchor_position()
			->willReturn( Component::ANCHOR_LEFT )
			->shouldBeCalled();
		$component->is_anchor_target()
			->willReturn( false )
			->shouldBeCalled();
		$component->set_json( 'layout', 'anchor-layout-left' )->shouldBeCalled();
		$component->set_json( 'animation', array(
			'type'             => 'fade_in',
			'userControllable' => true,
			'initialAlpha'     => 0.0,
		) )->shouldBeCalled();

		$layouts->set_anchor_layout_for( $component->reveal() );

		$this->assertTrue( array_key_exists( 'anchor-layout-left', $layouts->to_array() ) );
	}

}
