<?php

require_once plugin_dir_path( __FILE__ ) . 'components/class-component.php';
require_once plugin_dir_path( __FILE__ ) . 'components/class-image-component.php';
require_once plugin_dir_path( __FILE__ ) . 'components/class-body-component.php';

class ComponentFactory {

    public static function GetComponent( $component ) {
        if( self::is_image( $component ) ) {
            return new ImageComponent( $component );
        } else {
            return new BodyComponent( $component );
        }
    }

    private static function is_image( $component ) {
        return strpos( $component, '<img' ) !== false;
    }

}
