<?php

use \Apple_Exporter\Component_Factory as Component_Factory;
use \Apple_Exporter\Exporter_Content as Exporter_Content;
use \Apple_Exporter\Settings as Settings;
use \Apple_Exporter\Builders\Components as Components;
use \Apple_Exporter\Builders\Component_Layouts as Component_Layouts;
use \Apple_Exporter\Builders\Component_Text_Styles as Component_Text_Styles;

class Component_Tests extends PHPUnit_Framework_TestCase {

	protected $prophet;

	protected function setup() {
		$this->prophet  = new \Prophecy\Prophet;
		$this->settings = new Settings();
		$this->content  = new Exporter_Content( 1, 'My Title', '<p>Hello, World!</p>' );
		$this->styles   = new Component_Text_Styles( $this->content, $this->settings );
		$this->layouts  = new Component_Layouts( $this->content, $this->settings );
		$workspace      = $this->prophet->prophesize( '\Exporter\Workspace' );

		Component_Factory::initialize( $workspace, $this->settings, $this->styles, $this->layouts );
	}

	protected function tearDown() {
		$this->prophet->checkPredictions();
	}

	public function testBuiltArray() {
		$this->settings->set( 'enable_advertisement', 'no' );
		$builder = new Components( $this->content, $this->settings );
		$result  = $builder->to_array();

		$this->assertEquals( 2, count( $result ) );
		$this->assertEquals( 'title', $result[0]['role'] );
		$this->assertEquals( 'body', $result[1]['role'] );
	}

}
