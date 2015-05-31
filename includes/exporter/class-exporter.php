<?php
/**
 * Export a Exporter_Content instance to Apple format.
 *
 * @author  Federico Ramirez
 * @since   0.0.0
 */

require_once plugin_dir_path( __FILE__ ) . 'class-component-factory.php';
require_once plugin_dir_path( __FILE__ ) . 'class-exporter-content.php';

class Exporter {

    private $base_content;

    function __construct( Exporter_Content $content ) {
        $this->base_content = $content;
    }

    /**
     * Based on the content this instance holds, create an Article Format zipfile
     * and return the path.
     */
    public function export() {
        $json = array(
            'version'       => '0.1',
            'identifier'    => 'post-' . $this->content_id(),
            'language'      => 'en',
            'title'         => $this->content_title(),
            'components'    => $this->build_components(),
            // TODO: Create a Style object
            'componentTextStyles' => array(
                'default' => array(
                    'fontName' => 'Helvetica',
                    'fontSize' => 13,
                    'linkStyle' => array( 'textColor' => '#428bca' ),
                ),
            ),
            // TODO: Create a Layout object
            'layout' => array(
                'columns' => 7,
                'width'   => 1024,
                'margin'  => 30,
                'gutter'  => 20,
            ),
        );

        return json_encode( $json );
    }

    /**
     * Isolate content dependencies.
     */
    private function content_id() {
        return $this->base_content->id();
    }

    private function content_title() {
        return $this->base_content->title();
    }

    private function content_text() {
        return $this->base_content->content();
    }

    /**
     * Builds an array with all the components of this WordPress content.
     */
    private function build_components() {
        $components = array();
        foreach( $this->split_into_components() as $component ) {
            $components[] = $component->value();
        }
        return $components;
    }

    /**
     * Split components from the source WordPress content.
     */
    private function split_into_components() {
        $result = array();
        foreach( preg_split( "/(\n|\r\n|\r){3,}/", $this->content_text() ) as $component ) {
            $result[] = Component_Factory::GetComponent( $component );
        }
        return $result;
    }

}

