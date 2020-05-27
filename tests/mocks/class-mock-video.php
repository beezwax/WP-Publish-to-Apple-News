<?php
namespace Apple_Exporter\Components;
class MockVideo extends Video {
    protected static function remote_file_exists( $node ) {
        return true;
    }
}
