<?php
/**
 * Publish to Apple News tests: Apple_News_Testcase class
 *
 * @package Apple_News
 * @subpackage Tests
 */

/**
 * A base class for Apple News tests.
 *
 * @package Apple_News
 */
abstract class Apple_News_Testcase extends WP_UnitTestCase {

	/**
	 * Contains an instance of the Apple_Exporter\Builders\Component_Styles class for use in tests.
	 *
	 * @var Apple_Exporter\Builders\Component_Styles
	 */
	protected $component_styles;

	/**
	 * Contains an instance of the Apple_Exporter\Exporter_Content class for use in tests.
	 *
	 * @var Apple_Exporter\Exporter_Content
	 */
	protected $content;

	/**
	 * Contains an instance of the Apple_Exporter\Exporter_Content_Settings class for use in tests.
	 *
	 * @var Apple_Exporter\Exporter_Content_Settings
	 */
	protected $content_settings;

	/**
	 * Contains an instance of the Apple_Exporter\Builders\Component_Layouts class for use in tests.
	 *
	 * @var Apple_Exporter\Builders\Component_Layouts
	 */
	protected $layouts;

	/**
	 * Contains a Prophecy-wrapped instance of the Apple_Exporter\Workspace class.
	 *
	 * @var Prophecy\Prophecy\ObjectProphecy
	 */
	protected $prophecized_workspace;

	/**
	 * An instance of Prophet for use in tests.
	 *
	 * @var Prophecy\Prophet
	 */
	protected $prophet;

	/**
	 * Contains an instance of the Apple_Exporter\Settings class for use in tests.
	 *
	 * @var Apple_Exporter\Settings
	 */
	protected $settings;

	/**
	 * Contains an instance of the Apple_Exporter\Builders\Component_Text_Styles class for use in tests.
	 *
	 * @var Apple_Exporter\Builders\Component_Text_Styles
	 */
	protected $styles;

	/**
	 * Contains an instance of the Apple_Exporter\Theme class for use in tests.
	 *
	 * @var Apple_Exporter\Theme
	 */
	protected $theme;

	/**
	 * Contains an instance of the Apple_Exporter\Workspace class for use in tests.
	 *
	 * @var Apple_Exporter\Workspace
	 */
	protected $workspace;

	/**
	 * A function containing operations to be run before each test function.
	 *
	 * @access public
	 */
	public function setUp() {
		parent::setUp();

		// Ensure HTML5 image captions are supported.
		add_theme_support( 'html5', ['caption'] );

		// Create some dummy content and save it for future use.
		$this->content = new Apple_Exporter\Exporter_Content(
			1,
			'My Title',
			'<p>Hello, World!</p>'
		);

		// Create a new instance of the Settings object and save it for future use.
		$this->settings = new Apple_Exporter\Settings();

		// Create a new instance of the Exporter_Content_Settings object and save it for future use.
		$this->content_settings = new Apple_Exporter\Exporter_Content_Settings();

		// Create a new instance of Prophet for future use and create a prophecized workspace.
		$this->prophet               = new Prophecy\Prophet();
		$this->prophecized_workspace = $this->prophet->prophesize( '\Apple_Exporter\Workspace' );

		// Load the Default theme from config and save it for future use.
		$this->load_example_theme( 'default' );

		// Create styles for future use.
		$this->styles = new Apple_Exporter\Builders\Component_Text_Styles(
			$this->content,
			$this->content_settings
		);

		// Create layouts for future use.
		$this->layouts = new Apple_Exporter\Builders\Component_Layouts(
			$this->content,
			$this->content_settings
		);

		// Create component styles for future use.
		$this->component_styles = new Apple_Exporter\Builders\Component_Styles(
			$this->content,
			$this->content_settings
		);

		// Create a workspace for future use. Default it to use post ID 1, but this can be overridden at the test level.
		$this->set_workspace_post_id( 1 );
	}

	/**
	 * Actions to be run after every test.
	 *
	 * @access public
	 */
	public function tearDown() {
		$this->prophet->checkPredictions();
	}

	/**
	 * Runs create_upload_object using a test image and returns the image ID.
	 *
	 * @param int    $parent  Optional. The parent post ID. Defaults to no parent.
	 * @param string $caption Optional. The caption to set on the image.
	 *
	 * @return int The post ID of the attachment image that was created.
	 */
	protected function get_new_attachment( $parent = 0, $caption = '' ) {
		$image_id = self::factory()->attachment->create_upload_object( __DIR__ . '/data/test-image.jpg', $parent );

		if ( ! empty( $caption ) ) {
			$image = get_post( $image_id );
			$image->post_excerpt = $caption;
			wp_update_post( $image );
		}

		return $image_id;
	}

	/**
	 * Given an image ID, returns the HTML5 markup for an image with a caption.
	 *
	 * Extracts the caption from the database entry for the image (stored in post_excerpt).
	 *
	 * @param int $image_id The image ID to use when generating the <figure>.
	 *
	 * @return string HTML for the image and the caption.
	 */
	protected function get_image_with_caption( $image_id ) {
		return img_caption_shortcode(
			[
				'caption' => wp_get_attachment_caption( $image_id ),
				'width'   => 640,
			],
			wp_get_attachment_image( $image_id, 'full' )
		);
	}

	/**
	 * A helper function that generates JSON for a given post ID.
	 *
	 * @param int $post_id The for which to perform the export.
	 *
	 * @return array The JSON for the post, converted to an associative array.
	 */
	protected function get_json_for_post( $post_id ) {
		$export = new Apple_Actions\Index\Export(
			$this->settings,
			$post_id,
			Admin_Apple_Sections::get_sections_for_post( $post_id )
		);

		return json_decode( $export->perform(), true );
	}

	/**
	 * Loads an example theme given a slug.
	 *
	 * @param string $slug The slug of the example theme to load.
	 */
	protected function load_example_theme( $slug ) {
		// Load the theme data from the JSON configuration file.
		$options = json_decode( file_get_contents( dirname( __DIR__ ) . '/assets/themes/' . $slug . '.json' ), true );
		if ( empty( $options ) ) {
			return;
		}

		// Negotiate screenshot URL.
		$options['screenshot_url'] = plugins_url(
			'/assets/screenshots/' . $slug . '.png',
			__DIR__
		);

		// Create a new instance of the Theme class and set the theme name.
		$this->theme = new \Apple_Exporter\Theme();
		$this->theme->set_name( $options['theme_name'] );

		// Save the theme.
		$this->theme->load( $options );
		$this->theme->save();

		// Make this theme the active theme.
		$this->theme->set_active();
	}

	/**
	 * Given an array of theme settings, applies them to the currently active theme.
	 *
	 * @param array $settings The settings to apply to the theme.
	 */
	protected function set_theme_settings( $settings ) {
		$settings = wp_parse_args( $settings, $this->theme->all_settings() );
		$this->theme->load( $settings );
		$this->theme->save();
	}

	/**
	 * Sets the workspace post ID to the ID provided.
	 *
	 * @param int $post_id The post ID to set for the workspace.
	 */
	protected function set_workspace_post_id( $post_id ) {
		$this->workspace = new Apple_Exporter\Workspace( $post_id );
	}
}
