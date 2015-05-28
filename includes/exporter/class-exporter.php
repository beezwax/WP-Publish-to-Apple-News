<?php
/**
 * Export a post to Apple format.
 *
 * @author  Federico Ramirez
 * @since   0.0.0
 */

require_once plugin_dir_path( __FILE__ ) . 'class-component-factory.php';

class Exporter {

    private $post;

    function __construct( $post ) {
        $this->post = $post;
    }

    /**
     * Based on the post this instance holds, create an Article Format zipfile
     * and return the path.
     */
    public function export() {
        $json = array(
            'version'       => '0.1',
            'identifier'    => 'post-' . $this->post_id(),
            'language'      => 'en',
            'title'         => $this->post_title(),
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
     * Builds an array with all the components of this WordPress post.
     */
    private function build_components() {
        $components = array();
        foreach( $this->split_into_components() as $component ) {
            $components[] = $component->value();
        }
        return $components;
    }

    /**
     * Isolate post dependencies.
     */
    private function post_content() {
        return $this->post->post_content;
    }

    private function post_id() {
        return $this->post->ID;
    }

    private function post_title() {
        return $this->post->post_title;
    }

    /**
     * Split components from the source WordPress post.
     */
    private function split_into_components() {
        $result = array();
        foreach( preg_split( "/(\n|\r\n|\r){3,}/", $this->post_content() ) as $component ) {
            $result[] = ComponentFactory::GetComponent( $component );
        }
        return $result;
    }

}

