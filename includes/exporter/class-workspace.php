<?php

/**
 * Manage the exporter's workspace.
 */
class Workspace {

    private $path;

    function __construct() {
        $this->path = plugin_dir_path( __FILE__ ) . '../../workspace/';
    }

    public function write_file( $file, $contents ) {
        file_put_contents( $this->path . $file, $contents );
    }

}
