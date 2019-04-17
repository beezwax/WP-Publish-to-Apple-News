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

	public function testExport() {
		$workspace = $this->prophet->prophesize( '\Apple_Exporter\Workspace' );
		// Cleans up workspace
		$workspace
			->clean_up()
			->shouldBeCalled();

		// Writes JSON
		$workspace
			->write_json( Argument::that( array( $this, 'isValidJSON' ) ) )
			->shouldBeCalled();

		// Get JSON
		$workspace
			->get_json()
			->shouldBeCalled();

		$content  = new Apple_Exporter\Exporter_Content( 3, 'Title', '<p>Example content</p>' );
		$exporter = new Exporter( $content, $workspace->reveal() );
		$exporter->export();
	}

	public function isValidJSON( $json ) {
		return ( null !== json_decode( $json ) );
	}

	public function testBuildersGetCalled() {
		$workspace = $this->prophet->prophesize( '\Apple_Exporter\Workspace' );
		// Cleans up workspace
		$workspace
			->clean_up()
			->shouldBeCalled();

		// Writes JSON
		$workspace
			->write_json( Argument::that( array( $this, 'isValidJSON' ) ) )
			->shouldBeCalled();

		// Get JSON
		$workspace
			->get_json()
			->shouldBeCalled();

		$builder1 = $this->prophet->prophesize( '\Apple_Exporter\Builders\Builder' );
		$builder1
			->to_array()
			->shouldBeCalled();
		$builder2 = $this->prophet->prophesize( '\Apple_Exporter\Builders\Builder' );
		$builder2
			->to_array()
			->shouldBeCalled();
		$builder3 = $this->prophet->prophesize( '\Apple_Exporter\Builders\Builder' );
		$builder3
			->to_array()
			->shouldBeCalled();

		$content  = new Apple_Exporter\Exporter_Content( 3, 'Title', '<p>Example content</p>' );
		$exporter = new Exporter( $content, $workspace->reveal() );
		$exporter->initialize_builders( array(
			'componentTextStyles' => $builder1->reveal(),
			'componentLayouts'    => $builder2->reveal(),
			'componentStyles'     => $builder3->reveal(),
		) );
		$exporter->export();
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

