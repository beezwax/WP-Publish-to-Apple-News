<?php
namespace Exporter;

require_once plugin_dir_path( __FILE__ ) . 'components/class-component.php';
require_once plugin_dir_path( __FILE__ ) . 'components/class-image.php';
require_once plugin_dir_path( __FILE__ ) . 'components/class-body.php';
require_once plugin_dir_path( __FILE__ ) . 'components/class-heading.php';

class Component_Factory {

    private static $components = array();

    public static function initialize() {
        self::register_component( 'img',      '\\Exporter\\Components\\Image' );
        self::register_component( 'p',        '\\Exporter\\Components\\Body' );
        self::register_component( 'h[1-6]', '\\Exporter\\Components\\Heading' );
    }

    private static function register_component( $tagname, $classname ) {
        self::$components[ $tagname ] = $classname;
    }

    private static function get_component( $tagname ) {
        foreach( array_keys( self::$components ) as $key ) {
            if( preg_match( '@' . $key . '@', $tagname ) ) {
                return self::$components[ $key ];
            }
        }

        return null;
    }

    /**
     * Given a string, return an instance of the appropriate component.
     */
    public static function GetComponent( $tagname, $html, $workspace ) {
        $class = self::get_component( $tagname );

        if( is_null( $class ) ) {
            return null;
        }

        return new $class( $html, $workspace );
    }

}
