<?php

require_once __DIR__ . '/class-component-testcase.php';

use \Exporter\Components\Caption as Caption;

class Caption_Test extends Component_TestCase {

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

