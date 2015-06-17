<?php
namespace Exporter\Components;

class Title extends Component {

	protected function build( $text ) {
		$this->json = array(
			'role' => 'title',
			'text' => $text,
		);

		$this->set_style();
	}

	private function set_style() {
		$this->json[ 'textStyle' ] = 'default-title';
		$this->register_style( 'default-title', array(
			'fontName' => $this->get_setting( 'header_font' ),
			'fontSize' => $this->get_setting( 'header1_size' ),
			'relativeLineHeight' => 1.2,
			'textColor' => $this->get_setting( 'header_color' ),
		) );
	}

}

