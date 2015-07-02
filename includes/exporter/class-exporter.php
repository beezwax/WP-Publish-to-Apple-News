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
	 * Default layout values for exporter
	 */
	const LAYOUT_COLUMNS = 7;
	const LAYOUT_WIDTH   = 1024;

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
	 * An instance of styles. It manages all the styles required and can return a
	 * valid array.
	 *
	 * @var  Styles
	 * @since 0.4.0
	 */
	private $styles;

	/**
	 * An instance of layouts. It manages all the layouts required and can return
	 * a valid array.
	 *
	 * @var  Styles
	 * @since 0.4.0
	 */
	private $layouts;

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
		$this->layouts   = $layouts ?: new Component_Layouts( $this->settings );

		Component_Factory::initialize( $this->workspace, $this->settings, $this->styles, $this->layouts );
	}

	/**
	 * Generates JSON for the article.json file. By doing this, all attachments
	 * get added to the workspace/tmp directory automatically.
	 *
	 * When called, must alway call `clean_workspace` afterwards, as the
	 * workspace would be polluted for later articles.
	 *
	 * @since 0.4.0
	 */
	public function generate() {
		$this->write_to_workspace( 'article.json', $this->generate_json() );
	}

	/**
	 * Gets the instance of the current workspace.
	 *
	 * @since 0.4.0
	 */
	public function workspace() {
		return $this->workspace;
	}

	/**
	 * Based on the content this instance holds, create an Article Format zipfile
	 * and return the path.
	 * This function builds the article, zips it and cleans up after.
	 */
	public function export() {
		// Build the ./workspace/tmp folder.
		$this->generate();
		// ZIP files inside that folder. This also cleans the workspace when done.
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
				'backgroundColor' => '#FFFFFF',
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

		$meta = $this->build_metadata();
		if ( $meta ) {
			$json['metadata'] = $meta;
		}

		return json_encode( $json );
	}

	private function build_metadata() {
		$meta = array();

		// The content's intro is optional. In WordPress, it's a post's
		// excerpt. It's an introduction to the article.
		if ( $this->content_intro() ) {
			$meta[ 'excerpt' ] = $this->content_intro();
		}

		// If the content has a cover, use it as thumb.
		if ( $this->content_cover() ) {
			$filename  = basename( $this->content_cover() );
			$thumb_url = 'bundle://' . $filename;
			$meta[ 'thumbnailURL' ] = $thumb_url;
		}

		return $meta;
	}


	private function build_article_layout() {
		return array(
			'columns' => self::LAYOUT_COLUMNS,
			'width'   => self::LAYOUT_WIDTH,
			'margin'  => $this->get_setting( 'layout_margin' ),  // Defaults to 30
			'gutter'  => $this->get_setting( 'layout_gutter' ),  // Defaults to 20
		);
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

	private function content_byline() {
		return $this->exporter_content->byline();
	}

	private function content_nodes() {
		return $this->exporter_content->nodes();
	}

	private function write_to_workspace( $filename, $contents ) {
		$this->workspace->write_tmp_file( $filename, $contents );
	}

	private function zip_workspace( $id ) {
		return $this->workspace->zip( 'article-' . $id . '.zip' );
	}

	private function get_component_from_shortname( $shortname, $html ) {
		return Component_Factory::get_component( $shortname, $html );
	}

	private function get_components_from_node( $node ) {
		return Component_Factory::get_components_from_node( $node );
	}

	private function build_component_styles() {
		return $this->styles->to_array();
	}

	private function build_component_layouts() {
		return $this->layouts->to_array();
	}

	private function get_setting( $name ) {
		return $this->settings->get( $name );
	}

	/**
	 * Meta components are those which were not created from HTML, instead, they
	 * contain only text. This text is normally created from the article
	 * metadata.
	 */
	private function meta_components() {
		$components = array();

		// The content's cover is optional. In WordPress, it's a post's thumbnail
		// or featured image.
		if ( $this->content_cover() ) {
			$components[] = $this->get_component_from_shortname( 'cover', $this->content_cover() )->to_array();
		}

		// Add title
		$components[] = $this->get_component_from_shortname( 'title', $this->content_title() )->to_array();

		// Add title
		if ( $this->content_byline() ) {
			$components[] = $this->get_component_from_shortname( 'byline', $this->content_byline() )->to_array();
		}

		return $components;
	}

	/**
	 * Builds an array with all the components of this WordPress content.
	 */
	private function build_components() {
		// Based on the meta components, split the post's text into components and
		// append them to the "meta components".
		$components = $this->meta_components();
		foreach ( $this->split_into_components() as $component ) {
			$components[] = $component->to_array();
		}
		return $components;
	}

	/**
	 * Anchor components that are anchorable
	 */
	private function anchor_components( $components ) {
		$len = count( $components );

		for ( $i = 0; $i < $len; $i++ ) {
			$component = $components[ $i ];

			if ( ! $component->is_anchorable ) {
				continue;
			}

			// Anchor this component to previous component
			$uid             = uniqid();
			$other_component = $components[ $i - 1 ];
			$other_component->set_json( 'identifier', 'component-' . $uid );
			$component->set_json( 'anchor', array(
				'targetComponentIdentifier' => 'component-' . $uid,
				'targetAnchorPosition'      => 'center',
				'rangeStart' => 0,
				'rangeLength' => 1,
			) );
		}

		return $components;
	}

	/**
	 * Split components from the source WordPress content.
	 */
	private function split_into_components() {
		// Pullquote check
		$pullquote          = $this->content_setting( 'pullquote' );
		$pullquote_position = $this->content_setting( 'pullquote_position' );

		// Loop though the first-level nodes of the body element. Components
		// might include child-components, like an Cover and Image.
		$result   = array();
		$position = 0;
		foreach ( $this->content_nodes() as $node ) {
			$components = $this->get_components_from_node( $node );

			if ( !empty( $pullquote ) && $pullquote_position > 0 ) {
				// Do we have to insert a pullquote into the article?
				// If so, iterate all components, and add when the position is reached.
				foreach ( $components as $component ) {
					$position++;
					$result[] = $component;

					if ( $position == $pullquote_position ) {
						$pullquote_component = $this->get_component_from_shortname( 'blockquote', "<blockquote>$pullquote</blockquote>" );
						$pullquote_component->set_anchorable( true );
						$result[] = $pullquote_component;

						$pullquote_position = 0;
					}
				}
			} else {
				// No pullquote check needed, just add components into result.
				$result = array_merge( $result, $components );
			}
		}

		$result = $this->anchor_components( $result );

		return $result;
	}

}

