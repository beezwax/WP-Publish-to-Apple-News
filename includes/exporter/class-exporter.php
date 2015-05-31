<?php
/**
 * Export a Exporter_Content instance to Apple format. 
 * TODO: This class is designed to work outside of WordPress just fine, so it
 * can be a dependency. It can be used to create other plugins, for example,
 * a Joomla or Drupal plugin.
 *
 * @author  Federico Ramirez
 * @since   0.0.0
 */

require_once plugin_dir_path( __FILE__ ) . 'class-component-factory.php';
require_once plugin_dir_path( __FILE__ ) . 'class-exporter-content.php';
require_once plugin_dir_path( __FILE__ ) . 'class-workspace.php';

class Exporter {

    private $exporter_content;
    private $workspace;

    function __construct( Exporter_Content $content ) {
        $this->exporter_content = $content;
        $this->workspace = new Workspace();
    }

    /**
     * Based on the content this instance holds, create an Article Format zipfile
     * and return the path.
     */
    public function export() {
        $json = $this->generate_json();
        $this->write_to_workspace( 'article.json', $json );
        return $this->workspace->zip();
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
            $result[] = Component_Factory::GetComponent( $component, $this->workspace );
        }
        return $result;
    }

}

