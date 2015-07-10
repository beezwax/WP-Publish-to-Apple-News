<?php
namespace Exporter\Components;

require_once __DIR__ . '/../class-markdown.php';

/**
 * Base component class. All components must inherit from this class and
 * implement its abstract method "build".
 *
 * It provides several helper methods, such as get/set_setting and
 * register_style.
 *
 * @since 0.2.0
 */
abstract class Component {

	/**
	 * When a component is displayed aligned relative to another one, slide the
	 * other component a few columns, in this case, 2.
	 *
	 * @since 0.4.0
	 */
	const ALIGNMENT_OFFSET = 2;

	/**
	 * Anchorable components are anchored to the next element that appears.
	 *
	 * @since 0.4.0
	 */
	public $is_anchorable = false;

	/**
	 * @since 0.2.0
	 */
	protected $workspace;

	/**
	 * @since 0.2.0
	 */
	protected $text;

	/**
	 * @since 0.2.0
	 */
	protected $json;

	/**
	 * @since 0.4.0
	 */
	protected $settings;

	/**
	 * @since 0.4.0
	 */
	protected $styles;

	/**
	 * @since 0.4.0
	 */
	private $uid;

	function __construct( $text, $workspace, $settings, $styles, $layouts, $markdown = null ) {
		$this->workspace = $workspace;
		$this->settings  = $settings;
		$this->styles    = $styles;
		$this->layouts   = $layouts;
		$this->markdown  = $markdown ?: new \Exporter\Markdown();
		$this->text      = $text;
		$this->json      = null;

		// Once the text is set, build proper JSON. Store as an array.
		$this->build( $this->text );
	}

	/**
	 * Given a DomNode, if it matches the component, return the relevant node to
	 * work on. Otherwise, return null.
	 */
	public static function node_matches( $node ) {
		return null;
	}

	/**
	 * Transforms HTML into an array that describes the component using the build
	 * function.
	 */
	public function to_array() {
		return $this->json;
	}

	public function set_json( $name, $value ) {
		$this->json[ $name ] = $value;
	}

	public function get_json( $name ) {
		return $this->json[ $name ];
	}

	public function set_anchorable( $flag ) {
		if ( $flag ) {
			$this->layouts->set_anchor_layout_for( $this );
		}

		$this->is_anchorable = $flag;
	}

	public function uid() {
		if ( is_null( $this->uid ) ) {
			$this->uid = 'component-' . uniqid();
			$this->set_json( 'identifier', $this->uid );
		}

		return $this->uid;
	}

	/**
	 * Given a source (either a file path or an URL) gets the contents and writes
	 * them into a file with the given filename.
	 *
	 * @param string $filename  The name of the file to be created
	 * @param string $source    The path or URL of the resource which is going to
	 *                          be bundled
	 */
	protected function bundle_source( $filename, $source ) {
		$content = $this->workspace->get_file_contents( $source );
		$this->workspace->write_tmp_file( $filename, $content );
	}

	// Isolate settings dependency
	// -------------------------------------------------------------------------

	/**
	 * Gets an exporter setting.
	 *
	 * @since 0.4.0
	 */
	protected function get_setting( $name ) {
		return $this->settings->get( $name );
	}

	/**
	 * Sets an exporter setting.
	 *
	 * @since 0.4.0
	 */
	protected function set_setting( $name, $value ) {
		return $this->settings->set( $name, $value );
	}

	/**
	 * Using the style service, register a new style.
	 *
	 * @since 0.4.0
	 */
	protected function register_style( $name, $spec ) {
		$this->styles->register_style( $name, $spec );
	}

	/**
	 * Using the layouts service, register a new layout.
	 *
	 * @since 0.4.0
	 */
	protected function register_layout( $name, $spec ) {
		$this->layouts->register_layout( $name, $spec );
	}

	protected static function node_has_class( $node, $classname ) {
		if ( ! method_exists( $node, 'getAttribute' ) ) {
			return false;
		}

		$classes = trim( $node->getAttribute( 'class' ) );

		if ( empty( $classes ) ) {
			return false;
		}

		return 1 == preg_match( "/(?:\s+|^)$classname(?:\s+|$)/", $classes );
	}

	/**
	 * This function is in charge of transforming HTML into a Article Format
	 * valid array.
	 */
	abstract protected function build( $text );

}
