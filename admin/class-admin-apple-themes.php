<?php
/**
 * This class is in charge of handling the management of Apple News themes.
 */
class Admin_Apple_Themes extends Apple_News {

	/**
	 * Settings page name.
	 *
	 * @var string
	 * @access private
	 */
	private $page_name;

	/**
	 * Constructor.
	 */
	function __construct() {
		$this->page_name = $this->plugin_domain . '-themes';

		add_action( 'admin_menu', array( $this, 'setup_theme_page' ), 99 );
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
}
