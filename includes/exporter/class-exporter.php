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

    /**
     * Split components from the source WordPress content.
     */
    private function split_into_components() {
        $dom = new \DOMDocument();
        $dom->loadHTML( $this->content_text() );
        $nodes = $dom->getElementsByTagName( 'body' )->item( 0 )->childNodes;

        $result = array();
        foreach( $nodes as $node ) {
            // Some nodes might be found nested inside another, like a <p> or
            // <a>. Seek for them and add them. For now, there's only img which
            // has to be treated like this, but there might be more, so FIXME.
            if( method_exists( $node, 'getElementsByTagName' ) && $node->getElementsByTagName( 'img' )->length > 0 ) {
                $image_node = $node->getElementsByTagName( 'img' )->item(0);
                $result[] = $this->create_component_or_null( $image_node );
                continue;
            }

            $this->create_component_or_null( $node );
        }

        // Remove null values from result and return
        return array_filter( $result );
    }

}

