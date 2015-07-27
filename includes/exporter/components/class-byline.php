<?php
namespace Exporter\Components;

use \Exporter\Exporter as Exporter;

/**
 * A byline normally describes who wrote the article, the date, etc.
 *
 * @since 0.2.0
 */
class Byline extends Component {

	protected function build( $text ) {
		$this->json = array(
			'role' => 'byline',
			'text' => $text,
		);

		$this->set_default_style();
		$this->set_default_layout();
	}

	private function set_default_style() {
		$this->json[ 'textStyle' ] = 'default-byline';
		$this->register_style( 'default-byline', array(
			'textAlignment' => $this->find_text_alignment(),
			'fontName'      => $this->get_setting( 'byline_font' ),
			'fontSize'      => $this->get_setting( 'byline_size' ),
			'textColor'     => $this->get_setting( 'byline_color' ),
		) );
	}

	private function set_default_layout() {
		$this->json[ 'layout' ] = 'byline-layout';
		$this->register_layout( 'byline-layout', array(
			'margin'      => array( 'top' => 10, 'bottom' => 30 ),
			'columnStart' => 0,
			'columnSpan'  => $this->get_setting( 'layout_columns' ),
		) );
	}

}

