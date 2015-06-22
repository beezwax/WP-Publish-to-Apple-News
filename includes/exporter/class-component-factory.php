<?php
namespace Exporter;

require_once plugin_dir_path( __FILE__ ) . 'components/class-component.php';
require_once plugin_dir_path( __FILE__ ) . 'components/class-image.php';
require_once plugin_dir_path( __FILE__ ) . 'components/class-body.php';
require_once plugin_dir_path( __FILE__ ) . 'components/class-quote.php';
require_once plugin_dir_path( __FILE__ ) . 'components/class-heading.php';
require_once plugin_dir_path( __FILE__ ) . 'components/class-embed-web-video.php';
require_once plugin_dir_path( __FILE__ ) . 'components/class-intro.php';
require_once plugin_dir_path( __FILE__ ) . 'components/class-cover.php';
require_once plugin_dir_path( __FILE__ ) . 'components/class-gallery.php';
require_once plugin_dir_path( __FILE__ ) . 'components/class-tweet.php';
require_once plugin_dir_path( __FILE__ ) . 'components/class-instagram.php';
require_once plugin_dir_path( __FILE__ ) . 'components/class-video.php';
require_once plugin_dir_path( __FILE__ ) . 'components/class-audio.php';
require_once plugin_dir_path( __FILE__ ) . 'components/class-divider.php';
require_once plugin_dir_path( __FILE__ ) . 'components/class-title.php';

/**
 * This class in in charge of creating components. Manual component
 * instantiation should be avoided, use this instead.
 *
 * @since 0.2.0
 */
class Component_Factory {

	private static $components = array();
	private static $workspace  = null;
	private static $settings   = null;
	private static $styles     = null;
	private static $layouts    = null;

	public static function initialize( $workspace, $settings, $styles, $layouts ) {
		self::$workspace = $workspace;
		self::$settings  = $settings;
		self::$styles    = $styles;
		self::$layouts   = $layouts;

		// Order is important. Components are checked in the order they are added.
		self::register_component( 'gallery'   ,   '\\Exporter\\Components\\Gallery'         );
		self::register_component( 'tweet'     ,   '\\Exporter\\Components\\Tweet'           );
		self::register_component( 'instagram' ,   '\\Exporter\\Components\\Instagram'       );
		self::register_component( 'img'       ,   '\\Exporter\\Components\\Image'           );
		self::register_component( 'iframe'    ,   '\\Exporter\\Components\\Embed_Web_Video' );
		self::register_component( 'video'     ,   '\\Exporter\\Components\\Video'           );
		self::register_component( 'audio'     ,   '\\Exporter\\Components\\Audio'           );
		self::register_component( 'heading'   ,   '\\Exporter\\Components\\Heading'         );
		self::register_component( 'p'         ,   '\\Exporter\\Components\\Body'            );
		self::register_component( 'blockquote',   '\\Exporter\\Components\\Quote'           );
		self::register_component( 'hr'        ,   '\\Exporter\\Components\\Divider'         );
		// Non HTML-based components
		self::register_component( 'intro'     ,   '\\Exporter\\Components\\Intro'           );
		self::register_component( 'cover'     ,   '\\Exporter\\Components\\Cover'           );
		self::register_component( 'title'     ,   '\\Exporter\\Components\\Title'           );
	}

	private static function register_component( $shortname, $classname ) {
		self::$components[ $shortname ] = $classname;
	}

	public static function get_component( $shortname, $html ) {
		$class = self::$components[ $shortname ];

		if ( is_null( $class ) ) {
			return null;
		}

		return new $class( $html, self::$workspace, self::$settings, self::$styles, self::$layouts );
	}

	/**
	 * Given a node, check all components for a match. If a match is found,
	 * return an instance of the component or an array of instances of that
	 * component.
	 *
	 * If no component matches this node, return null.
	 *
	 * FIXME: This method does a bit too much and returns different things...
	 */
	public static function get_component_from_node( $node ) {
		foreach ( self::$components as $shortname => $class ) {
			$matched_node = $class::node_matches( $node );
			if ( ! $matched_node ) {
				continue;
			}

			// Did we match a list of nodes?
			if ( $matched_node instanceof \DOMNodeList ) {
				$result = array();
				foreach ( $matched_node as $item ) {
					$html     = $node->ownerDocument->saveXML( $item );
					$result[] = self::get_component( $shortname, $html );
				}
				return $result;
			}

			// We matched a single node
			$html = $node->ownerDocument->saveXML( $matched_node );
			return self::get_component( $shortname, $html );
		}

		// Nothing was found, return null.
		return null;
	}

}
