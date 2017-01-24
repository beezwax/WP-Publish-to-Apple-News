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
			array( $this, 'page_options_render' )
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

		//include plugin_dir_path( __FILE__ ) . 'partials/page_options.php';
	}
}
