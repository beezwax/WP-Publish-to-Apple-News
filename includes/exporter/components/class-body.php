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

		// If the node is p, ul or ol AND it's empty, just ignore.
		if ( empty( $node->nodeValue ) ) {
			return null;
		}

		// There are several components which cannot be translated to markdown,
		// namely images, videos, audios and EWV. If these components are inside a
		// paragraph, split the paragraph.
		if ( 'p' == $node->nodeName ) {
			$html = $node->ownerDocument->saveXML( $node );
			return self::split_non_markdownable( $html );
		}

		return $node;
	}

	private static function split_non_markdownable( $html ) {
		preg_match( '#<(img|video|audio|iframe).*?(?:>(.*?)</\1>|/?>)#si', $html, $matches );

		if ( ! $matches ) {
			return array( array( 'name' => 'p', 'value' => $html ) );
		}

		list( $whole, $tag )  = $matches;
		list( $left, $right ) = explode( $whole, $html, 3 );

		return array_merge(
		 	array(
				array( 'name'  => 'p',  'value' => self::clean_html( $left . '</p>' ) ),
				array( 'name'  => $tag, 'value' => $whole ),
		 	),
			self::split_non_markdownable( self::clean_html( '<p>' . $right ) )
		);
	}

	/**
	 * Use PHP's HTML parser to generate valid HTML out of potentially broken
	 * input.
	 */
	private static function clean_html( $html ) {
		// Because PHP's DomDocument doesn't like HTML5 tags, ignore errors.
		$dom = new \DOMDocument();
		libxml_use_internal_errors( true );
		$dom->loadHTML( '<?xml encoding="utf-8" ?>' . $html );
		libxml_clear_errors( true );

		// Find the first-level nodes of the body tag.
		$element = $dom->getElementsByTagName( 'body' )->item( 0 )->childNodes->item( 0 );
		$html    = $dom->saveHTML( $element );
		return preg_replace( '#<[^/>][^>]*></[^>]+>#', '', $html );
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

