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
	 * Possible anchoring positions
	 */
	const ANCHOR_NONE  = 0;
	const ANCHOR_AUTO  = 1;
	const ANCHOR_LEFT  = 2;
	const ANCHOR_RIGHT = 3;

	/**
	 * Anchorable components are anchored to the previous element that appears in
	 * the position specified. If the previous element is an advertisement,
	 * attaches to the next instead of the previous element.
	 *
	 * @since 0.6.0
	 */
	public $anchor_position = self::ANCHOR_NONE;

	/**
	 * If this component is set as a target for an anchor, does it need to fix
	 * it's layout? Defaults to true, components can set this to false if they do
	 * not need an automatic layout assigned to them or want more control.
	 *
	 * Right now, the only component that sets this to false is the body, as it
	 * doesn't need a special layout for anchoring, it just flows around anchored
	 * components.
	 *
	 * @since 0.6.0
	 */
	public $needs_layout_if_anchored = true;

	/**
	 * Whether this component can be an anchor target.
	 *
	 * @since 0.6.0
	 */
	protected $can_be_anchor_target = false;

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
	 * Use PHP's HTML parser to generate valid HTML out of potentially broken
	 * input.
	 */
	protected static function clean_html( $html ) {
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

	public function set_anchor_position( $position ) {
		$this->anchor_position = $position;
	}

	/**
	 * Sets the anchor layout for this component
	 *
	 * @since 0.6.0
	 */
	public function anchor() {
		if ( ! $this->needs_layout_if_anchored ) {
			return;
		}

		$this->layouts->set_anchor_layout_for( $this );
	}

	/**
	 * All components that are anchor target have an UID. Return whether this
	 * component is an anchor target.
	 *
	 * @since 0.6.0
	 */
	public function is_anchor_target() {
		return !is_null( $this->uid );
	}

	/**
	 * Check if it's can_be_anchor_target and it hasn't been anchored already.
	 */
	public function can_be_anchor_target() {
		return $this->can_be_anchor_target && is_null( $this->uid );
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
