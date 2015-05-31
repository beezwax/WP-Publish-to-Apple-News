<?php

/**
 * Represents a generic way to represent content that must be exported. This 
 * can be filled based on a WordPress post by example.
 */
class Exporter_Content {

    private $id;
    private $title;
    private $content;

    function __construct( $id, $title, $content ) {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
    }

    public function id() {
        return $this->id;
    }

    public function title() {
        return $this->title;
    }

    public function content() {
        return $this->content;
    }

}
