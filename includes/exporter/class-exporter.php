<?php
namespace Exporter;

require_once plugin_dir_path( __FILE__ ) . 'class-component-factory.php';
require_once plugin_dir_path( __FILE__ ) . 'class-exporter-content.php';
require_once plugin_dir_path( __FILE__ ) . 'class-workspace.php';

/**
 * Export a Exporter_Content instance to Apple format. 
 *
 * TODO: This class is designed to work outside of WordPress just fine, so it
 * can be a dependency. It can be used to create other plugins, for example,
 * a Joomla or Drupal plugin. 
 *
 * NOTE: Even though this is not a WordPress class it follows it's coding
 * conventions.
 *
 * @author  Federico Ramirez
 * @since   0.0.0
 */
class Exporter {

    private $exporter_content;
    private $workspace;

    function __construct( Exporter_Content $content ) {
        $this->exporter_content = $content;
        $this->workspace = new Workspace();

        Component_Factory::initialize();
    }

    /**
     * Based on the content this instance holds, create an Article Format zipfile
     * and return the path.
     */
    public function export() {
        $this->write_to_workspace( 'article.json', $this->generate_json() );
        return $this->zip_workspace( $this->content_id() );
    }

    private function generate_json() {
        $json = array(
            'version'       => '0.1',
            'identifier'    => 'post-' . $this->content_id(),
            'language'      => 'en',
            'title'         => $this->content_title(),
            'components'    => $this->build_components(),
            // TODO: Create a Layout object
            'layout' => array(
                'columns' => 7,
                'width'   => 1024,
                'margin'  => 30,
                'gutter'  => 20,
            ),
            // Styles
            'documentStyle' => array(
                'backgroundColor' => '#F7F7F7',
            ),
            // TODO: Create a Style object
            'componentTextStyles' => array(
                'default' => array(
                    'fontName' => 'Helvetica',
                    'fontSize' => 13,
                    'linkStyle' => array( 'textColor' => '#428bca' ),
                ),
                'title' => array(
                    'fontName' => 'Helvetica-Bold',
                    'fontSize' => 30,
                    'hyphenation' => false,
                ),
                'default-body' => array(
                    'fontName' => 'Helvetica',
                    'fontSize' => 13,
                ),
            ),
        );

        return json_encode( $json );
    }

    /**
     * Isolate content dependencies.
     */
    private function content_id() {
        return $this->exporter_content->id();
    }

    private function content_title() {
        return $this->exporter_content->title();
    }

    private function content_text() {
        return $this->exporter_content->content();
    }

    private function write_to_workspace( $file, $contents ) {
        $this->workspace->write_file( $file, $contents );
    }

    private function zip_workspace( $id ) {
        return $this->workspace->zip( 'article-' . $id . '.zip' );
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
     * Given a DomNode, try to create a component from it. It it fails, return
     * null.
     */
    private function create_component_or_null( $node ) {
        $html = $node->ownerDocument->saveXML( $node );
        // GetComponent returns null if no component matches.
        return Component_Factory::GetComponent( $node->nodeName, $html, $this->workspace );
    }

    private function node_contains( $node, $tagname ) {
        if( ! method_exists( $node, 'getElementsByTagName' ) ) {
            return false;
        }

        $elements = $node->getElementsByTagName( $tagname );

        if( $elements->length == 0 ) {
            return false;
        }

        return $elements->item( 0 );
    }

    /**
     * Split components from the source WordPress content.
     */
    private function split_into_components() {
        $dom = new \DOMDocument();
        $dom->loadHTML( $this->content_text() );
        $nodes = $dom->getElementsByTagName( 'body' )->item( 0 )->childNodes;

        // Loop though the first-level nodes of the body element. Components
        // might include child-components, like an Image Gallery or Header.
        $result = array();
        foreach( $nodes as $node ) {
            $component = null;

            // Some nodes might be found nested inside another, for example an
            // <img> could be inside a <p> or <a>. Seek for them and add them.
            // The way this is beeing handled right now is pretty hacky, but
            // I'm waiting until I get a bit more code so I can figure out how
            // to do it propertly. FIXME.
            if( $image_node = $this->node_contains( $node, 'img' ) ) {
                $component = $this->create_component_or_null( $image_node );
            } else if( $ewv = $this->node_contains( $node, 'iframe' ) ) {
                $component = $this->create_component_or_null( $ewv );
            } else {
                $component = $this->create_component_or_null( $node );
            }

            $result[] = $component;
        }

        // Remove null values from result and return
        return array_filter( $result );
    }

}

