<?php

class HeadingComponent extends Component {
    protected function build( $component ) {
        preg_match( '/<h(\d)>(.*?)<\/h\1>/im', $component, $matches );
        
        $this->json = array(
            'role' => 'heading' . $matches[1],
            'text' => $matches[2],
        );
    }
}

