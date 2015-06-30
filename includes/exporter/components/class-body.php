<?php
namespace Exporter\Components;

use \Exporter\Exporter as Exporter;

/**
 * A paragraph component.
 *
 * @since 0.2.0
 */
class Body extends Component {

	/**
	 * Body components always span 5 columns. This is related with
	 * Exporter::LAYOUT_COLUMNS, which is fixed at 7.
	 *
	 * @since 0.4.0
	 */
	const COLUMN_SPAN = 5;

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

		$this->set_default_layout();
	}

	private function set_default_layout() {
		// Find out where the body must start according to the body orientation.
		// Orientation defaults to left, thus, col_start is 0.
		$col_start = 0;
		switch ( $this->get_setting( 'body_orientation' ) ) {
		case 'right':
			$col_start = Exporter::LAYOUT_COLUMNS - self::COLUMN_SPAN;
			break;
		case 'center':
			$col_start = floor( ( Exporter::LAYOUT_COLUMNS - self::COLUMN_SPAN ) / 2 );
			break;
		}

		// Now that we have the appropriate col_start, register the layout
		$this->json[ 'layout' ] = 'body-layout';
		$this->register_layout( 'body-layout', array(
			'columnStart' => $col_start,
			'columnSpan'  => self::COLUMN_SPAN,
		) );
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

