<?php

require_once plugin_dir_path( __FILE__ ) . '../class-workspace.php';

abstract class Component {
    protected $json;
    protected $workspace;

    function __construct( $text ) {
        $this->workspace = new Workspace();
        $this->build( $text );
    }

    public function value() {
        return $this->json;
    }

    abstract protected function build( $text );
}
