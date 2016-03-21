<?php

use \Exporter\Exporter as Exporter;

class Exporter_Test extends WP_UnitTestCase {

	private $prophet;

	public function setup() {
		$this->prophet = new \Prophecy\Prophet;
	}

	public function tearDown() {
		$this->prophet->checkPredictions();
	}

	public function testExport() {
		$workspace = $this->prophet->prophesize( '\Exporter\Workspace' );
		// Cleans up workspace
		$workspace
			->clean_up()
			->shouldBeCalled();
		// Creates the article.json file
		$workspace
			->write_tmp_file( 'article.json', \Prophecy\Argument::type( 'string' ) )
			->willReturn( true )
			->shouldBeCalled();
		// Creates a zipfile with the id
		$workspace
			->zip( 'article-3.zip' )
			->willReturn( true )
			->shouldBeCalled();

		$content  = new \Exporter\Exporter_Content( 3, 'Title', '<p>Example content</p>' );
		$exporter = new Exporter( $content, $workspace->reveal() );
		$exporter->export();
	}

	public function testBuildersGetCalled() {
		$workspace = $this->prophet->prophesize( '\Exporter\Workspace' );
		// Cleans up workspace
		$workspace
			->clean_up()
			->shouldBeCalled();
		// Creates the article.json file
		$workspace
			->write_tmp_file( 'article.json', \Prophecy\Argument::type( 'string' ) )
			->willReturn( true );
		// Creates a zipfile with the id
		$workspace
			->zip( 'article-3.zip' )
			->willReturn( true );

		$builder1 = $this->prophet->prophesize( '\Exporter\Builders\Builder' );
		$builder1
			->to_array()
			->shouldBeCalled();
		$builder2 = $this->prophet->prophesize( '\Exporter\Builders\Builder' );
		$builder2
			->to_array()
			->shouldBeCalled();

		$content  = new \Exporter\Exporter_Content( 3, 'Title', '<p>Example content</p>' );
		$exporter = new Exporter( $content, $workspace->reveal() );
		$exporter->initialize_builders( array(
			'componentTextStyles' => $builder1->reveal(),
			'componentLayouts'    => $builder2->reveal(),
		) );
		$exporter->export();
	}

}

