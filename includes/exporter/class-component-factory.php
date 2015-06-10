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

		self::register_component( 'img'       ,   '\\Exporter\\Components\\Image'           );
		self::register_component( 'p'         ,   '\\Exporter\\Components\\Body'            );
		self::register_component( 'blockquote',   '\\Exporter\\Components\\Quote'           );
		self::register_component( 'h[1-6]'    ,   '\\Exporter\\Components\\Heading'         );
		self::register_component( 'iframe'    ,   '\\Exporter\\Components\\Embed_Web_Video' );
		self::register_component( 'intro'     ,   '\\Exporter\\Components\\Intro'           );
		self::register_component( 'cover'     ,   '\\Exporter\\Components\\Cover'           );
		self::register_component( 'gallery'   ,   '\\Exporter\\Components\\Gallery'         );
		self::register_component( 'tweet'     ,   '\\Exporter\\Components\\Tweet'           );
		self::register_component( 'instagram' ,   '\\Exporter\\Components\\Instagram'       );
		self::register_component( 'video'     ,   '\\Exporter\\Components\\Video'           );
		self::register_component( 'audio'     ,   '\\Exporter\\Components\\Audio'           );
		self::register_component( 'hr'        ,   '\\Exporter\\Components\\Divider'         );
	}

	private static function register_component( $tagname, $classname ) {
		self::$components[ $tagname ] = $classname;
	}

	private static function find_component_by_tagname( $tagname ) {
		foreach ( array_keys( self::$components ) as $key ) {
			if ( preg_match( '@' . $key . '@', $tagname ) ) {
				return self::$components[ $key ];
			}
		}

		return null;
	}

	public static function get_component( $tagname, $html ) {
		$class = self::find_component_by_tagname( $tagname );

		if ( is_null( $class ) ) {
			return null;
		}

		return new $class( $html, self::$workspace );
	}

	/**
	 * Given a string, return an instance of the appropriate component or null if
	 * no component matches the given tagname.
	 */
	private static function get_component_or_null( $node, $name = null ) {
		$tagname = $name ?: $node->nodeName;
		$html    = $node->ownerDocument->saveXML( $node );

		return self::get_component( $tagname, $html );
	}

	private static function node_find_by_tagname( $node, $tagname ) {
		if ( ! method_exists( $node, 'getElementsByTagName' ) ) {
			return false;
		}

		$elements = $node->getElementsByTagName( $tagname );

		if ( $elements->length == 0 ) {
			return false;
		}

		return $elements->item( 0 );
	}


	private static function node_has_class( $node, $classname ) {
		if ( ! method_exists( $node, 'getAttribute' ) ) {
			return false;
		}

		$classes = trim( $node->getAttribute( 'class' ) );

		if ( empty( $classes ) ) {
			return false;
		}

		return 1 == preg_match( "/(?:\s+|^)$classname(?:\s+|$)/", $classes );
	}

	public static function get_component_from_node( $node ) {
		// Some nodes might be found nested inside another, for example an
		// <img> could be inside a <p> or <a>. Seek for them and add them.
		// The way this is beeing handled right now is pretty hacky, but
		// I'm waiting until I get a bit more code so I can figure out how
		// to do it propertly. FIXME.
		if ( self::node_has_class( $node, 'gallery' ) ) {
			return self::get_component_or_null( $node, 'gallery' );
		}

		if ( self::node_has_class( $node, 'twitter-tweet' ) ) {
			return self::get_component_or_null( $node, 'tweet' );
		}

		if ( self::node_has_class( $node, 'instagram-media' ) ) {
			return self::get_component_or_null( $node, 'instagram' );
		}

		if ( $image_node = self::node_find_by_tagname( $node, 'img' ) ) {
			return self::get_component_or_null( $image_node );
		}

		if ( $ewv = self::node_find_by_tagname( $node, 'iframe' ) ) {
			return self::get_component_or_null( $ewv );
		}

		if ( $video = self::node_find_by_tagname( $node, 'video' ) ) {
			return self::get_component_or_null( $video );
		}

		if ( $audio = self::node_find_by_tagname( $node, 'audio' ) ) {
			return self::get_component_or_null( $audio );
		}

		if ( self::node_find_by_tagname( $node, 'script' ) ) {
			// Ignore script tags.
			return null;
		}

		return self::get_component_or_null( $node );
	}

}
