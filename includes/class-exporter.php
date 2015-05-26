<?php
/**
 * Export a post to Apple format.
 *
 * @author  Federico Ramirez
 * @since   0.0.0
 */
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

    public function export() {
        $this->add_components();
        return json_encode( $this->json );
    }

    /**
     * Adds all the components for the JSON
     */
    private function add_components() {
        foreach( $this->post_components() as $component ) {
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
     * Split components.
     */
    private function post_components() {
        $result = array();
        foreach( preg_split( "/(\n|\r\n|\r){3,}/", $this->post_content() ) as $component ) {
            $result[] = ComponentFactory::GetComponent( $component );
        }
        return $result;
    }

}

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

abstract class Component {
    protected $json;

    function __construct( $component ) {
        $this->build( $component );
    }

    public function value() {
        return $this->json;
    }

    abstract protected function build( $component );
}

class BodyComponent extends Component {
    protected function build( $component ) {
        $this->json = array(
            'role' => 'body',
            'text' => $component,
        );
    }
}

class ImageComponent extends Component {
    protected function build( $component ) {
        $matches = array();
        preg_match( '/src="(.*?)"/imU', $component, $matches );
        $url = $matches[1];

        $this->json = array(
            'role' => 'photo',
            'text' => 'bundle://' . $url,
        );
    }
}
