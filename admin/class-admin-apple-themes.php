<?php
/**
 * This class is in charge of handling the management of Apple News themes.
 */
class Admin_Apple_Themes extends Apple_News {

	/**
	 * Theme management page name.
	 *
	 * @var string
	 * @access private
	 */
	private $page_name;

	/**
	 * Key for the theme index.
	 *
	 * @var string
	 * @access private
	 */
	private $theme_index_key = 'apple_news_installed_themes';

	/**
	 * Prefix for individual theme keys.
	 *
	 * @var string
	 * @access private
	 */
	private $theme_key_prefix = 'apple_news_theme_';

	/**
	 * Valid actions handled by this class and their callback functions.
	 *
	 * @var array
	 * @access private
	 */
	private $actions = array(
		'apple_news_create_theme' => array( $this, 'create_theme' ),
		'apple_news_upload_theme' => array( $this, 'upload_theme' ),
		'apple_news_delete_theme' => array( $this, 'delete_theme' ),
		'apple_news_set_theme' => array( $this, 'set_theme' ),
	);

	/**
	 * Constructor.
	 */
	function __construct() {
		$this->page_name = $this->plugin_domain . '-themes';

		add_action( 'admin_menu', array( $this, 'setup_theme_page' ), 99 );
		add_action( 'admin_init', array( $this, 'action_router' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_assets' ) );
	}
	/**
	 * Options page setup.
	 *
	 * @access public
	 */
	public function setup_theme_page() {
		add_submenu_page(
			'apple_news_index',
			__( 'Apple News Themes', 'apple-news' ),
			__( 'Themes', 'apple-news' ),
			apply_filters( 'apple_news_settings_capability', 'manage_options' ),
			$this->page_name,
			array( $this, 'page_themes_render' )
		);
	}

	/**
	 * Options page render.
	 *
	 * @access public
	 */
	public function page_themes_render() {
		if ( ! current_user_can( apply_filters( 'apple_news_settings_capability', 'manage_options' ) ) ) {
			wp_die( __( 'You do not have permissions to access this page.', 'apple-news' ) );
		}

		include plugin_dir_path( __FILE__ ) . 'partials/page_themes.php';
	}

	/**
	 * Register assets for the options page.
	 *
	 * @param string $hook
	 * @access public
	 */
	public function register_assets( $hook ) {
		if ( 'apple-news_page_apple-news-themes' != $hook ) {
			return;
		}

		wp_enqueue_style( 'apple-news-themes-css', plugin_dir_url( __FILE__ ) .
			'../assets/css/themes.css', array() );

		wp_enqueue_script( 'apple-news-themes-js', plugin_dir_url( __FILE__ ) .
			'../assets/js/themes.js', array( 'jquery' )
		);

		wp_localize_script( 'apple-news-themes-js', 'appleNewsThemes', array(
			'deleteWarning' => __( 'Are you sure you want to delete this theme?', 'apple-news' ),
		) );
	}

	/**
	 * Saves the theme JSON for the key provided.
	 *
	 * @param string $key
	 * @param string $json
	 * @access private
	 */
	private function save_theme( $key, $json ) {
		// Get the index
		$index = get_option( $this->theme_index_key, array() );
		if ( ! is_array( $index ) ) {
			$index = array();
		}

		// Attempt to save the JSON first just in case there is an issue
		$result = update_option( $this->theme_key_prefix . $key, $json );
		if ( false === $result ) {
			\Admin_Apple_Notice::error( sprintf(
				__( 'There was an error saving the theme %s', 'apple-news' ),
				$key
			) );
			return;
		}

		// Add the key to the index
		$index[] = $key;
		$result = update_option( $this->theme_index_key, $index );
		if ( false === $result ) {
			\Admin_Apple_Notice::error( sprintf(
				__( 'There was an error saving the theme index for %s', 'apple-news' ),
				$key
			) );

			// Avoid any unpleasant data reference issues
			delete_option( $this->theme_key_prefix );
		}

		\Admin_Apple_Notice::success( sprintf(
			__( 'The theme %s was saved successfully', 'apple-news' ),
			$key
		) );
	}

	/**
	 * Route all possible theme actions to the right place.
	 *
	 * @param string $hook
	 * @access public
	 */
	public function action_router() {
		// Check for a valid action
		$action	= isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : null;
		if ( ( empty( $action ) && ! array_key_exists( $action, $this->actions ) ) {
			return;
		}

		// Check the nonce
		check_admin_referer( 'apple_news_themes', 'apple_news_themes' );

		// Call the callback for the action for further processing
		call_user_func( $this->actions[ $action ] );
	}

	/**
	 * Handles creating a new theme from current settings.
	 *
	 * @access private
	 */
	private function create_theme() {

	}

	/**
	 * Handles setting the active theme.
	 *
	 * @access private
	 */
	private function set_theme() {

	}

	/**
	 * Handles deleting a theme.
	 *
	 * @access private
	 */
	private function delete_theme() {

	}

	/**
	 * Handles uploading a new theme from a JSON file.
	 *
	 * @access private
	 */
	private function upload_theme() {

	}
}
