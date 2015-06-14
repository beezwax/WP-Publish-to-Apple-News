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
	 * Isolate all dependencies.
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

	private function write_to_workspace( $filename, $contents ) {
		$this->workspace->write_tmp_file( $filename, $contents );
	}

	private function zip_workspace( $id ) {
		return $this->workspace->zip( 'article-' . $id . '.zip' );
	}

	private function get_component_from_shortname( $shortname, $html ) {
		return Component_Factory::get_component( 'intro', $html )->value();
	}

	private function get_component_from_node( $node ) {
		return Component_Factory::get_component_from_node( $node );
	}

	/**
	 * Builds an array with all the components of this WordPress content.
	 */
	private function build_components() {
		$components = array();

		// The content's cover is optional. In WordPress, it's a post's thumbnail
		// or featured image.
		if ( $this->content_cover() ) {
			$components[] = $this->get_component_from_shortname( 'cover', $this->content_intro() );
		}

		// Add title
		$components[] = $this->get_component_from_shortname( 'title', $this->content_title() );

		// The content's intro is optional. In WordPress, it's a post's
		// excerpt. It's an introduction to the article.
		if ( $this->content_intro() ) {
			$components[] = $this->get_component_from_shortname( 'intro', $this->content_intro() );
		}

		foreach ( $this->split_into_components() as $component ) {
			$components[] = $component->value();
		}

		return $components;
	}

	/**
	 * Split components from the source WordPress content.
	 */
	private function split_into_components() {
		// Because PHP's DomDocument doesn't like HTML5 tags, ignore errors.
		$dom = new \DOMDocument();
		libxml_use_internal_errors( true );
		$dom->loadHTML( '<?xml encoding="utf-8" ?>' . $this->content_text() );
		libxml_clear_errors( true );

		// Find the first-level nodes of the body tag.
		$nodes = $dom->getElementsByTagName( 'body' )->item( 0 )->childNodes;

		// Loop though the first-level nodes of the body element. Components
		// might include child-components, like an Cover and Image.
		$result = array();
		foreach ( $nodes as $node ) {
			$component = $this->get_component_from_node( $node );

			if( is_array( $component ) ) {
				$result = array_merge( $result, $component );
			} else {
				$result[] = $component;
			}
		}

		// Remove null values from result and return
		return array_filter( $result );
	}

}

