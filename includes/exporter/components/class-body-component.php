<?php

class Body_Component extends Component {
    protected function build( $component ) {
        $this->json = array(
            'role' => 'body',
            'text' => $component,
        );
    }
}

