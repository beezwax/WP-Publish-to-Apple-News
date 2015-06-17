<?php
namespace Exporter\Components;

/**
 * One of the simplest components. It's just a paragraph.
 *
 * @since 0.2.0
 */
class Body extends Component {

	public static function node_matches( $node ) {
		if ( in_array( $node->nodeName, array( 'p', 'ul', 'ol' ) ) ) {
			return $node;
		}

		return null;
	}

	protected function build( $text ) {
		$this->json = array(
			'role' => 'body',
			'text' => $this->markdown->parse( $text ),
			'format' => 'markdown',
		);

		if( $this->get_setting( 'initial_dropcap' ) ) {
			// Toggle setting. This should only happen in the initial paragraph.
			$this->set_setting( 'initial_dropcap', false );
			$this->set_initial_dropcap_style();
		} else {
			$this->set_default_style();
		}
	}

	private function get_default_style() {
		return array(
			'textAlignment' => 'left',
			'fontName' => $this->get_setting( 'body_font' ),
			'fontSize' => 16,
			'relativeLineHeight' => 1.2,
			'textColor' => '#000',
			'linkStyle' => array( 'textColor' => '#428bca' ),
		);
	}

	private function set_default_style() {
		$this->json[ 'textStyle' ] = 'default-body';
		$this->register_style( 'default-body', $this->get_default_style() );
	}

	private function set_initial_dropcap_style() {
		$this->json[ 'textStyle' ] = 'dropcapBodyStyle';
		$this->register_style( 'dropcapBodyStyle', array_merge(
			$this->get_default_style(),
		 	array(
				'dropCapStyle' => array (
					'numberOfLines' => 2,
					'numberOfCharacters' => 1,
					'fontName' => $this->get_setting( 'dropcap_font' ),
					'textColor' => '#000',
				),
			)
	 	) );
	}

}

