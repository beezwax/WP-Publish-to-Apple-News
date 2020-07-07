<?php
namespace Apple_Exporter\Components;
class MockAudio extends Audio {
    protected static function remote_file_exists( $node ) {
        return true;
    }
}
