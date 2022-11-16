<?php
/**
 * Publish to Apple News Admin: Automation class
 *
 * Contains a class which is used to manage Automation settings.
 *
 * @package Apple_News
 * @since 2.4.0
 */

namespace Apple_News\Admin;

/**
 * This class is in charge of handling the management of Apple News automation.
 *
 * @since 2.4.0
 */
class Automation {

	/**
	 * The field name for the automation settings screen.
	 */
	const FIELD_NAME = 'apple-news-automation-settings-field';

	/**
	 * An array of valid automation fields with information about data type and
	 * location within what is sent to Apple News.
	 */
	const FIELDS = [
		'isHidden'    => [
			'location' => 'article_metadata',
			'type'     => 'boolean',
		],
		'isPaid'      => [
			'location' => 'article_metadata',
			'type'     => 'boolean',
		],
		'isPreview'   => [
			'location' => 'article_metadata',
			'type'     => 'boolean',
		],
		'isSponsored' => [
			'location' => 'article_metadata',
			'type'     => 'boolean',
		],
	];

	/**
	 * The option name for automation.
	 */
	const OPTION_KEY = 'apple_news_automation';

	/**
	 * The page name for the automation settings screen.
	 */
	const PAGE_NAME = 'apple-news-automation';

	/**
	 * Initialize functionality of this class by registering hooks.
	 */
	public static function init(): void {
		add_action( 'init', [ __CLASS__, 'action__admin_init' ] );
		add_action( 'admin_menu', [ __CLASS__, 'action__admin_menu' ] );
		add_filter( 'apple_news_article_metadata', [ __CLASS__, 'filter__apple_news_article_metadata' ], 0, 2 );
	}

	/**
	 * A callback function for the admin_init action hook.
	 */
	public static function action__admin_init(): void {
		// New React Routine
		register_setting(
			self::PAGE_NAME,
			self::OPTION_KEY,
			[
				'default'           => [],
				'description'       => __( 'Automation settings for Publish to Apple News.', 'apple-news' ),
				'sanitize_callback' => [ __CLASS__, 'sanitize_setting' ],
				// Do we need a schema here?
				'show_in_rest'      => true,
				'type'              => 'array',
			]
		);
	}

	/**
	 * A callback function for the admin_menu action hook.
	 */
	public static function action__admin_menu(): void {
		add_submenu_page(
			'apple_news_index',
			__( 'Apple News Automation', 'apple-news' ),
			__( 'Automation', 'apple-news' ),
			/** This filter is documented in admin/class-admin-apple-settings.php */
			apply_filters( 'apple_news_settings_capability', 'manage_options' ),
			self::PAGE_NAME,
			[ __CLASS__, 'render_submenu_page' ]
		);

		add_action( "admin_print_scripts", [ __CLASS__, 'add_options_assets' ] );
	}

	/**
	 * A callback function for the apple_news_article_metadata filter.
	 *
	 * @param array $metadata An array of metadata keys and values.
	 * @param int   $post_id  The ID of the post being pushed to Apple News.
	 *
	 * @return array The modified array of metadata.
	 */
	public static function filter__apple_news_article_metadata( $metadata, $post_id ) {
		// Trim down the list of matched rules to only those affecting article metadata.
		$metadata_rules = array_values(
			array_filter(
				self::get_automation_for_post( $post_id ),
				function( $rule ) {
					return 'article_metadata' === self::FIELDS[ $rule['field'] ]['location'] ?? '';
				}
			)
		);

		// Loop through each matched rule and apply the value to metadata.
		foreach ( $metadata_rules as $rule ) {
			$metadata[ $rule['field'] ] = $rule['value'];
		}

		return $metadata;
	}

	/**
	 * Given a post ID, returns an array of matching automation rules.
	 *
	 * @param int $post_id The post ID to query.
	 *
	 * @return array An array of matching automation rules.
	 */
	public static function get_automation_for_post( int $post_id ): array {
		return array_values(
			array_filter(
				self::get_automation_rules(),
				function( $rule ) use ( $post_id ) {
					return has_term( $rule['term_id'] ?? '', $rule['taxonomy'] ?? '', $post_id );
				}
			)
		);
	}

	/**
	 * Returns an array of automation rules defined in the database.
	 *
	 * @return array An array of automation rules.
	 */
	public static function get_automation_rules(): array {
		/**
		 * Allow the automation rules to be filtered and set via code.
		 *
		 * @since 2.4.0
		 *
		 * @param array $rules An array of automation rules.
		 */
		return apply_filters( 'apple_news_automation_rules', get_option( self::OPTION_KEY, [] ) );
	}

	/**
	 * A render callback for the REACT submenu page.
	 */
	public static function render_submenu_page(): void {
		add_filter(
			'should_load_block_editor_scripts_and_styles',
			function() {
				return true;
			}
		);
		wp_add_iframed_editor_assets_html();
		echo wp_kses(
			'<div class="block-editor"><div class="editor-styles-wrapper"><div id="apple-news-options__page"></div></div></div>',
			[ 'div' => [ 'id' => [] ] ]
		);
	}

	public static function add_options_assets(): void {
		wp_enqueue_script(
			'apple-news-plugin-admin-settings',
			plugins_url( 'build/adminSettings.js', __DIR__ ),
			[ 'wp-block-editor', 'wp-api-fetch', 'wp-api', 'wp-i18n', 'wp-components', 'wp-element', 'wp-tinymce' ],
			[],
			true
		);
		wp_enqueue_style( 'wp-edit-blocks' );
		// Was included in BASS logic, method not defined here.
		// Leaving for now in case it's important and needs to be done differently here.
		// inline_locale_data( 'apple-news-plugin-admin-settings' );

		wp_localize_script(
			'apple-news-plugin-admin-settings',
			'wpLocalizedData',
			[
				'taxonomies' => get_taxonomies( [ 'public' => 'true' ] ),
				'fields'     => self::FIELDS,
			]
		);
	}

	/**
	 * Sanitizes the field value.
	 *
	 * @param array $value An array containing the unsanitized setting value.
	 *
	 * @return array An array containing the sanitized setting value.
	 */
	public static function sanitize_setting( $value ) {
		return $value;
	}
}
