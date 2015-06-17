<?php
namespace Exporter\Components;

/**
 * Some Exporter_Content object might have an intro parameter.
 * This component does not need a node so no need to implement match_node.
 *
 * @since 0.2.0
 */
class Intro extends Component {

	protected function build( $text ) {
		$this->json = array(
			'role' => 'intro',
			'text' => $text . "\n",
		);

		$this->set_style();
	}

	private function set_style() {
		$this->json[ 'textStyle' ] = 'default-intro';
		$this->register_style( 'default-intro', array(
			'fontName' => $this->get_setting( 'body_font' ),
			'fontSize' => 16,
			'relativeLineHeight' => 1.2,
			'textColor' => '#000',
		) );
	}

}

