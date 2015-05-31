<?php

class Image_Component extends Component {

    public static function is_match( $string ) {
        return strpos( $string, '<img' ) !== false;
    }

    protected function build( $text ) {
        $matches = array();
        preg_match( '/src="([^"]*?)"/im', $text, $matches );
        $url = $matches[1];
        $filename = array_pop( explode( '/', $url ) );

        // Save image into bundle
        $this->workspace->write_file( $filename, file_get_contents( $url ) );

        $this->json = array(
            'role' => 'photo',
            'text' => 'bundle://' . $filename,
        );
    }

}

