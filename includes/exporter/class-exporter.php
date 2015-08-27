<?php
namespace Exporter;

/**
 * Export a Exporter_Content instance to Apple format.
 *
 * NOTE: This class is designed to work outside of WordPress just fine, so it
 * can be a dependency. It can be used to create other plugins, for example, a
 * Joomla or Drupal plugin. Even though this is not a WordPress class it
 * follows its coding conventions.
 *
 * @author  Federico Ramirez
 * @since   0.2.0
 */
class Exporter {

	/**
	 * The content object to be exported.
	 *
	 * @var  Exporter_Content
	 * @access private
	 * @since 0.2.0
	 */
	private $content;

	/**
	 * The workspace object, used to create the bundle.
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
	 * An ordered hash of builders. They will be executed in order when building
	 * the JSON array.
	 *
	 * @var array
	 * @since 0.4.0
	 */
	private $builders;

	function __construct( $content, $workspace = null, $settings = null ) {
		$this->content   = $content;
		$this->workspace = $workspace ?: new Workspace( $this->content_id() );
		$this->settings  = $settings  ?: new Settings();
		$this->builders  = array();
	}

	/**
	 * An ordered hash of builders. They will be executed in order when building
	 * the JSON array.
	 *
	 * @var array
	 * @since 0.4.0
	 */
	public function initialize_builders( $builders = null ) {
		if ( $builders ) {
			$this->builders = $builders;
		} else {
			$this->register_builder( 'layout'             , new Builders\Layout( $this->content, $this->settings ) );
			$this->register_builder( 'components'         , new Builders\Components( $this->content, $this->settings ) );
			$this->register_builder( 'componentTextStyles', new Builders\Component_Text_Styles( $this->content, $this->settings ) );
			$this->register_builder( 'componentLayouts'   , new Builders\Component_Layouts( $this->content, $this->settings ) );
			$this->register_builder( 'metadata'           , new Builders\Metadata( $this->content, $this->settings ) );
		}

		Component_Factory::initialize( $this->workspace, $this->settings, $this->get_builder( 'componentTextStyles' ), $this->get_builder( 'componentLayouts' ) );
	}

	private function register_builder( $name, $builder ) {
		$this->builders[ $name ] = $builder;
	}

	private function get_builder( $name ) {
		return $this->builders[ $name ];
	}

	/**
	 * Generates JSON for the article.json file. By doing this, all attachments
	 * get added to the workspace/tmp directory automatically.
	 *
	 * When called, `clean_workspace` must always be called before and
	 * afterwards.
	 *
	 * @since 0.4.0
	 */
	public function generate() {
		if ( ! $this->builders ) {
			$this->initialize_builders();
		}

		$this->write_json( $this->generate_json() );
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
	 * Based on the content this instance holds, create an Article Format bundle.
	 * and return the path.
	 * This function builds the article and cleans up after.
	 */
	public function export() {
		// If an export or push was cancelled, the workspace might be polluted.
		// Clean beforehand.
		$this->clean_workspace();

		// Build the bundle content.
		$this->generate();

		// Some use cases for this function expect it to return the JSON.
		return $this->get_json();
	}

	/**
	 * Generate article.json contents. It does so by looping though all data,
	 * generating valid JSON and adding attachments to workspace/tmp directory.
	 *
	 * @return string The generated JSON for article.json
	 */
	private function generate_json() {
		// Base JSON
		$json = array(
			'version'    => '0.10',
			'identifier' => 'post-' . $this->content_id(),
			'language'   => 'en',
			'title'      => $this->content_title(),
		);

		// Builders
		$json['documentStyle'] = $this->build_article_style();
		foreach ( $this->builders as $name => $builder ) {
			$arr = $builder->to_array();
			if ( $arr ) {
				$json[ $name ] = $arr;
			}
		}

		return json_encode( $json );
	}

	/**
	 * Isolate all dependencies.
	 */
	private function write_json( $content ) {
		$this->workspace->write_json( $content );
	}

	public function get_json() {
		return $this->workspace->get_json();
	}

	public function get_bundles() {
		return $this->workspace->get_bundles();
	}

	private function clean_workspace() {
		$this->workspace->clean_up();
	}

	private function build_article_style() {
		return array(
			'backgroundColor' => '#FFFFFF',
		);
	}

	private function content_id() {
		return $this->content->id();
	}

	private function content_title() {
		return $this->content->title() ?: 'Untitled Article';
	}

}
