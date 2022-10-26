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
		add_action( 'admin_init', [ __CLASS__, 'action__admin_init' ] );
		add_action( 'admin_menu', [ __CLASS__, 'action__admin_menu' ] );
	}

	/**
	 * A callback function for the admin_init action hook.
	 */
	public static function action__admin_init(): void {
		add_settings_section(
			self::PAGE_NAME,
			esc_html__( 'Configuration', 'apple-news' ),
			'__return_null',
			self::PAGE_NAME
		);
		add_settings_field(
			self::FIELD_NAME,
			esc_html__( 'By Taxonomy Term', 'apple-news' ),
			[ __CLASS__, 'render_settings_field' ],
			self::PAGE_NAME,
			self::PAGE_NAME
		);
		register_setting(
			self::PAGE_NAME,
			self::OPTION_KEY,
			[
				'default'           => [],
				'description'       => __( 'Automation settings for Publish to Apple News.', 'apple-news' ),
				'sanitize_callback' => [ __CLASS__, 'sanitize_setting' ],
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
	}

	/**
	 * A render callback for the automation settings field.
	 */
	public static function render_settings_field(): void {
		$value = get_option( self::OPTION_KEY );
		// TODO: Dynamically get list of registered taxonomies.
		?>
			<fieldset>
				<legend class="screen-reader-text">
					<?php esc_html_e( 'Configuration', 'apple-news' ); ?> 1
				</legend>
				<div>
					<label>
						<?php esc_html_e( 'Taxonomy', 'apple-news' ); ?>
						<select name="<?php echo esc_attr( self::OPTION_KEY ); ?>[0][taxonomy]">
							<option value=""></option>
							<option value="category" <?php selected( 'category' === ( $value[0]['taxonomy'] ?? '' ) ); ?>><?php esc_html_e( 'Category', 'apple-news' ); ?></option>
							<option value="post_tag" <?php selected( 'post_tag' === ( $value[0]['taxonomy'] ?? '' ) ); ?>><?php esc_html_e( 'Tag', 'apple-news' ); ?></option>
						</select>
					</label>
				</div>
				<div>
					<label>
						<?php esc_html_e( 'Term ID', 'apple-news' ); ?>
						<input name="<?php echo esc_attr( self::OPTION_KEY ); ?>[0][term_id]" type="number" value="<?php echo esc_attr( $value[0]['term_id'] ?? '' ); ?>" />
					</label>
				</div>
				<div>
					<label>
						<?php esc_html_e( 'Field', 'apple-news' ); ?>
						<select name="<?php echo esc_attr( self::OPTION_KEY ); ?>[0][field]">
							<option value=""></option>
							<option value="isHidden" <?php selected( 'isHidden' === ( $value[0]['field'] ?? '' ) ); ?>>isHidden</option>
							<option value="isPaid" <?php selected( 'isPaid' === ( $value[0]['field'] ?? '' ) ); ?>>isPaid</option>
							<option value="isPreview" <?php selected( 'isPreview' === ( $value[0]['field'] ?? '' ) ); ?>>isPreview</option>
							<option value="isSponsored" <?php selected( 'isSponsored' === ( $value[0]['field'] ?? '' ) ); ?>>isSponsored</option>
						</select>
					</label>
				</div>
				<div>
					<label>
						<?php esc_html_e( 'Field Value', 'apple-news' ); ?>
						<input name="<?php echo esc_attr( self::OPTION_KEY ); ?>[0][value]" type="text" value="<?php echo esc_attr( $value[0]['value'] ?? '' ); ?>" />
					</label>
				</div>
			</fieldset>
		<?php
	}

	/**
	 * A render callback for the submenu page.
	 */
	public static function render_submenu_page(): void {
		?>
			<div class="wrap apple-news-settings">
				<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
				<?php settings_errors(); ?>
				<form method="post" action="options.php" id="apple-news-automation">
					<?php settings_fields( 'apple-news-automation' ); ?>
					<?php do_settings_sections( 'apple-news-automation' ); ?>
					<?php submit_button(); ?>
				</form>
			</div>
		<?php
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
