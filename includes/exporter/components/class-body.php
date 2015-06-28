<?php
namespace Exporter\Components;

/**
 * A paragraph component.
 *
 * @since 0.2.0
 */
class Body extends Component {

	public static function node_matches( $node ) {
		// This is tricky. Everything inside a p, ul or ol will be extracted as
		// HTML and parsed as markdown. This means, if there's a video, image,
		// audio, iframe or pretty much anything inside it will be ignored.
		// FIXME: A possible solution would be to filter the HTML beforehand,
		// splitting every non-markdown-able component out of the paragraph, thus,
		// making several smaller paragraphs.
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

		if ( 'yes' == $this->get_setting( 'initial_dropcap' ) ) {
			// Toggle setting. This should only happen in the initial paragraph.
			$this->set_setting( 'initial_dropcap', 'no' );
			$this->set_initial_dropcap_style();
		} else {
			$this->set_default_style();
		}
	}

	private function get_default_style() {
		return array(
			'textAlignment' => 'left',
			'fontName' => $this->get_setting( 'body_font' ),
			'fontSize' => $this->get_setting( 'body_size' ),
			'relativeLineHeight' => 1.2,
			'textColor' => $this->get_setting( 'body_color' ),
			'linkStyle' => array( 'textColor' => $this->get_setting( 'body_link_color' ) ),
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
					'textColor' => $this->get_setting( 'dropcap_color' ),
				),
			)
	 	) );
	}
}

