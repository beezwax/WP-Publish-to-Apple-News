<?php

/**
 * Manage the exporter's workspace.
 */
class Workspace {

    private $path;

    function __construct() {
        $this->path = realpath( plugin_dir_path( __FILE__ ) . '../../workspace' ) . '/';
    }

    public function write_file( $file, $contents ) {
        file_put_contents( $this->path . $file, $contents );
    }

    public function zip() {
        $zipfile_path = realpath( $this->path . '../article.zip' );

        $zip = new ZipArchive();
        $zip->open( $zipfile_path, ZipArchive::CREATE | ZipArchive::OVERWRITE );

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator( $this->path ),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach( $files as $name => $file ) {
            if( ! $file->isDir() ) {
                $filePath = $file->getRealPath();
                $relativePath = substr( $filePath, strlen( $this->path ) );

                $zip->addFile( $filePath, $relativePath );
            }
        }

        $zip->close();
        return $zipfile_path;
    }

}
