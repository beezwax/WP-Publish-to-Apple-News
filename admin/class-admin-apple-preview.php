<?php
/**
 * This class is in charge of handling Apple News previews
 */
class Admin_Apple_Preview extends Apple_News {
	/**
	 * Constructor.
	 */
	function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'register_assets' ) );
	}

	/**
	 * Register assets for the options page.
	 *
	 * @param string $hook
	 * @access public
	 */
	public function register_assets( $hook ) {
		if ( ! in_array( $hook, array(
			'apple-news_page_apple-news-options',
			'apple-news_page_apple-news-theme-preview'
		) ) ) {
			return;
		}

		wp_enqueue_style( 'apple-news-preview-css', plugin_dir_url( __FILE__ ) .
		'../assets/css/preview.css', array() );

		wp_enqueue_script( 'apple-news-preview-js', plugin_dir_url( __FILE__ ) .
			'../assets/js/preview.js', array( 'jquery' )
		);
	}

	/**
	 * Outputs the HTML used for preview.
	 * Uses either current settings or a theme name, if provided.
	 *
	 * @param string $theme
	 * @access public
	 */
	public function get_preview_html( $theme = null ) {
		// Load current settings
		$admin_settings = new Admin_Apple_Settings();
		$settings = $admin_settings->fetch_settings();

		// If a theme name is provided, replace formatting settings with those from the theme.
		if ( ! empty( $theme ) ) {
			$themes = new Admin_Apple_Themes();
			$theme_settings = $themes->get_theme( $theme );
			if ( empty( $theme_settings ) || ! is_array( $theme_settings ) ) {
				?>
				<p class="error-message"><?php echo sprintf(
					__( 'The theme %s does not exist', 'apple-news' ),
					$theme
				) ?></p>
				<?php
				return;
			}

			// Replace all the formatting settings
			foreach ( $theme_settings as $key => $value ) {
				$settings->set( $key, $value );
			}
		}

		?>
		<div class="apple-news-settings-preview">
			<?php
				// Build sample content
				$title = sprintf(
					'<h1 class="apple-news-title apple-news-component apple-news-meta-component">%s</h1>',
					__( 'Sample Article', 'apple-news' )
				);

				$cover = sprintf(
					'<div class="apple-news-cover apple-news-meta-component">%s</div>',
					__( 'Cover', 'apple-news' )
				);

				// Build the byline
				$author = __( 'John Doe', 'apple-news' );
				$date = date( 'M j, Y g:i A' );
				$export = new Apple_Actions\Index\Export( $settings );
				$byline = sprintf(
					'<div class="apple-news-byline apple-news-component apple-news-meta-component">%s</div>',
					$export->format_byline( null, $author, $date )
				);

				// Get the order of the top components
				$component_order = $settings->get( 'meta_component_order' );
				foreach ( $component_order as $component ) {
					echo wp_kses( $$component, Admin_Apple_Settings_Section::$allowed_html );
				}
			?>
			<div class="apple-news-component">
			<p><span class="apple-news-dropcap">L</span>orem ipsum dolor sit amet, consectetur adipiscing elit. Mauris sagittis, <a href="#">augue vitae iaculis euismod</a>, libero nulla pellentesque quam, non venenatis massa odio id dolor.</p>
			<p>Praesent eget odio vel sapien scelerisque euismod. Phasellus eros sapien, rutrum ac nibh nec, tristique commodo neque.</p>
			<?php printf(
					'<div class="apple-news-image">%s</div>',
					esc_html__( 'Image', 'apple-news' )
				);
			?>
			<?php printf(
					'<div class="apple-news-image-caption">%s</div>',
					esc_html__( 'Image caption', 'apple-news' )
				);
			?>
			<p>Maecenas tortor dui, pellentesque ac ullamcorper quis, malesuada sit amet turpis. Nunc in tellus et justo dapibus sollicitudin.</p>
			<h2>Quisque efficitur</h2>
			<p>Quisque efficitur sit amet ex et venenatis. Morbi nisi nisi, ornare id iaculis eget, pulvinar ac dolor.</p>
			<blockquote>Blockquote lorem ipsum dolor sit amet, efficitur sit amet aliquet id, aliquam placerat turpis.</blockquote>
			<p>In eu la	cus porttitor, pellentesque diam et, tristique elit. Mauris justo odio, efficitur sit amet aliquet id, aliquam placerat turpis.</p>
			<div class="apple-news-pull-quote">Pull quote lorem ipsum dolor sit amet.</div>
			<p>Sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Pellentesque ipsum mi, sagittis eget sodales et, volutpat at felis.</p>
			<pre>
.code-sample {
font-family: monospace;
white-space: pre;
}
			</pre>
			</div>
		</div>
		<?php
	}
}
