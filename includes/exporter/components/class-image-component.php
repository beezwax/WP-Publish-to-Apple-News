<?php

class Image_Component extends Component {

    public static function is_match( $string ) {
        return strpos( $string, '<img' ) !== false;
    }

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

