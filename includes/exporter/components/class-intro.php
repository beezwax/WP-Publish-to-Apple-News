<?php
namespace Exporter\Components;

class Intro extends Component {
    protected function build( $text ) {
        $this->json = array(
            'role' => 'intro',
            'text' => $text,
        );
    }
}

