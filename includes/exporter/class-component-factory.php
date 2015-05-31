<?php

require_once plugin_dir_path( __FILE__ ) . 'components/class-component.php';
require_once plugin_dir_path( __FILE__ ) . 'components/class-image-component.php';
require_once plugin_dir_path( __FILE__ ) . 'components/class-body-component.php';
require_once plugin_dir_path( __FILE__ ) . 'components/class-heading-component.php';

class Component_Factory {

    /**
     * Given a string, return an instance of the appropriate component.
     */
    public static function GetComponent( $text, $workspace ) {
        if( Image_Component::is_match( $text ) ) {
            return new Image_Component( $text, $workspace );
        } else if( Heading_Component::is_match( $text ) ) {
            return new Heading_Component( $text, $workspace );
        } else {
            return new Body_Component( $text, $workspace );
        }
    }

}
