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

/**
 * This class in in charge of creating components. Manual component
 * instantiation should be avoided, use this instead.
 *
 * @since 0.0.0
 */
class Component_Factory {

	private static $components = array();
	private static $workspace  = null;

	public static function initialize( $workspace ) {
		self::$workspace = $workspace;

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
		self::register_component( 'intro'     ,   '\\Exporter\\Components\\Intro'           );
		self::register_component( 'cover'     ,   '\\Exporter\\Components\\Cover'           );
		self::register_component( 'hr'        ,   '\\Exporter\\Components\\Divider'         );
	}

	private static function register_component( $shortname, $classname ) {
		self::$components[$shortname] = $classname;
	}

	public static function get_component( $shortname, $node ) {
		$class = self::$components[$shortname];

		if ( is_null( $class ) ) {
			return null;
		}

		$html = $node->ownerDocument->saveXML( $node );
		return new $class( $html, self::$workspace );
	}

	public static function get_component_from_node( $node ) {
		foreach ( self::$components as $shortname => $class ) {
			if( $matched_node = $class::node_matches( $node ) ) {
				return self::get_component( $shortname, $matched_node );
			}
		}

		// Ignore every other tags
		return null;
	}

}
