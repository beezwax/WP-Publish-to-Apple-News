<?php
namespace Exporter\Components;

class Heading extends Component {

    protected function build( $text ) {
        preg_match( '/<h(\d)>(.*?)<\/h\1>/im', $text, $matches );
        
        $this->json = array(
            'role' => 'heading' . $matches[1],
            'text' => $matches[2],
            'textStyle' => 'title',
        );
    }

}

