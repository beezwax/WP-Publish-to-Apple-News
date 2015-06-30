<?php
namespace Exporter;

require_once plugin_dir_path( __FILE__ ) . 'class-component-factory.php';
require_once plugin_dir_path( __FILE__ ) . 'class-component-styles.php';
require_once plugin_dir_path( __FILE__ ) . 'class-component-layouts.php';
require_once plugin_dir_path( __FILE__ ) . 'class-exporter-content.php';
require_once plugin_dir_path( __FILE__ ) . 'class-workspace.php';
require_once plugin_dir_path( __FILE__ ) . 'class-settings.php';

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
 * @since   0.2.0
 */
class Exporter {

	/**
	 * The content object to be exported.
	 *
	 * @var  Exporter_Content
	 * @since 0.2.0
	 */
	private $exporter_content;

	/**
	 * The workspace object, used to write and zip files.
	 *
	 * @var  Workspace
	 * @since 0.2.0
	 */
	private $workspace;

	/**
	 * The settings object which is used to configure the output of the exporter.
	 *
	 * @var  Settings
	 * @since 0.4.0
	 */
	private $settings;

	/**
	 * FIXME: This constructor got big. Should make setters/getters instead?
	 */
	function __construct( Exporter_Content $content, Workspace $workspace = null,
		Settings $settings = null, Component_Styles $styles = null,
		Component_Layouts $layouts = null ) {

		$this->exporter_content = $content;
		$this->workspace = $workspace ?: new Workspace();
		$this->settings  = $settings ?: new Settings();
		$this->styles    = $styles ?: new Component_Styles();
		$this->layouts   = $layouts ?: new Component_Layouts();

		Component_Factory::initialize( $this->workspace, $this->settings, $this->styles, $this->layouts );
	}

	/**
	 * Generates JSON for the article.json file. By doing this, all attachments
	 * get added to the workspace/tmp directory automatically.
	 *
	 * If manually called build_article, must alway call `clean_workspace`
	 * afterwards, as the workspace would remain polluted for later articles
	 * otherwise.
	 */
	public function build_article() {
		$this->write_to_workspace( 'article.json', $this->generate_json() );
	}

	/**
	 * Based on the content this instance holds, create an Article Format zipfile
	 * and return the path.
	 * This function builds the article, zips it and cleans up after.
	 */
	public function export() {
		// Build the workspace/tmp folder.
		$this->build_article();
		// ZIP files inside that folder and clean it afterwards.
		return $this->zip_workspace( $this->content_id() );
	}

	/**
	 * Generate article.json contents. It does so by looping though all data,
	 * generating valid JSON and adding attachments to workspace/tmp directory.
	 *
	 * @return string The generated JSON for article.json
	 */
	private function generate_json() {
		$json = array(
			'version' => '0.10',
			'identifier' => 'post-' . $this->content_id(),
			'language' => 'en',
			'title' => $this->content_title(),
			// Base layout
			'layout' => $this->build_article_layout(),
			// Base style
			'documentStyle' => array(
				'backgroundColor' => '#F7F7F7',
			),
		);

		// Components
		$components = $this->build_components();
		if ( $components ) {
			$json['components'] = $components;
		}

		// Component styles. Must be called after build_components, as styles are
		// lazily added.
		$styles = $this->build_component_styles();
		if ( $styles ) {
			$json['componentTextStyles'] = $styles;
		}

		// Component layouts. Must be called after build_components, as layouts
		// are too lazily added.
		$layouts = $this->build_component_layouts();
		if ( $layouts ) {
			$json['componentLayouts'] = $layouts;
		}

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

	private function build_article_layout() {
		return array(
			'columns' => $this->get_setting( 'layout_columns' ), // Defaults to 8
			'width'   => $this->get_setting( 'layout_width' ),   // Defaults to 1024
			'margin'  => $this->get_setting( 'layout_margin' ),  // Defaults to 30
			'gutter'  => $this->get_setting( 'layout_gutter' ),  // Defaults to 20
		);
	}

	public function workspace() {
		return $this->workspace;
	}

	/**
	 * Isolate all dependencies.
	 */
	private function content_id() {
		return $this->exporter_content->id();
	}

	private function content_title() {
		return $this->exporter_content->title() ?: 'Untitled Article';
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

	private function content_setting( $name ) {
		return $this->exporter_content->get_setting( $name );
	}

	private function write_to_workspace( $filename, $contents ) {
		$this->workspace->write_tmp_file( $filename, $contents );
	}

	private function zip_workspace( $id ) {
		return $this->workspace->zip( 'article-' . $id . '.zip' );
	}

	private function get_component_from_shortname( $shortname, $html ) {
		return Component_Factory::get_component( $shortname, $html )->value();
	}

	private function get_components_from_node( $node ) {
		return Component_Factory::get_components_from_node( $node );
	}

	private function build_component_styles() {
		return $this->styles->get_styles();
	}

	private function build_component_layouts() {
		return $this->layouts->get_layouts();
	}

	private function get_setting( $name ) {
		return $this->settings->get( $name );
	}

	/**
	 * Builds an array with all the components of this WordPress content.
	 */
	private function build_components() {
		$meta_components = array();

		// The content's cover is optional. In WordPress, it's a post's thumbnail
		// or featured image.
		if ( $this->content_cover() ) {
			$meta_components[] = $this->get_component_from_shortname( 'cover', $this->content_cover() );
		}

		// Add title
		$meta_components[] = $this->get_component_from_shortname( 'title', $this->content_title() );

		// The content's intro is optional. In WordPress, it's a post's
		// excerpt. It's an introduction to the article.
		if ( $this->content_intro() ) {
			$meta_components[] = $this->get_component_from_shortname( 'intro', $this->content_intro() );
		}

		$post_components = array();

		$pullquote = $this->content_setting( 'pullquote' );
		$pullquote_position = $this->content_setting( 'pullquote_position' );
		if ( ! empty( $pullquote ) && $pullquote_position > 0 ) {
			$idx = 1;
			foreach ( $this->split_into_components() as $component ) {
				if ( $idx == $pullquote_position ) {
					$post_components[] = $this->get_component_from_shortname( 'blockquote', "<blockquote><p>$pullquote</p></blockquote>" );
					$pullquote_position = 0;
				}

				$post_components[] = $component->value();
				$idx++;
			}

			if ( $pullquote_position > 0 ) {
				$post_components[] = $this->get_component_from_shortname( 'blockquote', "<blockquote><p>$pullquote</p></blockquote>" );
			}
		} else {
			foreach ( $this->split_into_components() as $component ) {
				$post_components[] = $component->value();
			}
		}

		return array_merge( $meta_components, $post_components );
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
			$components = $this->get_components_from_node( $node );
			$result     = array_merge( $result, $components );
		}

		return $result;
	}

}

