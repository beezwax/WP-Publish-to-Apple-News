<?php

require_once plugin_dir_path( __FILE__ ) . 'components/class-component.php';
require_once plugin_dir_path( __FILE__ ) . 'components/class-image-component.php';
require_once plugin_dir_path( __FILE__ ) . 'components/class-body-component.php';
require_once plugin_dir_path( __FILE__ ) . 'components/class-heading-component.php';

class Component_Factory {

    /**
     * Given a string, return an instance of the appropriate component.
     */
    public static function GetComponent( $base_string ) {
        if( Image_Component::is_match( $base_string ) ) {
            return new Image_Component( $base_string );
        } else if( Heading_Component::is_match( $base_string ) ) {
            return new Heading_Component( $base_string );
        } else {
            return new Body_Component( $base_string );
        }
    }

}
