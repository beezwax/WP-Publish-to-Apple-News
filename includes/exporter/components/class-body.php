<?php
namespace Exporter\Components;

class Body extends Component {
    protected function build( $text ) {
        $this->json = array(
            'role' => 'body',
            'text' => $text,
        );
    }
}

