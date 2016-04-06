<?php

use Apple_Exporter\Exporter as Exporter;
use Apple_Exporter\Exporter_Content as Exporter_Content;
use Apple_Exporter\Settings as Settings;
use Apple_Exporter\Builders\Layout as Layout;

class Layout_Test extends WP_UnitTestCase {

	public function setup() {
		$this->settings = new Settings();
		$this->content  = new Exporter_Content( 1, 'My Title', '<p>Hello, World!</p>' );
	}

	public function testRegisterLayout() {
		$this->settings->set( 'layout_margin', 123 );
		$this->settings->set( 'layout_gutter', 222 );
		$layout = new Layout( $this->content, $this->settings );
		$result = $layout->to_array();

		$this->assertEquals( $this->settings->get( 'layout_columns' ), $result[ 'columns' ] );
		$this->assertEquals( $this->settings->get( 'layout_width' ), $result[ 'width' ] );
		$this->assertEquals( 123, $result[ 'margin' ] );
		$this->assertEquals( 222, $result[ 'gutter' ] );
	}
}
