<?php

use \Exporter\Exporter_Content as Exporter_Content;
use \Exporter\Settings as Settings;
use \Exporter\Builders\Component_Layouts as Component_Layouts;
use \Exporter\Builders\Component_Text_Styles as Component_Text_Styles;

abstract class Component_TestCase extends PHPUnit_Framework_TestCase {

	protected $prophet;

	protected function setup() {
		$this->prophet  = new \Prophecy\Prophet;
		$this->settings = new Settings();
		$this->content  = new Exporter_Content( 1, 'My Title', '<p>Hello, World!</p>' );
		$this->styles   = new Component_Text_Styles( $this->content, $this->settings );
		$this->layouts  = new Component_Layouts( $this->content, $this->settings );
	}

	protected function tearDown() {
		$this->prophet->checkPredictions();
	}

}
