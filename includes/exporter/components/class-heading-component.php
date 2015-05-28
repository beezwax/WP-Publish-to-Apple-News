<?php

class HeadingComponent extends Component {

    public static function is_match( $string ) {
        return preg_match( '/<h(\d)>(?:.*?)<\/h\1>/im', $string ) === 1;
    }

    protected function build( $component ) {
        preg_match( '/<h(\d)>(.*?)<\/h\1>/im', $component, $matches );
        
        $this->json = array(
            'role' => 'heading' . $matches[1],
            'text' => $matches[2],
        );
    }

}

