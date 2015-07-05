<?php

use \Exporter\Components\Caption as Caption;
use \Exporter\Settings as Settings;
use \Exporter\Component_Layouts as Component_Layouts;
use \Exporter\Component_Styles as Component_Styles;

class Caption_Test extends PHPUnit_Framework_TestCase {

	public function setup() {
		$this->settings = new Settings();
		$this->styles   = new Component_Styles();
		$this->layouts  = new Component_Layouts( $this->settings );
	}

	public function testSingleCaption() {
		$component = new Caption( '<figcaption>my text</figcaption>', null,
			$this->settings, $this->styles, $this->layouts );

		$this->assertEquals(
			array(
				'role' => 'caption',
				'text' => 'my text',
		 	),
			$component->to_array()
		);
	}

	private function build_node( $html ) {
		$dom = new DOMDocument( '1.0', 'utf-8' );
		libxml_use_internal_errors( true );
		$dom->loadHTML( $html );
		libxml_clear_errors( true );
		return $dom->getElementsByTagName( 'body' )->item( 0 )->childNodes->item( 0 );
	}

	public function testCompoundCaption() {
		$node = $this->build_node( '<figure><img src="example.jpg"/><figcaption>my text</figcaption></figure>' );
		$result = Caption::node_matches( $node );

		// Matched two elements, by shortname (img and caption).
		$this->assertTrue( array_key_exists( 'img', $result ) );
		$this->assertTrue( array_key_exists( 'caption', $result ) );
	}

}

