<?php

abstract class Component {
    protected $json;
    protected $workspace_path;

    function __construct( $component ) {
        $this->workspace_path = plugin_dir_path( __FILE__ ) . '../../../workspace/';
        $this->build( $component );
    }

    public function value() {
        return $this->json;
    }

    abstract protected function build( $component );
}
