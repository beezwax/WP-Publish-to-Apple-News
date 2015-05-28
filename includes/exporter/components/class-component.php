<?php

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
