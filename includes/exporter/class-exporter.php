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

    /**
     * The generated JSON file stored as an associative array.
     * @since 0.0.0
     */
    private $json;

    function __construct( $post ) {
        $this->post = $post;
        $this->json = array(
            'version'       => '0.1',
            'identifier'    => $post->ID,
            'language'      => 'en',
            'title'         => $post->post_title,
            'components'    => array(),
        );
    }

    /**
     * Based on the post this instance holds, create an Article Format zipfile
     * and return the path.
     */
    public function export() {
        $this->build_components();
        return json_encode( $this->json );
    }

    /**
     * Builds up the internal JSON representation of the article.
     */
    private function build_components() {
        foreach( $this->split_into_components() as $component ) {
            $this->json['components'][] = $component->value();
        }
    }

    /**
     * Isolate post dependency. Get the content.
     */
    private function post_content() {
        return $this->post->post_content;
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

