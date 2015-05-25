<?php
/**
 * Entry point for the admin side of the WP Plugin.
 *
 * @author  Federico Ramirez
 * @since   0.0.0
 */
class Admin_Apple_Export extends Apple_Export
{
    function __construct() {
        // Register hooks
        add_action( 'admin_menu', array( $this, 'setup_pages' ) );
    }

    public function setup_pages() {
        $this->page_index();
        $this->page_options();
    }

    /**
     * Index page setup
     */
    public function page_index() {
        add_menu_page( 
            'Apple Export',
            'Apple Export', 
            'manage_options',
            $this->plugin_name . '_index',
            array( $this, 'page_index_render' ) 
        );
    }

    public function page_index_render() {
        include plugin_dir_path( __FILE__ ) . 'partials/page_index.php';
    }

    /**
     * Options page setup
     */
    public function page_options() {
        add_options_page( 
            'Apple Export Options',
            'Apple Export',
            'manage_options',
            $this->plugin_name . '_options', 
            array( $this, 'page_options_render' ) 
        );
    }

    public function page_options_render() {
        if( ! current_user_can( 'manage_options' ) )
            wp_die( __( 'You do not have permissions to access this page.' ) );

        include plugin_dir_path( __FILE__ ) . 'partials/page_options.php';
    }
}
