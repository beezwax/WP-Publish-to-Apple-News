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
		// We are only interested in p, ul and ol
		if ( ! in_array( $node->nodeName, array( 'p', 'ul', 'ol' ) ) ) {
			return null;
		}

		// There are several components which cannot be translated to markdown. The
		// most common beeing images, so we split the HTML in all images. Note that
		// other elements, like Vide, EWV and Audio are not yet supported and must
		// not be inside a paragraph.
		if( 'p' == $node->nodeName ) {
			return self::split_from_all_images( $node );
		}

		return $node;
	}

	private static function remove_empty_tags( $html ) {
		return preg_replace( '#<[^/>][^>]*></[^>]+>#', '', $html );
	}

	private static function split_from_all_images( $node ) {
		$html = $node->ownerDocument->saveXML( $node );
		preg_match_all( '#<(\w+).*?>.*?(<img(?:.*?)/?>).*?</\1>#si', $html, $matches, PREG_SET_ORDER );

		if( ! $matches ) {
			return $node;
		}

		$result = array();
		foreach( $matches as $match ) {
			list( $all, $tag, $img ) = $match;
			$result = array_merge( $result, self::split_from_image( $html, $tag, $img ) );
		}
		return $result;
	}

	private static function split_from_image( $html, $tag, $img ) {
		$prefix  = '<p>';
		$postfix = '</p>';
		if ( 'p' != $tag  ) {
			$prefix  = $prefix . "<$tag>";
			$postfix = "</$tag>" . $postfix;
		}

		$parts = explode( $img, $html, 3 );

		$result   = array();
		$result[] = array(
			'name'  => 'p',
			'value' => self::remove_empty_tags( $parts[0] . $postfix ),
		);
		$result[] = array(
			'name'  => 'img',
			'value' => $img
		);
		$result[] = array(
			'name'  => 'p',
			'value' => self::remove_empty_tags( $prefix . $parts[1] ),
		);
		return $result;
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

