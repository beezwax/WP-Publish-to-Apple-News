<?php

require_once plugin_dir_path( __FILE__ ) . 'actions/index/class-push.php';

/**
 * Bulk export page. Display progress on multiple articles export process.
 *
 * @since 0.6.0
 */
class Admin_Apple_Bulk_Export_Page extends Apple_News {

	/**
	 * Current plugin settings.
	 *
	 * @var array
	 * @access private
	 */
	private $settings;

	/**
	 * Constructor.
	 */
	function __construct( $settings ) {
		$this->settings = $settings;

		add_action( 'admin_menu', array( $this, 'register_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_assets' ) );
		add_action( 'wp_ajax_push_post', array( $this, 'ajax_push_post' ) );
	}

	/**
	 * Registers the plugin submenu page.
	 *
	 * @access public
	 */
	public function register_page() {
		add_submenu_page(
			null,                                // Parent, if null, it won't appear in any menu
			__( 'Bulk Export', 'apple-news' ),   // Page title
			__( 'Bulk Export', 'apple-news' ),   // Menu title
			apply_filters( 'apple_news_bulk_export_capability', 'manage_options' ),	// Capability
			$this->plugin_slug . '_bulk_export', // Menu Slug
			array( $this, 'build_page' )         // Function
	 	);
	}

	/**
	 * Builds the plugin submenu page.
	 *
	 * @access public
	 */
	public function build_page() {
		$ids = isset( $_GET['ids'] ) ? sanitize_text_field( $_GET['ids'] ) : null;
		if ( ! $ids ) {
			wp_safe_redirect( menu_page_url( $this->plugin_slug . '_index', false ) );
			exit;
		}

		// Populate $articles array with a set of valid posts
		$articles = array();
		foreach ( explode( '.', $ids ) as $id ) {
			if ( $post = get_post( absint( $id ) ) ) {
				$articles[] = $post;
			}
		}

		require_once plugin_dir_path( __FILE__ ) . 'partials/page_bulk_export.php';
	}

	/**
	 * Handles the ajax action to push a post to Apple News.
	 *
	 * @access public
	 */
	public function ajax_push_post() {
		$id = absint( $_GET['id'] );

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

	/**
	 * Registers assets used by the bulk export process.
	 *
	 * @access public
	 */
	public function register_assets() {
		wp_enqueue_style( $this->plugin_slug . '_bulk_export_css', plugin_dir_url(
			__FILE__ ) .  '../assets/css/bulk-export.css' );
		wp_enqueue_script( $this->plugin_slug . '_bulk_export_js', plugin_dir_url(
			__FILE__ ) .  '../assets/js/bulk-export.js', array( 'jquery' ),
			$this->version, true );
	}

}
