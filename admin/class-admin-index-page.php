<?php
/**
 * Entry point for the admin side of the WP Plugin.
 *
 * @author  Federico Ramirez
 * @since   0.6.0
 */

require_once plugin_dir_path( __FILE__ ) . 'actions/index/class-push.php';
require_once plugin_dir_path( __FILE__ ) . 'actions/index/class-delete.php';
require_once plugin_dir_path( __FILE__ ) . 'actions/index/class-export.php';
require_once plugin_dir_path( __FILE__ ) . 'class-admin-export-list-table.php';

class Admin_Index_Page extends Apple_Export {

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

		add_action( 'admin_menu', array( $this, 'setup_admin_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'setup_assets' ) );
	}

	/**
	 * Sets up the admin page.
	 *
	 * @access public
	 */
	public function setup_admin_page() {
		// Set up main page. This page reads parameters and handles actions
		// accordingly.
		add_menu_page(
			__( 'Apple News', 'apple-news' ),	// Page Title
			__( 'Apple News', 'apple-news' ),	// Menu Title
			'manage_options',              		// Capability
			$this->plugin_slug . '_index', 		// Menu Slug
			array( $this, 'page_router' ), 		// Function
			'dashicons-format-aside'       		// Icon
		);
	}

	/**
	 * Sets up all pages used in the plugin's admin page. Associate each route
	 * with an action. Actions are methods that end with "_action" and must
	 * perform a task and output HTML with the result.
	 *
	 * FIXME: Regarding this class doing too much, maybe split all actions into
	 * their own class.
	 *
	 * @since 0.4.0
	 * @return mixed
	 * @access public
	 */
	public function page_router() {
		$id     = isset( $_GET['post_id'] ) ? intval( $_GET['post_id'] ) : null;
		$action = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : null;

		// Given an action and ID, map the attributes to corresponding actions.

		if ( ! $id ) {
			switch ( $action ) {
			case 'push':
				$url  = menu_page_url( $this->plugin_slug . '_bulk_export', false );
				$url .= '&ids=' . implode( '.', $_GET['article'] );
				wp_safe_redirect( $url );
				exit;
			default:
				return $this->show_post_list_action();
			}
		}

		switch ( $action ) {
		case 'settings':
			return $this->settings_action( $id );
		case 'export':
			return $this->export_action( $id );
		case 'push':
			return $this->push_action( $id );
		case 'delete':
			return $this->delete_action( $id );
		default:
			wp_die( __( 'Invalid action: ', 'apple-news' ) . $action );
		}
	}

	/**
	 * Shows a success message.
	 *
	 * @param string $message
	 * @access public
	 */
	private function flash_success( $message ) {
		Flash::success( $message );
		wp_redirect( menu_page_url( $this->plugin_slug . '_index', false ) );
		wp_die(); // Ignore everything else that would be rendered otherwise
	}

	/**
	 * Shows an error message.
	 *
	 * @param string $message
	 * @access public
	 */
	private function flash_error( $message ) {
		Flash::error( $message );
		wp_redirect( menu_page_url( $this->plugin_slug . '_index', false ) );
		wp_die(); // Ignore everything else that would be rendered otherwise
	}

	/**
	 * Gets a setting by name which was loaded from WordPress options.
	 *
	 * @since 0.4.0
	 * @param string $name
	 * @return mixed
	 * @access private
	 */
	private function get_setting( $name ) {
		return $this->settings->get( $name );
	}

	/**
	 * Downloads the JSON file for troubleshooting purposes.
	 *
	 * @param string $json
	 * @param int $id
	 * @access private
	 */
	private function download_json( $json, $id ) {
		header( 'Content-Type: application/json' );
		header( 'Content-Disposition: attachment; filename="article-' . absint( $id ) . '.json"' );
		ob_clean();
		flush();
		echo $json;
		exit;
	}

	/**
	 * Sets up admin assets.
	 *
	 * @access public
	 */
	public function setup_assets() {
		wp_enqueue_script( $this->plugin_slug . '_zeroclipboard', plugin_dir_url(
			__FILE__) .  '../vendor/zeroclipboard/ZeroClipboard.min.js', array(
				'jquery' ), $this->version, true );

		wp_enqueue_script( $this->plugin_slug . '_export_table_js', plugin_dir_url(
			__FILE__ ) .  '../assets/js/export-table.js', array( 'jquery',
			$this->plugin_slug . '_zeroclipboard' ), $this->version, true );
	}

	/**
	 * Shows a post from the list table.
	 *
	 * @access private
	 */
	private function show_post_list_action() {
		$table = new Admin_Export_List_Table();
		$table->prepare_items();
		include plugin_dir_path( __FILE__ ) . 'partials/page_index.php';
	}

	/**
	 * Handles all settings actions.
	 *
	 * @param int $id
	 * @access private
	 */
	private function settings_action( $id ) {
		if ( isset( $_POST['pullquote'] ) ) {
			update_post_meta( $id, 'apple_export_pullquote', sanitize_text_field( $_POST['pullquote'] ) );
		}

		if ( isset( $_POST['pullquote_position'] ) ) {
			update_post_meta( $id, 'apple_export_pullquote_position', sanitize_text_field( $_POST['pullquote_position'] ) );
			$message = __( 'Settings saved.', 'apple-news' );
		}

		$post      = get_post( $id );
		$post_meta = get_post_meta( $id );
		include plugin_dir_path( __FILE__ ) . 'partials/page_single_settings.php';
	}

	/**
	 * Handles an export action.
	 *
	 * @param int $id
	 * @access private
	 */
	private function export_action( $id ) {
		$action = new Actions\Index\Export( $this->settings, $id );
		try {
			$json = $action->perform();
			$this->download_json( $json, $id );
		} catch ( Actions\Action_Exception $e ) {
			$this->flash_error( $e->getMessage() );
		}
	}

	/**
	 * Handles a push to Apple News action.
	 *
	 * @param int $id
	 * @access private
	 */
	private function push_action( $id ) {
		$action = new Actions\Index\Push( $this->settings, $id );
		try {
			$action->perform();
			$this->flash_success( __( 'Your article has been pushed successfully!', 'apple-news' ) );
		} catch ( Actions\Action_Exception $e ) {
			$this->flash_error( $e->getMessage() );
		}
	}

	/**
	 * Handles a delete from Apple News action.
	 *
	 * @param int $id
	 * @access private
	 */
	private function delete_action( $id ) {
		$action = new Actions\Index\Delete( $this->settings, $id );
		try {
			$action->perform();
			$this->flash_success( __( 'Your article has been removed from apple news.', 'apple-news' ) );
		} catch ( Actions\Action_Exception $e ) {
			$this->flash_error( $e->getMessage() );
		}
	}

}
