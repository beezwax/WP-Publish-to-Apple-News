<?php

use Apple_Exporter\Exporter_Content as Exporter_Content;
use Apple_Exporter\Settings as Settings;
use Apple_Exporter\Builders\Advertising_Settings as Advertising_Settings;

class Test_Class_Advertising_Settings extends PHPUnit_Framework_TestCase {

	protected function setup() {
		$this->settings = new Settings();
		$this->content = new Exporter_Content( 1, 'My Title', '<p>Hello, World!</p>' );
	}

	public function testDefaultAdSettings() {
		$builder = new Advertising_Settings( $this->content, $this->settings );
		$result  = $builder->to_array();
		$this->assertEquals( 2, count( $result ) );
		$this->assertEquals( 1, $result['frequency'] );
		$this->assertEquals( 1, count( $result['layout'] ) );
		$this->assertEquals( 15, $result['layout']['margin']['top'] );
		$this->assertEquals( 15, $result['layout']['margin']['bottom'] );
	}

	public function testNoAds() {
		$this->settings->set( 'enable_advertisement', 'no' );
		$builder = new Advertising_Settings( $this->content, $this->settings );
		$result  = $builder->to_array();
		$this->assertEquals( 0, count( $result ) );
	}

	public function testCustomAdFrequency() {
		$this->settings->set( 'ad_frequency', '5' );
		$builder = new Advertising_Settings( $this->content, $this->settings );
		$result  = $builder->to_array();
		$this->assertEquals( 2, count( $result ) );
		$this->assertEquals( 5, $result['frequency'] );
		$this->assertEquals( 1, count( $result['layout'] ) );
		$this->assertEquals( 15, $result['layout']['margin']['top'] );
		$this->assertEquals( 15, $result['layout']['margin']['bottom'] );
	}

	public function testCustomAdMargin() {
		$this->settings->set( 'ad_margin', '20' );
		$builder = new Advertising_Settings( $this->content, $this->settings );
		$result  = $builder->to_array();
		$this->assertEquals( 2, count( $result ) );
		$this->assertEquals( 1, $result['frequency'] );
		$this->assertEquals( 1, count( $result['layout'] ) );
		$this->assertEquals( 20, $result['layout']['margin']['top'] );
		$this->assertEquals( 20, $result['layout']['margin']['bottom'] );
	}
}
