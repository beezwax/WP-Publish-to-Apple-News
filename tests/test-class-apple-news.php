<?php

use \Apple_Exporter\Settings as Settings;

class Apple_News_Test extends WP_UnitTestCase {

	public function setup() {
		parent::setup();

		$this->settings = new Settings();
	}

	public function testGetFilename() {}

	public function testVersion() {}

	public function testMigrateSettings() {}

	public function testSupportInfo() {}
}
