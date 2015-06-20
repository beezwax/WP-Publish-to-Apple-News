<?php
namespace Exporter\Components;

/**
 * An HTML's blockquote representation.
 *
 * @since 0.2.0
 */
class Quote extends Component {

	public static function node_matches( $node ) {
		if ( 'blockquote' == $node->nodeName ) {
			return $node;
		}

		return null;
	}

	protected function build( $text ) {
		// Remove initial and trailing tags: <blockquote><p>...</p></blockquote>
		$text = substr( $text, 15, -17 );

		$this->json = array(
			'role' => 'quote',
			'text' => $text,
		);

		$this->set_style();
	}

	private function set_style() {
		$this->json[ 'textStyle' ] = 'default-pullquote';
		$this->register_style( 'default-pullquote', array(
			'fontName' => $this->get_setting( 'pullquote_font' ),
			'fontSize' => $this->get_setting( 'pullquote_size' ),
			'textColor' => $this->get_setting( 'pullquote_color' ),
			'textTransform' => $this->get_setting( 'pullquote_transform' ),
			'relativeLineHeight' => 0.7,
		) );
	}

}

