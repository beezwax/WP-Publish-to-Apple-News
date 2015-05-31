<?php
namespace Exporter;

use \ZipArchive as ZipArchive;
use \RecursiveIteratorIterator as RecursiveIteratorIterator;
use \RecursiveDirectoryIterator as RecursiveDirectoryIterator;

/**
 * Manage the exporter's workspace. This class is able to write to the
 * workspace as well as zipping it.
 *
 * @author  Federico Ramirez
 * @since   0.0.0
 */
class Workspace {

    private $path;

    function __construct() {
        $this->path = realpath( plugin_dir_path( __FILE__ ) . '../../workspace' ) . '/';

        if( ! file_exists( $this->path ) ) {
            mkdir( $this->path, 0775, true );
        }
    }

    /**
     * Delete all files from the workspace directory.
     *
     * @since   0.0.0
     */
    private function clean_workspace() {
        $files = glob( $this->path . '*', GLOB_BRACE );
        foreach( $files as $file ) {
            if( is_file( $file ) ) {
                unlink( $file );
            }
        }
    }

    /**
     * Write a file to the workspace.
     *
     * @since   0.0.0
     */
    public function write_file( $file, $contents ) {
        file_put_contents( $this->path . $file, $contents );
    }

    /**
     * Compresses the workspace directory recursively the ZIP algorhythm.
     *
     * @since   0.0.0
     * @return  The full path to the generated zipfile
     */
    public function zip() {
        $zipfile_path = realpath( $this->path . '..' ) . '/article.zip';

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
        $this->clean_workspace();
        return $zipfile_path;
    }

}
