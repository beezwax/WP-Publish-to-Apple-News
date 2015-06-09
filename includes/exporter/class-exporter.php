<?php
namespace Exporter;

require_once plugin_dir_path( __FILE__ ) . 'class-component-factory.php';
require_once plugin_dir_path( __FILE__ ) . 'class-exporter-content.php';
require_once plugin_dir_path( __FILE__ ) . 'class-workspace.php';

/**
 * Export a Exporter_Content instance to Apple format.
 *
 * TODO: This class is designed to work outside of WordPress just fine, so it
 * can be a dependency. It can be used to create other plugins, for example,
 * a Joomla or Drupal plugin.
 *
 * NOTE: Even though this is not a WordPress class it follows its coding
 * conventions.
 *
 * @author  Federico Ramirez
 * @since   0.0.0
 */
class Exporter {

	private $exporter_content;
	private $workspace;

	function __construct( Exporter_Content $content, Workspace $workspace = null ) {
		$this->exporter_content = $content;
		$this->workspace = $workspace ?: new Workspace();

		Component_Factory::initialize( $this->workspace );
	}

	/**
	 * Based on the content this instance holds, create an Article Format zipfile
	 * and return the path.
	 */
	public function export() {
		$this->write_to_workspace( 'article.json', $this->generate_json() );
		return $this->zip_workspace( $this->content_id() );
	}

	private function generate_json() {
		$json = array(
			'version'       => '0.1',
			'identifier'    => 'post-' . $this->content_id(),
			'language'      => 'en',
			'title'         => $this->content_title(),
			'components'    => $this->build_components(),
			// TODO: Create a Layout object
			'layout' => array(
				'columns' => 7,
				'width'   => 1024,
				'margin'  => 30,
				'gutter'  => 20,
			),
			// Styles
			'documentStyle' => array(
				'backgroundColor' => '#F7F7F7',
			),
			// TODO: Create a Style object
			'componentTextStyles' => array(
				'default' => array(
					'fontName' => 'Helvetica',
					'fontSize' => 13,
					'linkStyle' => array( 'textColor' => '#428bca' ),
				),
				'title' => array(
					'fontName' => 'Helvetica-Bold',
					'fontSize' => 30,
					'hyphenation' => false,
				),
				'default-body' => array(
					'fontName' => 'Helvetica',
					'fontSize' => 13,
				),
			),
			// TODO: Create a Component Layout object
			'componentLayouts' => array(
				'headerContainerLayout' => array(
					'columnStart' => 0,
					'columnSpan' => 7,
					'ignoreDocumentMargin' => true,
					'minimumHeight' => '50vh',
				),
			),
		);

		// For now, generate the thumb url in here, eventually it will move to the
		// metadata manager object. The cover component is in charge of copying
		// the actual file, just link here.
		if ( $this->content_cover() ) {
			$filename  = basename( $this->content_cover() );
			$thumb_url = 'bundle://' . $filename;

			// TODO: Create a metadata object
			$json['metadata'] = array(
				'thumbnailURL' => $thumb_url,
			);
		}

		return json_encode( $json );
	}

	/**
	 * Isolate content dependencies.
	 */
	private function content_id() {
		return $this->exporter_content->id();
	}

	private function content_title() {
		return $this->exporter_content->title();
	}

	private function content_text() {
		return $this->exporter_content->content();
	}

	private function content_intro() {
		return $this->exporter_content->intro();
	}

	private function content_cover() {
		return $this->exporter_content->cover();
	}

	private function write_to_workspace( $file, $contents ) {
		$this->workspace->write_tmp_file( $file, $contents );
	}

	private function zip_workspace( $id ) {
		return $this->workspace->zip( 'article-' . $id . '.zip' );
	}

	/**
	 * Builds an array with all the components of this WordPress content.
	 */
	private function build_components() {
		$components = array();

		// The content's cover is optional. In WordPress, it's a post's thumbnail
		// or featured image.
		if ( $this->content_cover() ) {
			$components[] = Component_Factory::get_component( 'cover', $this->content_cover() )->value();
		}

		// The content's intro is optional. In WordPress, it's a post's
		// excerpt. It's an introduction to the article.
		if ( $this->content_intro() ) {
			$components[] = Component_Factory::get_component( 'intro', $this->content_intro() )->value();
		}

		foreach ( $this->split_into_components() as $component ) {
			$components[] = $component->value();
		}

		return $components;
	}

	/**
	 * Given a DomNode, try to create a component from it. If it fails, return
	 * null.
	 */
	private function create_component_or_null( $node, $name = null ) {
		$html = $node->ownerDocument->saveXML( $node );
		// get_component returns null if no component matches.
		return Component_Factory::get_component( $name ?: $node->nodeName, $html, $this->workspace );
	}

	private function node_contains( $node, $tagname ) {
		if ( ! method_exists( $node, 'getElementsByTagName' ) ) {
			return false;
		}

		$elements = $node->getElementsByTagName( $tagname );

		if ( $elements->length == 0 ) {
			return false;
		}

		return $elements->item( 0 );
	}

	private function node_has_class( $node, $classname ) {
		if ( ! method_exists( $node, 'getAttribute' ) ) {
			return false;
		}

		$classes = trim( $node->getAttribute( 'class' ) );

		if ( empty( $classes ) ) {
			return false;
		}

		return in_array( $classname, explode( ' ', $classes ) );
	}

	/**
	 * Split components from the source WordPress content.
	 */
	private function split_into_components() {
		// Because PHP's DomDocument doesn't like HTML5 tags, ignore errors.
		$dom = new \DOMDocument();
		libxml_use_internal_errors( true );
		$dom->loadHTML( $this->content_text() );
		libxml_clear_errors( true );

		// Find the first-level nodes of the body tag.
		$nodes = $dom->getElementsByTagName( 'body' )->item( 0 )->childNodes;

		// Loop though the first-level nodes of the body element. Components
		// might include child-components, like an Cover and Image.
		$result = array();
		foreach ( $nodes as $node ) {
			$component = null;

			// Some nodes might be found nested inside another, for example an
			// <img> could be inside a <p> or <a>. Seek for them and add them.
			// The way this is beeing handled right now is pretty hacky, but
			// I'm waiting until I get a bit more code so I can figure out how
			// to do it propertly. FIXME.
			if ( $this->node_has_class( $node, 'gallery' ) ) {
				$component = $this->create_component_or_null( $node, 'gallery' );
			} else if ( $this->node_has_class( $node, 'twitter-tweet' ) ) {
				$component = $this->create_component_or_null( $node, 'tweet' );
			} else if ( $image_node = $this->node_contains( $node, 'img' ) ) {
				$component = $this->create_component_or_null( $image_node );
			} else if ( $ewv = $this->node_contains( $node, 'iframe' ) ) {
				$component = $this->create_component_or_null( $ewv );
			} else if ( $video = $this->node_contains( $node, 'video' ) ) {
				$component = $this->create_component_or_null( $video );
			} else if ( $this->node_contains( $node, 'script' ) ) {
				// Ignore script tags.
				$component = null;
			} else {
				$component = $this->create_component_or_null( $node );
			}

			$result[] = $component;
		}

		// Remove null values from result and return
		return array_filter( $result );
	}

}

