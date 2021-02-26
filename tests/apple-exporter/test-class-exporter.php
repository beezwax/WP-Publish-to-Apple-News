<?php

use Apple_Exporter\Exporter as Exporter;
use Prophecy\Argument;

class Exporter_Test extends WP_UnitTestCase {

	private $prophet;

	public function setup() {
		$this->prophet = new \Prophecy\Prophet;
	}

	public function tearDown() {
		$this->prophet->checkPredictions();
	}

	public function isValidJSON( $json ) {
		return ( null !== json_decode( $json ) );
	}

	/**
	 * Tests the functionality of the prepare_for_encoding function to ensure
	 * that unwanted characters are stripped.
	 */
	public function testPrepareForEncoding() {
		// Test UTF-8 characters with accents common in French.
		$test_content = 'Pondant à Noël — aÀâÂèÈéÉêÊëËîÎïÏôÔùÙûÛüÜÿŸçÇœŒ€æÆ';
		Exporter::prepare_for_encoding( $test_content );
		$this->assertEquals( 'Pondant à Noël — aÀâÂèÈéÉêÊëËîÎïÏôÔùÙûÛüÜÿŸçÇœŒ€æÆ', $test_content );

		// Test Unicode whitespace character removal.
		$test_content = json_decode( '"\u0020"' )
			. json_decode( '"\u00a0"' )
			. json_decode( '"\u2000"' )
			. json_decode( '"\u2001"' )
			. json_decode( '"\u2002"' )
			. json_decode( '"\u2003"' )
			. json_decode( '"\u2004"' )
			. json_decode( '"\u2005"' )
			. json_decode( '"\u2006"' )
			. json_decode( '"\u2007"' )
			. json_decode( '"\u2008"' )
			. json_decode( '"\u2009"' )
			. json_decode( '"\u200a"' )
			. json_decode( '"\u202f"' )
			. json_decode( '"\u205f"' )
			. json_decode( '"\u3000"' );
		Exporter::prepare_for_encoding( $test_content );
		$this->assertEquals(
			str_repeat( ' ', strlen( $test_content ) ),
			$test_content
		);
	}
}

