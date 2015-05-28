<?php

require_once plugin_dir_path( __FILE__ ) . 'components/class-component.php';
require_once plugin_dir_path( __FILE__ ) . 'components/class-image-component.php';
require_once plugin_dir_path( __FILE__ ) . 'components/class-body-component.php';
require_once plugin_dir_path( __FILE__ ) . 'components/class-heading-component.php';

class ComponentFactory {

    /**
     * Given a string, return an instance of the appropriate component.
     */
    public static function GetComponent( $base_string ) {
        if( ImageComponent::is_match( $base_string ) ) {
            return new ImageComponent( $base_string );
        } else if( HeadingComponent::is_match( $base_string ) ) {
            return new HeadingComponent( $base_string );
        } else {
            return new BodyComponent( $base_string );
        }
    }

}
