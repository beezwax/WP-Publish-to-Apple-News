<?php
namespace Exporter;

require_once plugin_dir_path( __FILE__ ) . 'components/class-component.php';
require_once plugin_dir_path( __FILE__ ) . 'components/class-image.php';
require_once plugin_dir_path( __FILE__ ) . 'components/class-body.php';
require_once plugin_dir_path( __FILE__ ) . 'components/class-heading.php';

class Component_Factory {

    /**
     * Given a string, return an instance of the appropriate component.
     */
    public static function GetComponent( $text, $workspace ) {
        if( Components\Image::is_match( $text ) ) {
            return new Components\Image( $text, $workspace );
        } else if( Components\Heading::is_match( $text ) ) {
            return new Components\Heading( $text, $workspace );
        } else {
            return new Components\Body( $text, $workspace );
        }
    }

}
