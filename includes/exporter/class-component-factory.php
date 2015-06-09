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

	/**
	 * Given a string, return an instance of the appropriate component or null if
	 * no component matches the given tagname.
	 */
	public static function get_component( $tagname, $html ) {
		$class = self::find_component_by_tagname( $tagname );

		if ( is_null( $class ) ) {
			return null;
		}

		return new $class( $html, self::$workspace );
	}

}
