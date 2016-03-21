<?php

use \Exporter\Component_Factory as Component_Factory;
use \Exporter\Exporter_Content as Exporter_Content;
use \Exporter\Settings as Settings;
use \Exporter\Builders\Components as Components;
use \Exporter\Builders\Component_Layouts as Component_Layouts;
use \Exporter\Builders\Component_Text_Styles as Component_Text_Styles;

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

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 'body', $result[0]['role'] );
	}

}
