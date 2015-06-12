<?php
namespace Exporter\Markdown;

/**
 * This class transforms HTML into Article Format Markdown, which is a subset
 * of Markdown.
 *
 * For elements that are not supported, just skip them and add the contents of
 * the tag.
 *
 * @since 0.0.0
 */
class Markdown {

	private $list_mode;
	private $list_index;

	function __construct() {
		$this->list_mode = 'ul';
		$this->list_index = 1;
	}

	/**
	 * Transforms HTML into Article Format Markdown.
	 */
	public function parse( $html ) {
		// PHP's DomDocument doesn't like HTML5 so we must ignore errors, we'll
		// manually handle all tags anyways.
		$dom = new \DOMDocument();
		libxml_use_internal_errors( true );
		// A trick to load string as UTF-8
		$dom->loadHTML( '<?xml encoding="UTF-8">' . $html );
		libxml_clear_errors( true );

		// Find the first-level nodes of the body tag.
		$nodes = $dom->getElementsByTagName( 'body' )->item( 0 )->childNodes;

		// Parse them and return result
		return $this->parseNodes( $nodes );
	}

	private function parseNodes( $nodes ) {
		$result = '';
		foreach ( $nodes as $node ) {
			$result .= $this->parse_node( $node );
		}

		return $result;
	}

	private function parse_node( $node ) {
		switch( $node->nodeName ) {
		case '#text':
			return $this->parse_text_node( $node );
		case 'strong':
			return $this->parse_strong_node( $node );
		case  'i':
		case 'em':
			return $this->parse_emphasis_node( $node );
		case 'br':
			return $this->parse_linebreak_node( $node );
		case 'p':
			return $this->parse_paragraph_node( $node );
		case 'a':
			return $this->parse_hyperlink_node( $node );
		case 'ul':
			return $this->parse_unordered_list_node( $node );
		case 'ol':
			return $this->parse_ordered_list_node( $node );
		case 'li':
			return $this->parse_list_item_node( $node );
		}

		return $node->nodeValue ?: '';
	}

	private function parse_text_node( $node ) {
		return $node->nodeValue;
	}

	private function parse_linebreak_node( $node ) {
		return "  \n";
	}

	private function parse_strong_node( $node ) {
		return '**' . $this->parseNodes( $node->childNodes ) . '**';
	}

	private function parse_emphasis_node( $node ) {
		return '_' . $this->parseNodes( $node->childNodes ) . '_';
	}

	private function parse_paragraph_node( $node ) {
		return $this->parseNodes( $node->childNodes ) . "\n\n";
	}

	/**
	 * Hyperlinks are not yet supported in Article Format markdown. Ignore for
	 * now.
	 */
	private function parse_hyperlink_node( $node ) {
		$url = $node->getAttribute( 'href' );
		return '[' . $this->parseNodes( $node->childNodes ) . '](' . $url . ')';
	}

	private function parse_unordered_list_node( $node ) {
		$this->list_mode = 'ul';
		return $this->parseNodes( $node->childNodes ) . "\n";
	}

	private function parse_ordered_list_node( $node ) {
		$this->list_mode = 'ol';
		$this->list_index = 1;
		return $this->parseNodes( $node->childNodes ) . "\n";
	}

	private function parse_list_item_node( $node ) {
		if( 'ol' == $this->list_mode ) {
			return $this->list_index . '. ' . $this->parseNodes( $node->childNodes );
			$this->list_index += 1;
		}

		return "- " . $this->parseNodes( $node->childNodes );
	}

}
