<?php

/**
 * This class provides a meta box to publish posts to Apple News from the edit screen.
 *
 * @since 0.9.0
 */
class Admin_Apple_Meta_Boxes extends Apple_Export {

	/**
	 * Current settings.
	 *
	 * @since 0.9.0
	 * @var array
	 * @access private
	 */
	private $settings;

	/**
	 * Publish action.
	 *
	 * @since 0.9.0
	 * @var array
	 * @access private
	 */
	private $publish_action = 'apple_news_publish';

	/**
	 * Constructor.
	 */
	function __construct( $settings = null ) {
		$this->settings = $settings;

		// Register hooks if enabled
		if ( 'yes' == $settings->get( 'show_metabox' ) ) {
			// Handle a publish action on save.
			// However, if auto sync is enabled, don't bother.
			if ( 'yes' != $settings->get( 'api_autosync' ) ) {
				add_action( 'save_post', array( $this, 'do_publish' ), 10, 2 );
			}

			// Add the custom meta boxes to each post type
			$post_types = $settings->get( 'post_types' );
			if ( ! is_array( $post_types ) ) {
				$post_types = array( $post_types );
			}

			foreach ( $post_types as $post_type ) {
				add_action( 'add_meta_boxes_' . $post_type, array( $this, 'add_meta_boxes' ) );
			}

			// Register assets used by the meta box
			add_action( 'admin_enqueue_scripts', array( $this, 'register_assets' ) );
		}
	}

	/**
	 * Check for a publish action from the meta box.
	 *
	 * @since 0.9.0
	 * @param int $post_id
	 * @param WP_Post $post
	 * @access public
	 */
	public function do_publish( $post_id, $post ) {
		// Check if the values we want are present in $_REQUEST params.
		if ( empty( $_POST['apple_news_publish_action'] )
			|| empty( $_POST['apple_news_publish_nonce'] )
			|| empty( $_POST['post_ID'] ) ) {
			return;
		}

		// Check the nonce
		if ( ! wp_verify_nonce( $_POST['apple_news_publish_nonce'], $this->publish_action ) ) {
			return;
		}

		// Do the publish
		$post_sync = new Admin_Apple_Post_Sync( $this->settings );
		$post_sync->do_publish( $post_id, $post );
	}

	/**
	 * Add the Apple News meta boxes
	 *
	 * @since 0.9.0
	 * @param int $post_id
	 * @param WP_Post $post
	 * @access public
	 */
	public function add_meta_boxes( $post_id, $post ) {
		// Only add if this post is not an auto-draft
		if ( 'auto-draft' == $post->post_status ) {
			return;
		}

		// Add the publish meta box
		add_meta_box(
			'apple_news_publish',
			__( 'Apple News', 'apple-news' ),
			array( $this, 'publish_meta_box' ),
			$post->post_type,
			apply_filters( 'apple_news_publish_meta_box_context', 'side' ),
			apply_filters( 'apple_news_publish_meta_box_priority', 'high' )
		);
	}

	/**
	 * Add the Apple News publish meta box
	 *
	 * @since 0.9.0
	 * @param WP_Post $post
	 * @access public
	 */
	public function publish_meta_box( $post_type, $post ) {
		// Only show the publish feature if the user is authorized and auto sync is not enabled.
		if ( 'yes' != $settings->get( 'api_autosync' ) ):
		?>
		<p><?php esc_html_e( 'Click the button below to publish this article to Apple News', 'apple-news' ); ?></p>
		<div id="apple-news-publish">
		<input type="hidden" id="apple-news-publish-action" name="apple_news_publish_action" value="">
		<input type="hidden" id="apple-news-publish-nonce" name="apple_news_publish_nonce" value="<?php echo esc_attr( wp_create_nonce( $this->publish_action ) ) ?>" >
		<input type="button" id="apple-news-publish-submit" name="apple_news_publish_submit" value="<?php esc_attr_e( 'Publish to Apple News', 'apple-news' ) ?>" class="button-primary" />
		</div>
		<?php
		endif;
	}

	/**
	 * Registers assets used by meta boxes.
	 *
	 * @access public
	 */
	public function register_assets() {
		wp_enqueue_script( $this->plugin_slug . '_meta_boxes_js', plugin_dir_url(
			__FILE__ ) .  '../assets/js/meta-boxes.js', array( 'jquery' ),
			$this->version, true );

		// Localize the JS file for handling frontend actions.
		wp_localize_script( $this->plugin_slug . '_meta_boxes_js', 'apple_news_meta_boxes', array(
			'publish_action' => $this->publish_action,
		) );

	}

}
