<?php

class ImageComponent extends Component {
    protected function build( $component ) {
        $matches = array();
        preg_match( '/src="(.*?)"/imU', $component, $matches );
        $url = $matches[1];

        $this->json = array(
            'role' => 'photo',
            'text' => 'bundle://' . $url,
        );
    }
}

