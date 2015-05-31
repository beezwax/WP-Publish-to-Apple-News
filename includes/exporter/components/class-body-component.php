<?php

class Body_Component extends Component {
    protected function build( $text ) {
        $this->json = array(
            'role' => 'body',
            'text' => $text,
        );
    }
}

