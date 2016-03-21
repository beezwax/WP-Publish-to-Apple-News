<?php

use \Exporter\Exporter_Content as Exporter_Content;
use \Exporter\Settings as Settings;
use \Exporter\Builders\Metadata as Metadata;

class Metadata_Test extends PHPUnit_Framework_TestCase {

	protected function setup() {
		$this->settings = new Settings();
	}

	public function testNoIntroNoCover() {
		$content = new Exporter_Content( 1, 'My Title', '<p>Hello, World!</p>' );
		$builder = new Metadata( $content, $this->settings );
		$result  = $builder->to_array();
		$this->assertEquals( 0, count( $result ) );
	}

	public function testIntro() {
		$content = new Exporter_Content( 1, 'My Title', '<p>Hello, World!</p>', 'This is an intro.' );
		$builder = new Metadata( $content, $this->settings );
		$result  = $builder->to_array();

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 'This is an intro.', $result[ 'excerpt' ] );
	}

	public function testCover() {
		$content = new Exporter_Content( 1, 'My Title', '<p>Hello, World!</p>', null, '/etc/somefile.jpg' );
		$builder = new Metadata( $content, $this->settings );
		$result  = $builder->to_array();

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 'bundle://somefile.jpg', $result[ 'thumbnailURL' ] );
	}

	public function testIntroAndCover() {
		$content = new Exporter_Content( 1, 'My Title', '<p>Hello, World!</p>', 'This is an intro.', '/etc/somefile.jpg' );
		$builder = new Metadata( $content, $this->settings );
		$result  = $builder->to_array();

		$this->assertEquals( 2, count( $result ) );
		$this->assertEquals( 'This is an intro.', $result[ 'excerpt' ] );
		$this->assertEquals( 'bundle://somefile.jpg', $result[ 'thumbnailURL' ] );
	}

}
