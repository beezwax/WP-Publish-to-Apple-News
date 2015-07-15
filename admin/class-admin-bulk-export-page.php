<?php

require_once plugin_dir_path( __FILE__ ) . 'actions/index/class-push.php';

/**
 * Bulk export page. Display progress on multiple articles export process.
 *
 * @since 0.6.0
 */
class Admin_Bulk_Export_Page extends Apple_Export {

	private $settings;

	function __construct( $settings ) {
		$this->settings = $settings;

		add_action( 'admin_menu', array( $this, 'register_page' ) );
		add_action( 'wp_ajax_push_post', array( $this, 'ajax_push_post' ) );
	}

	public function register_page() {
		$this->register_assets();

		add_submenu_page(
			null,                                // Parent, if null, it won't appear in any menu
			'Bulk Export',                       // Page title
			'Bulk Export',                       // Menu title
			'manage_options',                    // Capability
			$this->plugin_slug . '_bulk_export', // Menu Slug
			array( $this, 'build_page' )         // Function
	 	);
	}

	public function build_page() {
		$ids = @$_REQUEST['ids'];
		if ( ! $ids ) {
			wp_redirect( menu_page_url( $this->plugin_slug . '_index', false ) );
			return;
		}

		// Populate $articles array with a set of valid posts
		$articles = array();
		foreach( explode( '.', $ids ) as $id ) {
			if ( $post = get_post( $id ) ) {
				$articles[] = $post;
			}
		}

		require_once plugin_dir_path( __FILE__ ) . 'partials/page_bulk_export.php';
	}

	public function ajax_push_post() {
		$id     = intval( $_REQUEST['id'] );
		// TODO: Move push action to shared
		$action = new Actions\Index\Push( $this->settings, $id );
		$errors = $action->perform();

		if ( $errors ) {
			echo json_encode( array(
				'success' => false,
				'error'   => $errors,
			) );
		} else {
			echo json_encode( array(
				'success' => true,
			) );
		}

		// This is required to terminate immediately and return a valid response
		wp_die();
	}

	private function register_assets() {
		wp_enqueue_style( $this->plugin_slug . '_bulk_export_css', plugin_dir_url(
			__FILE__ ) .  '../assets/css/bulk-export.css' );
		wp_enqueue_script( $this->plugin_slug . '_bulk_export_js', plugin_dir_url(
			__FILE__ ) .  '../assets/js/bulk-export.js', array( 'jquery' ),
			$this->version, true );
	}

}
